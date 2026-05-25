<?php
/**
 * POST /api/verify_google_token.php
 * Called by Android after Google Sign-In gives it a Firebase ID token.
 * Verifies the token with Firebase Admin SDK (kreait), then either:
 *   - Links to an existing account (matched by email), or
 *   - Creates a new customer account automatically.
 *
 * Body: { "id_token": "firebase_id_token_string" }
 * Returns: { "success": true, "acct_id": 5, "role": "customer", "display_name": "..." }
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit(); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    { http_response_code(405); echo json_encode(['error' => 'Method not allowed']); exit(); }

require_once '../config/db.php';
require_once '../config/firebase.php';

$body    = json_decode(file_get_contents('php://input'), true);
$idToken = trim($body['id_token'] ?? '');

if (!$idToken) {
    http_response_code(400);
    echo json_encode(['error' => 'id_token is required']);
    exit();
}

try {
    $auth        = getFirebaseAuth();
    $verified    = $auth->verifyIdToken($idToken);
    $firebaseUid = $verified->claims()->get('sub');
    $email       = $verified->claims()->get('email') ?? '';
    $name        = $verified->claims()->get('name')  ?? '';
    $picture     = $verified->claims()->get('picture') ?? '';

    // Check if account already exists by email
    $account = fs_find('accounts', [['email', '=', $email]]);

    if ($account) {
        $acctId = (int)$account['id'];
        $role   = $account['role'];

        // Store Firebase UID if not already set
        if (empty($account['firebaseUid'])) {
            fs_update('accounts', $acctId, ['firebaseUid' => $firebaseUid]);
        }

        // Get display name
        if ($role === 'customer') {
            $cust = fs_find('customers', [['acctId', '=', $acctId]]);
            $displayName = trim(($cust['firstName'] ?? '') . ' ' . ($cust['lastName'] ?? ''));
        } else {
            $displayName = $account['email'];
        }

    } else {
        // New user — create account + customer profile
        $nameParts = explode(' ', trim($name), 2);
        $firstName = $nameParts[0] ?? $name;
        $lastName  = $nameParts[1] ?? '';

        $acctId = fs_insert('accounts', [
            'email'       => $email,
            'password'    => '',   // no password — Google-authenticated
            'role'        => 'customer',
            'status'      => 'active',
            'firebaseUid' => $firebaseUid,
        ]);

        fs_insert('customers', [
            'acctId'    => $acctId,
            'firstName' => $firstName,
            'lastName'  => $lastName,
        ]);

        $role        = 'customer';
        $displayName = trim("$firstName $lastName");
    }

    echo json_encode([
        'success'      => true,
        'acct_id'      => $acctId,
        'role'         => $role,
        'display_name' => $displayName,
        'email'        => $email,
        'picture'      => $picture,
    ]);

} catch (\Throwable $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Token verification failed: ' . $e->getMessage()]);
}
