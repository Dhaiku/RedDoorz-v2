<?php
/**
 * notify.php — send an FCM push notification to a user.
 *
 * Usage:
 *   require_once "../config/notify.php";
 *   sendPushNotification($conn, $acctId, 'Booking Confirmed', 'Your booking #0042 is confirmed!', ['booking_id' => '42']);
 */

function sendPushNotification($conn, int $acctId, string $title, string $body, array $data = []): void {
    // Fetch all FCM tokens for this account
    $res = $conn->query("SELECT Token_Value FROM FcmTokens WHERE Token_AcctId=$acctId");
    if (!$res || $res->num_rows === 0) return;

    try {
        require_once __DIR__ . '/firebase.php';
        $messaging = getFirebaseMessaging();

        while ($row = $res->fetch_assoc()) {
            $token = $row['Token_Value'];
            try {
                $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $token)
                    ->withNotification(\Kreait\Firebase\Messaging\Notification::create($title, $body))
                    ->withData(array_map('strval', $data));
                $messaging->send($message);
            } catch (\Throwable $e) {
                // Token may be stale — remove it
                $safeToken = $conn->real_escape_string($token);
                $conn->query("DELETE FROM FcmTokens WHERE Token_Value='$safeToken'");
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
 */
function syncBookingToFirestore($conn, int $bookId): void {
    $row = $conn->query("
        SELECT b.Book_Id, b.Book_Status, b.Book_CheckIn, b.Book_CheckOut,
               b.Book_TotalPrice, b.Book_HotelId, h.Hotel_Name,
               r.Room_Type, b.Book_CustId, c.Cust_AcctId
        FROM Bookings b
        JOIN Hotels h    ON h.Hotel_Id = b.Book_HotelId
        JOIN Rooms r     ON r.Room_Id  = b.Book_RoomId
        JOIN Customers c ON c.Cust_Id  = b.Book_CustId
        WHERE b.Book_Id = $bookId LIMIT 1
    ")->fetch_assoc();
    if (!$row) return;

    try {
        require_once __DIR__ . '/firebase.php';
        $firestore = getFirestore()->database();
        $firestore->collection('bookings')->document((string)$bookId)->set([
            'bookingId'   => $bookId,
            'status'      => $row['Book_Status'],
            'hotelName'   => $row['Hotel_Name'],
            'roomType'    => $row['Room_Type'],
            'checkIn'     => $row['Book_CheckIn'],
            'checkOut'    => $row['Book_CheckOut'],
            'totalPrice'  => (float) $row['Book_TotalPrice'],
            'customerId'  => (int) $row['Book_CustId'],
            'acctId'      => (int) $row['Cust_AcctId'],
            'updatedAt'   => date('c'),
        ]);
    } catch (\Throwable $e) {
        error_log('Firestore sync error: ' . $e->getMessage());
    }
}
