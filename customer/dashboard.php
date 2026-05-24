<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /auth/login.php"); exit();
}

if (isset($_POST['cancel_booking'])) {
    $bookId = (int) $_POST['book_id'];
    $custId = (int) $_SESSION['customer_id'];

    // Fetch booking to check status and 24-hour rule
    $bRow = $conn->query("
        SELECT Book_Status, Book_CheckIn FROM Bookings
        WHERE Book_Id=$bookId AND Book_CustId=$custId
        LIMIT 1
    ")->fetch_assoc();

    $cancelError = '';
    if (!$bRow) {
        $cancelError = 'Booking not found.';
    } elseif (!in_array($bRow['Book_Status'], ['pending', 'confirmed'])) {
        $cancelError = 'This booking cannot be cancelled.';
    } else {
        // Enforce 24-hour rule: cannot cancel within 24h of check-in
        $checkInTs = strtotime($bRow['Book_CheckIn']);
        $hoursUntilCheckIn = ($checkInTs - time()) / 3600;
        if ($hoursUntilCheckIn < 24) {
            $cancelError = 'Cancellations must be made at least 24 hours before check-in.';
        }
    }

    if (!$cancelError) {
        $conn->query("UPDATE Bookings SET Book_Status='cancelled' WHERE Book_Id=$bookId AND Book_CustId=$custId");
        // Void the Earnings record if it exists
        $hasEarningsTable = $conn->query("SHOW TABLES LIKE 'Earnings'")->num_rows > 0;
        if ($hasEarningsTable) {
            $conn->query("UPDATE Earnings SET Earn_Status='voided' WHERE Earn_BookId=$bookId");
        }
        header("Location: dashboard.php?cancelled=1"); exit();
    } else {
        // Re-show dashboard with error
        session_start() ; // already started but store error
        $_SESSION['cancel_error'] = $cancelError;
        header("Location: dashboard.php"); exit();
    }
}

$custId   = (int) $_SESSION['customer_id'];
$cancelError = $_SESSION['cancel_error'] ?? '';
unset($_SESSION['cancel_error']);
$justCancelled = isset($_GET['cancelled']);

$hasPaymentsTable = $conn->query("SHOW TABLES LIKE 'Payments'")->num_rows > 0;

if ($hasPaymentsTable) {
    $bookings = $conn->query("
        SELECT b.*, h.Hotel_Name, h.Hotel_City, r.Room_Type, r.Room_Price,
               p.Paymt_Id, p.Paymt_Status
        FROM Bookings b
        JOIN Hotels h ON h.Hotel_Id = b.Book_HotelId
        JOIN Rooms   r ON r.Room_Id  = b.Book_RoomId
        LEFT JOIN Payments p ON p.Paymt_BookId = b.Book_Id
        WHERE b.Book_CustId = $custId
        ORDER BY b.Book_CreatedAt DESC
    ");
} else {
    $bookings = $conn->query("
        SELECT b.*, h.Hotel_Name, h.Hotel_City, r.Room_Type, r.Room_Price
        FROM Bookings b
        JOIN Hotels h ON h.Hotel_Id = b.Book_HotelId
        JOIN Rooms   r ON r.Room_Id  = b.Book_RoomId
        WHERE b.Book_CustId = $custId
        ORDER BY b.Book_CreatedAt DESC
    ");
}

$title = "My Bookings";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:calc(100vh - 64px);">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <div class="page-header">
            <h1>My Bookings</h1>
            <p>Track and manage all your hotel reservations.</p>
        </div>

        <?php if ($justCancelled): ?>
        <div class="alert-rd-success mb-4" style="display:flex; align-items:center; gap:9px;">
            <i class="bi bi-check-circle-fill"></i> Your booking has been successfully cancelled.
        </div>
        <?php endif; ?>

        <?php if ($cancelError): ?>
        <div class="alert-rd-danger mb-4" style="display:flex; align-items:center; gap:9px;">
            <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($cancelError) ?>
        </div>
        <?php endif; ?>

        <?php if ($bookings->num_rows === 0): ?>
            <!-- Empty state -->
            <div style="
                background:#fff; border-radius:14px; padding:72px 20px;
                text-align:center; box-shadow:var(--rd-shadow);
                border:1px solid rgba(228,223,223,0.5);
            ">
                <div style="
                    width:70px; height:70px; margin:0 auto 18px;
                    background:var(--rd-red-pale); border-radius:50%;
                    display:flex; align-items:center; justify-content:center;
                    font-size:28px; color:var(--rd-red);
                "><i class="bi bi-calendar-x"></i></div>
                <h5 style="font-size:17px; font-weight:700; margin:0 0 8px; color:#333;">No bookings yet</h5>
                <p style="color:var(--rd-muted); font-size:14px; margin:0 0 24px; max-width:320px; margin-left:auto; margin-right:auto;">
                    Start exploring available hotels and make your first reservation.
                </p>
                <a href="/hotels/search.php" class="btn-rd" style="padding:10px 28px;">
                    <i class="bi bi-search me-1"></i>Find Hotels
                </a>
            </div>

        <?php else: ?>

            <?php while ($b = $bookings->fetch_assoc()):
                $nights = max(1, (new DateTime($b['Book_CheckIn']))->diff(new DateTime($b['Book_CheckOut']))->days);
                $badge  = match($b['Book_Status']) {
                    'confirmed' => '<span class="badge-confirmed">Confirmed</span>',
                    'cancelled' => '<span class="badge-cancelled">Cancelled</span>',
                    'completed' => '<span class="badge-completed">Completed</span>',
                    default     => '<span class="badge-pending">Pending</span>',
                };
                $imgSeed = 'reddoorz' . $b['Book_HotelId'];
            ?>
            <div style="
                background:#fff; border-radius:14px; margin-bottom:14px;
                box-shadow:var(--rd-shadow); overflow:hidden;
                border:1px solid rgba(228,223,223,0.5);
                display:flex; gap:0; align-items:stretch;
                transition:box-shadow 0.2s;
            "
            onmouseover="this.style.boxShadow='0 8px 28px rgba(184,0,32,0.12)'"
            onmouseout="this.style.boxShadow='var(--rd-shadow)'">

                <!-- Hotel image strip -->
                <div style="width:80px; flex-shrink:0; overflow:hidden; display:none;" class="d-md-block">
                    <img src="https://picsum.photos/seed/<?= $imgSeed ?>/200/200"
                         alt="" style="width:100%; height:100%; object-fit:cover;">
                </div>

                <!-- Info -->
                <div style="flex:1; padding:18px 20px; display:flex; gap:16px; flex-wrap:wrap; align-items:center;">

                    <!-- Hotel icon (mobile fallback) -->
                    <div class="d-md-none" style="
                        width:52px; height:52px; flex-shrink:0;
                        background:var(--rd-red-pale); border-radius:10px;
                        display:flex; align-items:center; justify-content:center;
                        font-size:22px; color:var(--rd-red);
                    "><i class="bi bi-building"></i></div>

                    <div style="flex:1; min-width:200px;">
                        <div style="font-size:15px; font-weight:700; margin-bottom:2px;">
                            <?= htmlspecialchars($b['Hotel_Name']) ?>
                        </div>
                        <div style="font-size:12px; color:var(--rd-muted); margin-bottom:8px; display:flex; align-items:center; gap:5px;">
                            <i class="bi bi-geo-alt-fill" style="font-size:10px; color:var(--rd-red);"></i>
                            <?= htmlspecialchars($b['Hotel_City']) ?>
                            &nbsp;&bull;&nbsp;
                            <?= htmlspecialchars($b['Room_Type']) ?>
                        </div>
                        <div style="font-size:12px; color:#555; display:flex; gap:14px; flex-wrap:wrap;">
                            <span style="display:flex; align-items:center; gap:4px;">
                                <i class="bi bi-calendar-event" style="color:var(--rd-red);"></i>
                                <?= date('M d', strtotime($b['Book_CheckIn'])) ?> &rarr; <?= date('M d, Y', strtotime($b['Book_CheckOut'])) ?>
                            </span>
                            <span style="display:flex; align-items:center; gap:4px;">
                                <i class="bi bi-moon" style="color:var(--rd-red);"></i>
                                <?= $nights ?> night<?= $nights != 1 ? 's' : '' ?>
                            </span>
                            <span style="display:flex; align-items:center; gap:4px;">
                                <i class="bi bi-people" style="color:var(--rd-red);"></i>
                                <?= $b['Book_Guests'] ?> guest<?= $b['Book_Guests'] > 1 ? 's' : '' ?>
                            </span>
                        </div>
                    </div>

                    <!-- Price + Status + Actions -->
                    <div style="text-align:right; min-width:140px;">
                        <div class="price-tag" style="font-size:17px;">&#8369;<?= number_format($b['Book_TotalPrice']) ?></div>
                        <div style="margin:6px 0;"><?= $badge ?></div>
                        <div style="display:flex; gap:7px; justify-content:flex-end; margin-top:8px; flex-wrap:wrap;">
                            <a href="/customer/booking_detail.php?id=<?= $b['Book_Id'] ?>"
                               class="btn-rd-outline" style="font-size:12px; padding:5px 14px; border-radius:6px;">
                                View
                            </a>
                            <?php if ($b['Book_Status'] === 'pending'): ?>
                                <?php $needsPayment = $hasPaymentsTable && empty($b['Paymt_Id']); ?>
                                <?php if ($needsPayment): ?>
                                <a href="/hotels/payment.php?id=<?= $b['Book_Id'] ?>"
                                   class="btn-rd" style="font-size:12px; padding:5px 14px; border-radius:6px;">
                                    <i class="bi bi-credit-card me-1"></i>Pay Now
                                </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (in_array($b['Book_Status'], ['pending', 'confirmed'])): ?>
                                <form method="POST" onsubmit="return confirm('Cancel this booking? This cannot be undone.');" style="margin:0;">
                                    <input type="hidden" name="book_id" value="<?= $b['Book_Id'] ?>">
                                    <button type="submit" name="cancel_booking" style="
                                        font-size:12px; background:none; color:#999;
                                        border:1px solid #DDD; border-radius:6px;
                                        padding:5px 14px; cursor:pointer; font-weight:600;
                                        font-family:'DM Sans',sans-serif;
                                        transition:all 0.15s;
                                    "
                                    onmouseover="this.style.borderColor='#aaa';this.style.color='#666'"
                                    onmouseout="this.style.borderColor='#DDD';this.style.color='#999'">
                                        Cancel
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
            <?php endwhile; ?>

        <?php endif; ?>

    </div>
</div>

<?php include "../layout/footer.php"; ?>


