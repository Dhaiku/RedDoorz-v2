<?php
/**
 * POST /api/verify_google_token.php
 * Called by Android after Google Sign-In gives it a Firebase ID token.
 * Verifies the token with Firebase Admin SDK, then either:
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

    $safeEmail = $conn->real_escape_string($email);

    // Check if account already exists by email
    $account = $conn->query("SELECT * FROM Accounts WHERE Acct_Email='$safeEmail' LIMIT 1")->fetch_assoc();

    if ($account) {
        // Existing account — link Firebase UID if not already stored
        $hasUidCol = $conn->query("SHOW COLUMNS FROM Accounts LIKE 'Acct_FirebaseUid'")->num_rows > 0;
        if ($hasUidCol) {
            $safeUid = $conn->real_escape_string($firebaseUid);
            $conn->query("UPDATE Accounts SET Acct_FirebaseUid='$safeUid' WHERE Acct_Id={$account['Acct_Id']}");
        }
        $acctId = (int) $account['Acct_Id'];
        $role   = $account['Acct_Role'];

        // Get display name
        if ($role === 'customer') {
            $cust = $conn->query("SELECT Cust_FName, Cust_LName FROM Customers WHERE Cust_AcctId=$acctId LIMIT 1")->fetch_assoc();
            $displayName = trim(($cust['Cust_FName'] ?? '') . ' ' . ($cust['Cust_LName'] ?? ''));
        } else {
            $displayName = $account['Acct_Email'];
        }
    } else {
        // New user — create account + customer profile
        $safeName = $conn->real_escape_string($name);
        $nameParts = explode(' ', trim($name), 2);
        $firstName = $conn->real_escape_string($nameParts[0] ?? $name);
        $lastName  = $conn->real_escape_string($nameParts[1] ?? '');

        // Insert account (no password — Google-authenticated)
        $safeUid = $conn->real_escape_string($firebaseUid);
        $hasUidCol = $conn->query("SHOW COLUMNS FROM Accounts LIKE 'Acct_FirebaseUid'")->num_rows > 0;

        if ($hasUidCol) {
            $conn->query("
                INSERT INTO Accounts (Acct_Email, Acct_Password, Acct_Role, Acct_Status, Acct_FirebaseUid)
                VALUES ('$safeEmail', '', 'customer', 'active', '$safeUid')
            ");
        } else {
            $conn->query("
                INSERT INTO Accounts (Acct_Email, Acct_Password, Acct_Role, Acct_Status)
                VALUES ('$safeEmail', '', 'customer', 'active')
            ");
        }
        $acctId = $conn->insert_id;

        // Insert customer profile
        $conn->query("
            INSERT INTO Customers (Cust_AcctId, Cust_FName, Cust_LName)
            VALUES ($acctId, '$firstName', '$lastName')
        ");

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
