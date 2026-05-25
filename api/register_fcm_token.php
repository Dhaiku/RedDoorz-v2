<?php
/**
 * POST /api/register_fcm_token.php
 * Called by the Android app after Firebase gives it an FCM token.
 * Body: { "acct_id": 5, "token": "fcm_token_string" }
 * Auth: simple bearer token matching session or API key header.
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

// Upsert: insert or update timestamp if already exists
$safeToken = $conn->real_escape_string($token);
$conn->query("
    INSERT INTO FcmTokens (Token_AcctId, Token_Value)
    VALUES ($acctId, '$safeToken')
    ON DUPLICATE KEY UPDATE Token_UpdatedAt = CURRENT_TIMESTAMP
");

echo json_encode(['success' => true]);
