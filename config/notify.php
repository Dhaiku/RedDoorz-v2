<?php
/**
 * notify.php — send an FCM push notification to a user.
 * Updated for Firestore (no MySQL $conn needed).
 *
 * Usage:
 *   require_once "../config/notify.php";
 *   sendPushNotification(null, $acctId, 'Booking Confirmed', 'Your booking #0042 is confirmed!', ['booking_id' => '42']);
 */

function sendPushNotification($conn_unused, int $acctId, string $title, string $body, array $data = []): void {
    // Fetch all FCM tokens for this account from Firestore
    $tokens = fs_query('fcmtokens', [['acctId', '=', $acctId]]);
    if (empty($tokens)) return;

    try {
        require_once __DIR__ . '/firebase.php';
        $messaging = getFirebaseMessaging();

        foreach ($tokens as $tokenRow) {
            $token = $tokenRow['token'] ?? '';
            if (!$token) continue;
            try {
                $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $token)
                    ->withNotification(\Kreait\Firebase\Messaging\Notification::create($title, $body))
                    ->withData(array_map('strval', $data));
                $messaging->send($message);
            } catch (\Throwable $e) {
                // Token may be stale — remove it from Firestore
                fs_delete('fcmtokens', (int)$tokenRow['id']);
            }
        }
    } catch (\Throwable $e) {
        // Firebase not configured — silently skip
        error_log('FCM notify error: ' . $e->getMessage());
    }
}

/**
 * Write a booking status update to Firestore so the Android app
 * gets real-time updates without polling.
 * With the full Firestore migration, the bookings collection IS Firestore,
 * so this just ensures the booking document reflects the latest status.
 */
function syncBookingToFirestore($conn_unused, int $bookId): void {
    // With full Firestore, the booking is already written — just enrich with denormalized fields
    $booking = fs_get('bookings', $bookId);
    if (!$booking) return;

    try {
        // Enrich the Firestore booking doc with hotel/room name for Android real-time listeners
        $hotel = fs_get('hotels', (int)($booking['hotelId'] ?? 0));
        $room  = fs_get('rooms',  (int)($booking['roomId']  ?? 0));
        $cust  = fs_get('customers', (int)($booking['custId'] ?? 0));

        fs_update('bookings', $bookId, [
            'hotelName'  => $hotel['name']      ?? '',
            'roomType'   => $room['type']        ?? '',
            'acctId'     => (int)($cust['acctId'] ?? 0),
        ]);
    } catch (\Throwable $e) {
        error_log('Firestore sync error: ' . $e->getMessage());
    }
}
