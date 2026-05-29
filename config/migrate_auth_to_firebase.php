<?php
/**
 * migrate_auth_to_firebase.php
 *
 * ONE-TIME migration script.
 * Reads every document in the Firestore `accounts` collection,
 * creates a matching Firebase Authentication user (email + temporary password),
 * then writes the Firebase UID back to the Firestore account doc as `firebaseUid`.
 *
 * After this runs:
 *  - The Android app can sign in via Firebase Auth and find the user's Firestore doc.
 *  - The PHP website continues to work unchanged (it uses bcrypt, not Firebase Auth).
 *  - Existing users will need to reset their password once (or use the temp password
 *    "RedDoorz2024!" which is set here — change it below if you prefer another).
 *
 * Usage (run from CLI):
 *   C:\xampp\php\php.exe C:\RedDoorz\config\migrate_auth_to_firebase.php
 *
 * Requirements:
 *   - composer packages already installed (kreait/firebase-php)
 *   - FIREBASE_KEY_PATH env var set, or default path correct
 */

set_time_limit(0);
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/firestore.php';   // fs_all(), fs_update(), _fs_token()

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth\CreateUser;
use Kreait\Firebase\Exception\Auth\EmailExists;

// ── Config ─────────────────────────────────────────────────────────────────────
$keyPath = getenv('FIREBASE_KEY_PATH')
    ?: 'C:/Users/bever/Downloads/reddoorz-8f605-firebase-adminsdk-fbsvc-bb9a5b8ce6.json';

// Temporary password given to every migrated account.
// Android users will need to use this (or "Forgot Password") on first login.
// Hotel owners already have mustChangePassword=true so they'll be prompted anyway.
$TEMP_PASSWORD = 'RedDoorz2024!';

// ── Firebase Admin SDK ────────────────────────────────────────────────────────
$factory = (new Factory)->withServiceAccount($keyPath);
$auth    = $factory->createAuth();

echo "=== Firebase Auth Migration ===\n\n";

// ── Fetch all accounts from Firestore ─────────────────────────────────────────
echo "Fetching accounts from Firestore...\n";
$accounts = fs_all('accounts');
$total    = count($accounts);
echo "Found $total account(s).\n\n";

if ($total === 0) {
    echo "No accounts found. Make sure migrate_to_firestore.php was run first.\n";
    exit(1);
}

// ── Process each account ──────────────────────────────────────────────────────
$created  = 0;
$skipped  = 0;
$errors   = 0;

foreach ($accounts as $account) {
    $fsDocId = (string)($account['id'] ?? '');   // integer ID stored as string in Firestore doc name
    $email   = $account['email'] ?? '';
    $role    = $account['role']  ?? 'customer';

    if (empty($email)) {
        echo "  SKIP  doc=$fsDocId — no email\n";
        $skipped++;
        continue;
    }

    // If already migrated, skip
    if (!empty($account['firebaseUid'])) {
        echo "  SKIP  $email — already has firebaseUid={$account['firebaseUid']}\n";
        $skipped++;
        continue;
    }

    try {
        // Try to create the Firebase Auth user
        $newUser = $auth->createUser(
            CreateUser::new()
                ->withEmail($email)
                ->withPassword($TEMP_PASSWORD)
                ->withEmailVerified(false)
        );
        $firebaseUid = $newUser->uid;
        echo "  CREATE  $email  →  uid=$firebaseUid\n";

    } catch (EmailExists $e) {
        // User already exists in Firebase Auth — just fetch the UID
        try {
            $existing    = $auth->getUserByEmail($email);
            $firebaseUid = $existing->uid;
            echo "  EXIST   $email  →  uid=$firebaseUid (already in Firebase Auth)\n";
        } catch (\Throwable $inner) {
            echo "  ERROR   $email — could not fetch existing user: " . $inner->getMessage() . "\n";
            $errors++;
            continue;
        }

    } catch (\Throwable $e) {
        echo "  ERROR   $email — " . $e->getMessage() . "\n";
        $errors++;
        continue;
    }

    // Write firebaseUid back to the Firestore account doc
    // The Firestore doc ID is the integer account ID (as string)
    try {
        fs_update('accounts', (int)$fsDocId, ['firebaseUid' => $firebaseUid]);

        // Also create/update the customers doc using firebaseUid as the doc ID
        // so the Android app can do a single-doc lookup by Firebase UID
        $custDocs = fs_query('customers', [['acctId', '=', (int)$fsDocId]]);
        if (!empty($custDocs)) {
            $cust = $custDocs[0];
            // Write a parallel customers doc keyed by firebaseUid
            global $_FS_BASE_URL;
            $url  = "$_FS_BASE_URL/customers/$firebaseUid";
            $body = [
                'fields' => [
                    'acctId'    => ['stringValue' => $firebaseUid],
                    'firstName' => ['stringValue' => $cust['firstName'] ?? ''],
                    'lastName'  => ['stringValue' => $cust['lastName']  ?? ''],
                    'phone'     => ['stringValue' => $cust['phone']     ?? ''],
                ]
            ];
            _fs_req('PATCH', $url, $body);
        }

        // Also write accounts doc keyed by firebaseUid (so Android single-doc lookup works)
        $url  = "$_FS_BASE_URL/accounts/$firebaseUid";
        $body = [
            'fields' => [
                'email'              => ['stringValue' => $email],
                'role'               => ['stringValue' => $role],
                'status'             => ['stringValue' => $account['status']             ?? 'active'],
                'mustChangePassword' => ['booleanValue' => (bool)($account['mustChangePassword'] ?? false)],
                'firebaseUid'        => ['stringValue' => $firebaseUid],
                'createdAt'          => ['stringValue' => $account['createdAt']          ?? date('c')],
            ]
        ];
        _fs_req('PATCH', $url, $body);

        $created++;
    } catch (\Throwable $e) {
        echo "  ERROR   writing firebaseUid for $email — " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n=== Done ===\n";
echo "  Migrated : $created\n";
echo "  Skipped  : $skipped\n";
echo "  Errors   : $errors\n";
echo "\n";
echo "All existing accounts have been given the temporary password: $TEMP_PASSWORD\n";
echo "Android users will be asked to change it on first login (mustChangePassword=true).\n";
echo "\nNext step: run  set_must_change.php  to mark all migrated accounts for password reset.\n";
