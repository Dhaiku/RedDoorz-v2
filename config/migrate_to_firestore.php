<?php
/**
 * migrate_to_firestore.php
 * Run ONCE from CLI to copy all MySQL data into Firestore via REST API.
 *
 * Usage:
 *   C:\xampp\php\php.exe C:\RedDoorz\config\migrate_to_firestore.php
 *
 * Requirements: MySQL running on 127.0.0.1:3307, Firestore REST API reachable.
 */

set_time_limit(0);
ini_set('display_errors', 1);

// Load firestore.php for _fs_token, _fs_req, _fs_encode, _fs_doc_url helpers
require_once __DIR__ . '/firestore.php';

// ── MySQL connection ──────────────────────────────────────────────────────────
$mysql = new mysqli("127.0.0.1", "root", "", "reddoorz", 3307);
if ($mysql->connect_error) {
    die("MySQL connection failed: " . $mysql->connect_error . "\n");
}
echo "MySQL connected.\n\n";

// ── UTF-8 safety: fix ISO-8859-1 bytes that sneak in from MySQL ───────────────
function migrate_fix_utf8(array $doc): array {
    return array_map(function($v) {
        if (!is_string($v)) return $v;
        return mb_check_encoding($v, 'UTF-8') ? $v : mb_convert_encoding($v, 'UTF-8', 'ISO-8859-1');
    }, $doc);
}

// ── Low-level: write a document directly by integer ID via REST PATCH ─────────
function migrate_write(string $col, int $id, array $doc): bool {
    global $_FS_BASE_URL;
    $url = "$_FS_BASE_URL/$col/$id";

    // Build updateMask query params
    $fields = array_keys($doc);
    $maskParams = implode('&', array_map(fn($f) => 'updateMask.fieldPaths=' . urlencode($f), $fields));

    $body = ['fields' => _fs_array_to_doc($doc)['fields']];
    $result = _fs_req('PATCH', $url . '?' . $maskParams, $body);
    return isset($result['fields']);
}

// ── Write counter document ────────────────────────────────────────────────────
function migrate_set_counter(string $col, int $nextId): void {
    global $_FS_BASE_URL;
    $url  = "$_FS_BASE_URL/counters/$col";
    $body = ['fields' => ['next' => ['integerValue' => (string)$nextId]]];
    _fs_req('PATCH', $url, $body);
}

// ── Field maps: MySQL column → Firestore field ────────────────────────────────
$maps = [
    'accounts' => [
        'table'  => 'Accounts',
        'idCol'  => 'Acct_Id',
        'fields' => [
            'email'              => ['col' => 'Acct_Email',             'type' => 'string'],
            'password'           => ['col' => 'Acct_Password',          'type' => 'string'],
            'role'               => ['col' => 'Acct_Role',              'type' => 'string'],
            'status'             => ['col' => 'Acct_Status',            'type' => 'string'],
            'mustChangePassword' => ['col' => 'Acct_MustChangePassword','type' => 'bool'],
            'firebaseUid'        => ['col' => 'Acct_FirebaseUid',       'type' => 'string'],
            'createdAt'          => ['col' => 'Acct_CreatedAt',         'type' => 'string'],
        ],
    ],
    'customers' => [
        'table'  => 'Customers',
        'idCol'  => 'Cust_Id',
        'fields' => [
            'acctId'    => ['col' => 'Cust_AcctId', 'type' => 'int'],
            'firstName' => ['col' => 'Cust_FName',  'type' => 'string'],
            'lastName'  => ['col' => 'Cust_LName',  'type' => 'string'],
            'phone'     => ['col' => 'Cust_Phone',  'type' => 'string'],
        ],
    ],
    'hotels' => [
        'table'  => 'Hotels',
        'idCol'  => 'Hotel_Id',
        'fields' => [
            'name'        => ['col' => 'Hotel_Name',        'type' => 'string'],
            'city'        => ['col' => 'Hotel_City',        'type' => 'string'],
            'address'     => ['col' => 'Hotel_Address',     'type' => 'string'],
            'description' => ['col' => 'Hotel_Description', 'type' => 'string'],
            'amenities'   => ['col' => 'Hotel_Amenities',   'type' => 'string'],
            'image'       => ['col' => 'Hotel_Image',       'type' => 'string'],
            'rating'      => ['col' => 'Hotel_Rating',      'type' => 'float'],
            'status'      => ['col' => 'Hotel_Status',      'type' => 'string'],
            'ownerId'     => ['col' => 'Hotel_OwnerId',     'type' => 'int'],
            'createdAt'   => ['col' => 'Hotel_CreatedAt',   'type' => 'string'],
        ],
    ],
    'rooms' => [
        'table'  => 'Rooms',
        'idCol'  => 'Room_Id',
        'fields' => [
            'hotelId'     => ['col' => 'Room_HotelId',     'type' => 'int'],
            'type'        => ['col' => 'Room_Type',        'type' => 'string'],
            'price'       => ['col' => 'Room_Price',       'type' => 'float'],
            'capacity'    => ['col' => 'Room_Capacity',    'type' => 'int'],
            'description' => ['col' => 'Room_Description', 'type' => 'string'],
            'status'      => ['col' => 'Room_Status',      'type' => 'string'],
        ],
    ],
    'bookings' => [
        'table'  => 'Bookings',
        'idCol'  => 'Book_Id',
        'fields' => [
            'custId'     => ['col' => 'Book_CustId',     'type' => 'int'],
            'hotelId'    => ['col' => 'Book_HotelId',    'type' => 'int'],
            'roomId'     => ['col' => 'Book_RoomId',     'type' => 'int'],
            'checkIn'    => ['col' => 'Book_CheckIn',    'type' => 'string'],
            'checkOut'   => ['col' => 'Book_CheckOut',   'type' => 'string'],
            'guests'     => ['col' => 'Book_Guests',     'type' => 'int'],
            'totalPrice' => ['col' => 'Book_TotalPrice', 'type' => 'float'],
            'status'     => ['col' => 'Book_Status',     'type' => 'string'],
            'refCode'    => ['col' => 'Book_RefCode',    'type' => 'string'],
            'createdAt'  => ['col' => 'Book_CreatedAt',  'type' => 'string'],
        ],
    ],
    'payments' => [
        'table'  => 'Payments',
        'idCol'  => 'Paymt_Id',
        'fields' => [
            'bookId'    => ['col' => 'Paymt_BookId', 'type' => 'int'],
            'method'    => ['col' => 'Paymt_Method', 'type' => 'string'],
            'status'    => ['col' => 'Paymt_Status', 'type' => 'string'],
            'refCode'   => ['col' => 'Paymt_RefCode','type' => 'string'],
            'amount'    => ['col' => 'Paymt_Amount', 'type' => 'float'],
            'createdAt' => ['col' => 'Paymt_Date',   'type' => 'string'],
        ],
    ],
    'earnings' => [
        'table'  => 'Earnings',
        'idCol'  => 'Earn_Id',
        'fields' => [
            'bookId'      => ['col' => 'Earn_BookId',      'type' => 'int'],
            'hotelId'     => ['col' => 'Earn_HotelId',     'type' => 'int'],
            'ownerId'     => ['col' => 'Earn_OwnerId',     'type' => 'int'],
            'totalAmount' => ['col' => 'Earn_TotalAmount', 'type' => 'float'],
            'ownerShare'  => ['col' => 'Earn_OwnerShare',  'type' => 'float'],
            'platformFee' => ['col' => 'Earn_PlatformFee', 'type' => 'float'],
            'status'      => ['col' => 'Earn_Status',      'type' => 'string'],
            'createdAt'   => ['col' => 'Earn_CreatedAt',   'type' => 'string'],
        ],
    ],
    'reviews' => [
        'table'  => 'Reviews',
        'idCol'  => 'Review_Id',
        'fields' => [
            'bookId'    => ['col' => 'Review_BookId',    'type' => 'int'],
            'hotelId'   => ['col' => 'Review_HotelId',   'type' => 'int'],
            'custId'    => ['col' => 'Review_CustId',    'type' => 'int'],
            'rating'    => ['col' => 'Review_Rating',    'type' => 'int'],
            'comment'   => ['col' => 'Review_Comment',   'type' => 'string'],
            'createdAt' => ['col' => 'Review_CreatedAt', 'type' => 'string'],
        ],
    ],
    'hotelstaff' => [
        'table'  => 'HotelStaff',
        'idCol'  => 'Staff_Id',
        'fields' => [
            'hotelId'   => ['col' => 'Staff_HotelId',   'type' => 'int'],
            'name'      => ['col' => 'Staff_Name',      'type' => 'string'],
            'role'      => ['col' => 'Staff_Role',      'type' => 'string'],
            'email'     => ['col' => 'Staff_Email',     'type' => 'string'],
            'phone'     => ['col' => 'Staff_Phone',     'type' => 'string'],
            'status'    => ['col' => 'Staff_Status',    'type' => 'string'],
            'createdAt' => ['col' => 'Staff_CreatedAt', 'type' => 'string'],
        ],
    ],
    'fcmtokens' => [
        'table'  => 'FcmTokens',
        'idCol'  => 'Token_Id',
        'fields' => [
            'acctId'    => ['col' => 'Token_AcctId',   'type' => 'int'],
            'token'     => ['col' => 'Token_Value',    'type' => 'string'],
            'updatedAt' => ['col' => 'Token_UpdatedAt','type' => 'string'],
        ],
    ],
    'payoutrequests' => [
        'table'  => 'PayoutRequests',
        'idCol'  => 'Payout_Id',
        'fields' => [
            'ownerId'   => ['col' => 'Payout_OwnerId',   'type' => 'int'],
            'hotelId'   => ['col' => 'Payout_HotelId',   'type' => 'int'],
            'amount'    => ['col' => 'Payout_Amount',     'type' => 'float'],
            'method'    => ['col' => 'Payout_Method',     'type' => 'string'],
            'accountNo' => ['col' => 'Payout_AccountNo',  'type' => 'string'],
            'status'    => ['col' => 'Payout_Status',     'type' => 'string'],
            'adminNote' => ['col' => 'Payout_AdminNote',  'type' => 'string'],
            'createdAt' => ['col' => 'Payout_CreatedAt',  'type' => 'string'],
        ],
    ],
    'blockeddates' => [
        'table'  => 'BlockedDates',
        'idCol'  => 'Block_Id',
        'fields' => [
            'roomId'    => ['col' => 'Block_RoomId',   'type' => 'int'],
            'hotelId'   => ['col' => 'Block_HotelId',  'type' => 'int'],
            'dateFrom'  => ['col' => 'Block_DateFrom', 'type' => 'string'],
            'dateTo'    => ['col' => 'Block_DateTo',   'type' => 'string'],
            'reason'    => ['col' => 'Block_Reason',   'type' => 'string'],
            'createdAt' => ['col' => 'Block_CreatedAt','type' => 'string'],
        ],
    ],
    'ownerapplications' => [
        'table'  => 'OwnerApplications',
        'idCol'  => 'App_Id',
        'fields' => [
            'applicantName' => ['col' => 'App_FullName',    'type' => 'string'],
            'acctEmail'     => ['col' => 'App_Email',       'type' => 'string'],
            'phone'         => ['col' => 'App_Phone',       'type' => 'string'],
            'hotelName'     => ['col' => 'App_HotelName',   'type' => 'string'],
            'city'          => ['col' => 'App_City',        'type' => 'string'],
            'address'       => ['col' => 'App_Address',     'type' => 'string'],
            'roomCount'     => ['col' => 'App_RoomCount',   'type' => 'int'],
            'message'       => ['col' => 'App_Message',     'type' => 'string'],
            'status'        => ['col' => 'App_Status',      'type' => 'string'],
            'createdAt'     => ['col' => 'App_CreatedAt',   'type' => 'string'],
        ],
    ],
];

// ── Warm up OAuth token once ───────────────────────────────────────────────────
echo "Getting Firestore OAuth token... ";
try {
    $tok = _fs_token();
    echo "OK\n\n";
} catch (Exception $e) {
    die("FAILED: " . $e->getMessage() . "\n");
}

// ── Migrate each collection ───────────────────────────────────────────────────
foreach ($maps as $fsCollection => $cfg) {
    $table  = $cfg['table'];
    $idCol  = $cfg['idCol'];
    $fields = $cfg['fields'];

    // Check table exists
    $checkRes = $mysql->query("SHOW TABLES LIKE '$table'");
    if (!$checkRes || $checkRes->num_rows === 0) {
        echo "  Skipping $fsCollection ($table not found)\n";
        continue;
    }

    $res = $mysql->query("SELECT * FROM `$table`");
    if (!$res) {
        echo "  Skipping $fsCollection (query failed: {$mysql->error})\n";
        continue;
    }

    $count = 0;
    $maxId = 0;
    $errors = 0;

    while ($row = $res->fetch_assoc()) {
        $id  = (int) $row[$idCol];
        if ($id <= 0) continue;

        $doc = ['id' => $id];

        foreach ($fields as $fsField => $info) {
            $sqlCol = $info['col'];
            $type   = $info['type'];
            $val    = $row[$sqlCol] ?? null;

            if ($val === null || $val === '') continue;

            switch ($type) {
                case 'int':   $doc[$fsField] = (int)$val;   break;
                case 'float': $doc[$fsField] = (float)$val; break;
                case 'bool':  $doc[$fsField] = (bool)$val;  break;
                default:      $doc[$fsField] = (string)$val; break;
            }
        }

        // Add timestamps if missing
        if (!isset($doc['createdAt'])) {
            $doc['createdAt'] = date('Y-m-d H:i:s');
        }
        $doc['updatedAt'] = $doc['createdAt'];

        // Fix any ISO-8859-1 characters (e.g. ñ in city/address fields)
        $doc = migrate_fix_utf8($doc);

        $ok = migrate_write($fsCollection, $id, $doc);
        if ($ok) {
            $count++;
            $maxId = max($maxId, $id);
        } else {
            $errors++;
            echo "  ERROR writing $fsCollection/$id\n";
        }

        if ($count % 20 === 0 && $count > 0) {
            echo "  $fsCollection: $count rows written...\n";
        }

        // Small delay to avoid rate limits
        if ($count % 50 === 0) usleep(200000); // 200ms every 50 docs
    }

    // Set counter so fs_next_id() starts above the highest ID
    if ($maxId > 0) {
        migrate_set_counter($fsCollection, $maxId + 1);
    }

    $status = $errors > 0 ? "($errors errors)" : "";
    echo "✓ $fsCollection: $count documents migrated (next ID = " . ($maxId + 1) . ") $status\n";
}

echo "\n✅ Migration complete!\n";
echo "MySQL data is now in Firestore. You can test the site at http://localhost:8014/\n";
