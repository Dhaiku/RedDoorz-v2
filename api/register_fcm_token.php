<?php
/**
 * POST /api/register_fcm_token.php
 * Called by the Android app after Firebase gives it an FCM token.
 * Body: { "acct_id": 5, "token": "fcm_token_string" }
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Api-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit(); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    { http_response_code(405); echo json_encode(['error' => 'Method not allowed']); exit(); }

require_once '../config/db.php';

$body   = json_decode(file_get_contents('php://input'), true);
$acctId = (int) ($body['acct_id'] ?? 0);
$token  = trim($body['token'] ?? '');

if (!$acctId || !$token) {
    http_response_code(400);
    echo json_encode(['error' => 'acct_id and token are required']);
    exit();
}

// Check if a token record already exists for this account
$existing = fs_find('fcmtokens', [['acctId', '=', $acctId]]);

if ($existing) {
    // Update token value and timestamp
    fs_update('fcmtokens', (int)$existing['id'], [
        'token'     => $token,
        'updatedAt' => date('Y-m-d H:i:s'),
    ]);
} else {
    // Insert new token record
    fs_insert('fcmtokens', [
        'acctId' => $acctId,
        'token'  => $token,
    ]);
}

echo json_encode(['success' => true]);
