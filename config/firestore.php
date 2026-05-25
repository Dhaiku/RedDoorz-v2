<?php
/**
 * Firestore REST client — replaces MySQL entirely.
 *
 * Uses the Firestore REST API over HTTPS (no ext-grpc needed).
 * Bearer token obtained from kreait Firebase Factory (uses ext-openssl + ext-curl).
 *
 * Collections mirror the old MySQL tables:
 *   accounts / customers / hotels / rooms / bookings / payments
 *   earnings / reviews / hotelstaff / fcmtokens / payoutrequests
 *   blockeddates / ownerapplications / counters
 */

// Firestore calls are network I/O — remove PHP's execution time limit so a
// slow cold request or cache miss never triggers "Maximum execution time exceeded".
// Individual curl calls are bounded by CURLOPT_TIMEOUT instead.
set_time_limit(0);

require_once __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;

// ── Cache layer ───────────────────────────────────────────────────────────────
//
// Two-level cache:
//   1. Memory (static array)  — 0ms, lives for current request only
//   2. Disk (JSON files)      — ~1ms, survives across page loads
//
// TTLs are long because writes always bust the relevant collection instantly.
// A 1-in-50 chance cleanup runs on each request to delete expired files.

define('_FS_CACHE_DIR', __DIR__ . '/../cache/');

// TTL in seconds — kept long because writes immediately bust the cache
$_FS_CACHE_TTL = [
    'hotels'            => 600,   // 10 min
    'rooms'             => 300,   // 5 min
    'bookings'          => 120,   // 2 min
    'payments'          => 120,
    'earnings'          => 300,
    'customers'         => 600,
    'accounts'          => 600,
    'reviews'           => 600,
    'hotelstaff'        => 600,
    'fcmtokens'         => 120,
    'payoutrequests'    => 120,
    'blockeddates'      => 300,
    'ownerapplications' => 120,
    'counters'          => 0,     // never cache — always live
];

/** Memory store — shared across all calls in one PHP request */
function &_fs_mem_cache(): array {
    static $store = [];
    return $store;
}

function _fs_cache_key(string $col, string $op, array $params): string {
    return $col . '__' . $op . '__' . md5(serialize($params));
}

function _fs_cache_get(string $col, string $key, bool $allowStale = false) {
    global $_FS_CACHE_TTL;

    // 1. Memory — fastest path
    $mem = &_fs_mem_cache();
    if (array_key_exists($key, $mem)) return $mem[$key];

    // 2. Disk
    $ttl = $_FS_CACHE_TTL[$col] ?? 60;
    if ($ttl <= 0) return null;

    $file = _FS_CACHE_DIR . $key . '.json';
    if (!file_exists($file)) return null;

    $age = time() - filemtime($file);
    if ($age > $ttl && !$allowStale) return null;

    $raw  = file_get_contents($file);
    $data = $raw !== false ? json_decode($raw, true) : null;
    if ($data === null) return null;

    $mem[$key] = $data;   // promote to memory
    return $data;
}

function _fs_cache_set(string $col, string $key, $value): void {
    global $_FS_CACHE_TTL;

    $mem = &_fs_mem_cache();
    $mem[$key] = $value;

    $ttl = $_FS_CACHE_TTL[$col] ?? 60;
    if ($ttl <= 0) return;

    if (!is_dir(_FS_CACHE_DIR)) @mkdir(_FS_CACHE_DIR, 0755, true);
    @file_put_contents(_FS_CACHE_DIR . $key . '.json', json_encode($value), LOCK_EX);

    // 2% chance: sweep expired files so the cache dir stays lean
    if (mt_rand(1, 50) === 1) _fs_cache_sweep();
}

/** Delete expired cache files. Fast — only unlinks files past their TTL. */
function _fs_cache_sweep(): void {
    global $_FS_CACHE_TTL;
    if (!is_dir(_FS_CACHE_DIR)) return;
    $now = time();
    foreach (glob(_FS_CACHE_DIR . '*.json') as $f) {
        $base = basename($f, '.json');
        // derive collection name from filename prefix (everything before __)
        $col = strstr($base, '__', true) ?: '';
        $ttl = $_FS_CACHE_TTL[$col] ?? 60;
        if ($ttl > 0 && ($now - filemtime($f)) > $ttl * 2) {
            @unlink($f);
        }
    }
}

/** Bust all cached entries for a collection (called on every write). */
function _fs_cache_bust(string $col): void {
    // Clear memory
    $mem = &_fs_mem_cache();
    foreach (array_keys($mem) as $k) {
        if (strncmp($k, $col . '__', strlen($col) + 2) === 0) unset($mem[$k]);
    }
    // Delete disk files — use a collection-specific index file to avoid
    // scanning the whole directory with glob() every time
    if (!is_dir(_FS_CACHE_DIR)) return;
    foreach (glob(_FS_CACHE_DIR . $col . '__*.json') as $f) {
        @unlink($f);
    }
}

// ── Bootstrap: obtain a short-lived OAuth2 access token ──────────────────────

$_FS_KEY_PATH = getenv('FIREBASE_KEY_PATH')
    ?: 'C:/Users/bever/Downloads/reddoorz-8f605-firebase-adminsdk-fbsvc-bb9a5b8ce6.json';

$_FS_PROJECT_ID = 'reddoorz-8f605';
$_FS_BASE_URL   = "https://firestore.googleapis.com/v1/projects/{$_FS_PROJECT_ID}/databases/(default)/documents";

/** Return (and cache) a valid Bearer token.
 *  Three-level cache: static var → disk file → Google OAuth endpoint.
 *  The disk file means even the first call in a new PHP process is ~0ms
 *  as long as the token hasn't expired (tokens last 1 hour).
 */
function _fs_token(): string {
    static $cache = null;
    static $exp   = 0;
    if ($cache && time() < $exp - 30) return $cache;

    // Check disk token cache (lives up to 55 min)
    $tokenFile = _FS_CACHE_DIR . '__oauth_token.json';
    if (!is_dir(_FS_CACHE_DIR)) @mkdir(_FS_CACHE_DIR, 0755, true);
    if (file_exists($tokenFile)) {
        $tok = json_decode(file_get_contents($tokenFile), true);
        if ($tok && !empty($tok['access_token']) && time() < $tok['exp'] - 30) {
            $cache = $tok['access_token'];
            $exp   = $tok['exp'];
            return $cache;
        }
    }

    global $_FS_KEY_PATH;

    // Build a signed JWT for the service account scopes
    $key   = json_decode(file_get_contents($_FS_KEY_PATH), true);
    $now   = time();
    $claim = [
        'iss'   => $key['client_email'],
        'sub'   => $key['client_email'],
        'aud'   => 'https://oauth2.googleapis.com/token',
        'iat'   => $now,
        'exp'   => $now + 3600,
        'scope' => 'https://www.googleapis.com/auth/datastore',
    ];

    $header  = base64url_encode(json_encode(['alg'=>'RS256','typ'=>'JWT']));
    $payload = base64url_encode(json_encode($claim));
    $toSign  = "$header.$payload";

    $pkeyId  = openssl_pkey_get_private($key['private_key']);
    openssl_sign($toSign, $sig, $pkeyId, OPENSSL_ALGO_SHA256);
    $jwt = $toSign . '.' . base64url_encode($sig);

    $resp = _fs_http_post('https://oauth2.googleapis.com/token', http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion'  => $jwt,
    ]), 'application/x-www-form-urlencoded');

    if (empty($resp['access_token'])) {
        // If token fetch failed but we have a (possibly expired) disk token, use it
        // rather than hard-crashing the page — Firestore may still accept it briefly
        if (file_exists($tokenFile)) {
            $tok = json_decode(file_get_contents($tokenFile), true);
            if ($tok && !empty($tok['access_token'])) {
                error_log('Firestore: token refresh failed, using stale token. Error: ' . json_encode($resp));
                $cache = $tok['access_token'];
                $exp   = $tok['exp'] ?? (time() + 300);
                return $cache;
            }
        }
        throw new RuntimeException('Firestore: could not get access token: ' . json_encode($resp));
    }

    $cache = $resp['access_token'];
    $exp   = $now + ($resp['expires_in'] ?? 3600);

    // Persist token to disk so next PHP process skips this round-trip
    @file_put_contents($tokenFile, json_encode([
        'access_token' => $cache,
        'exp'          => $exp,
    ]), LOCK_EX);

    return $cache;
}

function base64url_encode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

// ── Low-level HTTP helpers ────────────────────────────────────────────────────

// ── Shared curl handle (connection reuse / keep-alive) ────────────────────────
//
// A single curl handle reused across all _fs_req() calls in one PHP request
// means only ONE TLS handshake per page load (~400 ms saved per extra call).
// PHP frees the handle automatically at request end.

/** Return (or create) the shared persistent curl handle for Firestore calls. */
function &_fs_curl_handle() {
    static $ch = null;
    if ($ch === null) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_TCP_KEEPALIVE  => 1,       // keep TCP connection alive
            CURLOPT_TCP_KEEPIDLE   => 30,      // send keepalive after 30s idle
            CURLOPT_ENCODING       => 'gzip',  // accept gzip to cut bandwidth
            // HTTP/2 where available — falls back to HTTP/1.1 automatically
            CURLOPT_HTTP_VERSION   => defined('CURL_HTTP_VERSION_2TLS')
                                      ? CURL_HTTP_VERSION_2TLS
                                      : CURLOPT_HTTP_VERSION,
        ]);
    }
    return $ch;
}

function _fs_http_post(string $url, $body, string $ct = 'application/json'): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => is_array($body) ? json_encode($body) : $body,
        CURLOPT_HTTPHEADER     => ["Content-Type: $ct"],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 8,
    ]);
    $out = curl_exec($ch);
    curl_close($ch);
    return json_decode($out ?: '{}', true) ?: [];
}

function _fs_req(string $method, string $url, array $body = []): array {
    $tok = _fs_token();
    $ch  = &_fs_curl_handle();

    $opts = [
        CURLOPT_URL           => $url,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER    => [
            "Authorization: Bearer $tok",
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS    => $body ? json_encode($body) : null,
    ];
    curl_setopt_array($ch, $opts);

    $out   = curl_exec($ch);
    $errno = curl_errno($ch);
    $code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Reset body for next call (shared handle must not leak POST body)
    curl_setopt($ch, CURLOPT_POSTFIELDS, null);

    // Network error (timeout, no connection, etc.) — return sentinel
    if ($errno !== 0) {
        error_log("Firestore curl error ($errno) on $method $url");
        return ['_curl_error' => $errno];
    }

    $decoded = json_decode($out ?: '{}', true) ?: [];
    if ($code >= 400 && !empty($decoded['error'])) {
        if ($code === 404) return [];
        throw new RuntimeException("Firestore REST error $code: " . json_encode($decoded['error']));
    }
    return $decoded;
}

// ── Value encoding / decoding ─────────────────────────────────────────────────

/** Encode a PHP value into a Firestore Value object. */
function _fs_encode($v): array {
    if ($v === null)               return ['nullValue'    => null];
    if (is_bool($v))               return ['booleanValue' => $v];
    if (is_int($v))                return ['integerValue'  => (string)$v];
    if (is_float($v))              return ['doubleValue'   => $v];
    if ($v instanceof DateTimeInterface) return ['timestampValue' => $v->format(DateTime::RFC3339_EXTENDED)];
    if (is_array($v) && array_keys($v) !== range(0, count($v)-1)) {
        // associative → mapValue
        $fields = [];
        foreach ($v as $k => $val) $fields[$k] = _fs_encode($val);
        return ['mapValue' => ['fields' => $fields]];
    }
    if (is_array($v)) {
        return ['arrayValue' => ['values' => array_map('_fs_encode', $v)]];
    }
    return ['stringValue' => (string)$v];
}

/** Decode a Firestore Value object back to a PHP value. */
function _fs_decode(array $val) {
    if (array_key_exists('nullValue',     $val)) return null;
    if (array_key_exists('booleanValue',  $val)) return (bool)$val['booleanValue'];
    if (array_key_exists('integerValue',  $val)) return (int)$val['integerValue'];
    if (array_key_exists('doubleValue',   $val)) return (float)$val['doubleValue'];
    if (array_key_exists('stringValue',   $val)) return $val['stringValue'];
    if (array_key_exists('timestampValue',$val)) return $val['timestampValue'];
    if (array_key_exists('arrayValue',    $val)) {
        return array_map('_fs_decode', $val['arrayValue']['values'] ?? []);
    }
    if (array_key_exists('mapValue', $val)) {
        $out = [];
        foreach ($val['mapValue']['fields'] ?? [] as $k => $fv) $out[$k] = _fs_decode($fv);
        return $out;
    }
    return null;
}

/** Convert a raw Firestore REST document to a plain PHP array. */
function _fs_doc_to_array(array $doc): ?array {
    if (empty($doc['name'])) return null;
    // Extract the document ID from the resource name
    $parts = explode('/', $doc['name']);
    $id    = end($parts);
    $out   = ['id' => (int)$id];
    foreach ($doc['fields'] ?? [] as $k => $v) {
        $out[$k] = _fs_decode($v);
    }
    return $out;
}

/** Build a Firestore document body from a PHP array. */
function _fs_array_to_doc(array $data): array {
    $fields = [];
    foreach ($data as $k => $v) {
        $fields[$k] = _fs_encode($v);
    }
    return ['fields' => $fields];
}

// ── URL helpers ───────────────────────────────────────────────────────────────

function _fs_doc_url(string $col, string $id): string {
    global $_FS_BASE_URL;
    return "{$_FS_BASE_URL}/{$col}/{$id}";
}

function _fs_col_url(string $col): string {
    global $_FS_BASE_URL;
    return "{$_FS_BASE_URL}/{$col}";
}

function _fs_query_url(): string {
    global $_FS_BASE_URL;
    // runQuery endpoint is at the documents level: .../documents:runQuery
    return $_FS_BASE_URL . ':runQuery';
}

// ── Structured query builder ──────────────────────────────────────────────────

function _fs_op(string $op): string {
    return match($op) {
        '='  , '==' => 'EQUAL',
        '!=' , '<>' => 'NOT_EQUAL',
        '<'         => 'LESS_THAN',
        '<='        => 'LESS_THAN_OR_EQUAL',
        '>'         => 'GREATER_THAN',
        '>='        => 'GREATER_THAN_OR_EQUAL',
        'in'        => 'IN',
        'array-contains' => 'ARRAY_CONTAINS',
        default     => strtoupper($op),
    };
}

/**
 * Run a structured query.
 * $wheres  = [[$field, $op, $value], ...]
 * $orderBy = [[$field, 'asc'|'desc'], ...]
 *
 * When both $wheres and $orderBy are present Firestore requires a composite
 * index for every combination.  To avoid that maintenance burden we send the
 * orderBy to Firestore only when there are NO where-filters; otherwise we
 * fetch all matching docs and sort them in PHP.  The $limit is applied after
 * the PHP sort so it is still respected.
 */
function _fs_run_query(string $col, array $wheres = [], array $orderBy = [], int $limit = 0): array {
    $filters = [];
    foreach ($wheres as [$f, $op, $v]) {
        $filters[] = ['fieldFilter' => [
            'field'  => ['fieldPath' => $f],
            'op'     => _fs_op($op),
            'value'  => _fs_encode($v),
        ]];
    }

    $where = match(count($filters)) {
        0 => null,
        1 => $filters[0],
        default => ['compositeFilter' => ['op' => 'AND', 'filters' => $filters]],
    };

    // Push orderBy to Firestore only when there is no where-clause (avoids
    // composite-index requirement).  With a where-clause we sort in PHP below.
    $fsOrderBy   = ($where === null) ? $orderBy : [];
    $fsLimit     = ($where === null) ? $limit   : 0; // apply limit after PHP sort

    $structured = ['from' => [['collectionId' => $col]]];
    if ($where)  $structured['where'] = $where;
    foreach ($fsOrderBy as [$f, $dir]) {
        $structured['orderBy'][] = [
            'field'     => ['fieldPath' => $f],
            'direction' => strtoupper($dir) === 'DESC' ? 'DESCENDING' : 'ASCENDING',
        ];
    }
    if ($fsLimit > 0) $structured['limit'] = $fsLimit;

    // Check cache before hitting the network
    $cacheKey    = _fs_cache_key($col, 'q', [$wheres, $orderBy, $limit]);
    $cachedDocs  = _fs_cache_get($col, $cacheKey);
    if ($cachedDocs !== null) return $cachedDocs;

    $url  = _fs_query_url();
    $resp = _fs_req('POST', $url, ['structuredQuery' => $structured]);

    // Curl timed out — serve stale cache if available, else empty array
    if (!empty($resp['_curl_error'])) {
        return _fs_cache_get($col, $cacheKey, true) ?? [];
    }

    $docs = [];
    foreach ((array)$resp as $item) {
        if (!empty($item['document'])) {
            $arr = _fs_doc_to_array($item['document']);
            if ($arr !== null) $docs[] = $arr;
        }
    }

    // PHP-side sort when we have a where-clause
    if ($where !== null && !empty($orderBy)) {
        usort($docs, function($a, $b) use ($orderBy) {
            foreach ($orderBy as [$field, $dir]) {
                $av = $a[$field] ?? null;
                $bv = $b[$field] ?? null;
                if ($av === $bv) continue;
                $cmp = (is_numeric($av) && is_numeric($bv))
                    ? ($av <=> $bv)
                    : strcmp((string)$av, (string)$bv);
                if (strtoupper($dir) === 'DESC') $cmp = -$cmp;
                return $cmp;
            }
            return 0;
        });
    }

    // Apply limit after PHP sort
    if ($limit > 0 && count($docs) > $limit) {
        $docs = array_slice($docs, 0, $limit);
    }

    _fs_cache_set($col, $cacheKey, $docs);
    return $docs;
}

// ── ID counter (replaces AUTO_INCREMENT) ─────────────────────────────────────

/**
 * Get the next auto-increment ID for a collection.
 * Uses a Firestore transaction-like read-modify-write on counters/{col}.
 */
function fs_next_id(string $collection): int {
    $url  = _fs_doc_url('counters', $collection);
    $resp = _fs_req('GET', $url);

    $current = 1;
    if (!empty($resp['fields']['next'])) {
        $current = (int)_fs_decode($resp['fields']['next']);
    }

    // Write next+1 back
    $newDoc = _fs_array_to_doc(['next' => $current + 1]);
    _fs_req('PATCH', $url, $newDoc);

    return $current;
}

// ── Public API ────────────────────────────────────────────────────────────────

/** Get one document by its integer ID. Returns array or null. */
function fs_get(string $col, int $id): ?array {
    $key    = _fs_cache_key($col, 'get', [$id]);
    $cached = _fs_cache_get($col, $key);
    if ($cached !== null) return $cached ?: null;

    $url  = _fs_doc_url($col, (string)$id);
    $resp = _fs_req('GET', $url);

    // Curl timed out — serve stale cache if available, else return null
    if (!empty($resp['_curl_error'])) {
        $stale = _fs_cache_get($col, $key, true);
        return $stale ? ($stale ?: null) : null;
    }

    $result = _fs_doc_to_array($resp);
    _fs_cache_set($col, $key, $result ?? false);
    return $result;
}

/** Get all documents in a collection. */
function fs_all(string $col, array $orderBy = [], int $limit = 0): array {
    return _fs_run_query($col, [], $orderBy, $limit);
}

/** Simple single-condition query. */
function fs_where(string $col, string $field, string $op, $value, array $orderBy = [], int $limit = 0): array {
    return _fs_run_query($col, [[$field, $op, $value]], $orderBy, $limit);
}

/** Multi-condition query. $wheres = [[$field, $op, $value], ...] */
function fs_query(string $col, array $wheres = [], array $orderBy = [], int $limit = 0): array {
    return _fs_run_query($col, $wheres, $orderBy, $limit);
}

/** Insert a new document with auto-incremented integer ID. Returns new ID. */
function fs_insert(string $col, array $data): int {
    $id  = fs_next_id($col);
    $now = date('c');
    if (!isset($data['createdAt'])) $data['createdAt'] = $now;
    $data['updatedAt'] = $now;
    $data['id']        = $id;

    $url = _fs_doc_url($col, (string)$id);
    _fs_req('PATCH', $url, _fs_array_to_doc($data));
    _fs_cache_bust($col);
    return $id;
}

/** Update specific fields on an existing document. */
function fs_update(string $col, int $id, array $data): void {
    $data['updatedAt'] = date('c');
    $url = _fs_doc_url($col, (string)$id);

    // Build updateMask query param so only the named fields are touched
    $mask  = implode('&', array_map(fn($k) => 'updateMask.fieldPaths=' . urlencode($k), array_keys($data)));
    _fs_req('PATCH', $url . '?' . $mask, _fs_array_to_doc($data));
    _fs_cache_bust($col);
}

/** Delete a document. */
function fs_delete(string $col, int $id): void {
    $url = _fs_doc_url($col, (string)$id);
    _fs_req('DELETE', $url);
    _fs_cache_bust($col);
}

/** Count documents matching a query. */
function fs_count(string $col, array $wheres = []): int {
    return count(fs_query($col, $wheres));
}

/** Sum a numeric field across matching documents. */
function fs_sum(string $col, string $field, array $wheres = []): float {
    $docs = fs_query($col, $wheres);
    return (float)array_sum(array_column($docs, $field));
}

/** Find first document matching wheres. Returns array or null. */
function fs_find(string $col, array $wheres): ?array {
    $results = fs_query($col, $wheres, [], 1);
    return $results[0] ?? null;
}
