<?php
/**
 * set_must_change.php
 *
 * ONE-TIME script — run AFTER migrate_auth_to_firebase.php.
 *
 * Sets mustChangePassword = true on every existing account so that
 * users who migrated are prompted to set their own password on next
 * Android login (instead of using the temporary "RedDoorz2024!" one).
 *
 * Admin and hotel_owner accounts are ALWAYS flagged.
 * Customer accounts are also flagged by default — remove the customer
 * block below if you prefer customers keep using the temp password silently.
 *
 * Usage:
 *   C:\xampp\php\php.exe C:\RedDoorz\config\set_must_change.php
 */

set_time_limit(0);
ini_set('display_errors', 1);

require_once __DIR__ . '/firestore.php';

echo "=== Set mustChangePassword ===\n\n";

$accounts = fs_all('accounts');
$updated  = 0;

foreach ($accounts as $account) {
    $id    = (int)($account['id'] ?? 0);
    $email = $account['email'] ?? '';
    $uid   = $account['firebaseUid'] ?? '';

    if ($id <= 0) continue;

    // Mark all accounts — users will set a proper password on first Android login
    fs_update('accounts', $id, ['mustChangePassword' => true]);

    // Also update the firebaseUid-keyed doc if it exists
    if (!empty($uid)) {
        global $_FS_BASE_URL;
        $url  = "$_FS_BASE_URL/accounts/$uid?updateMask.fieldPaths=mustChangePassword";
        $body = ['fields' => ['mustChangePassword' => ['booleanValue' => true]]];
        _fs_req('PATCH', $url, $body);
    }

    echo "  SET  $email\n";
    $updated++;
}

echo "\nDone — $updated account(s) flagged for password reset.\n";
