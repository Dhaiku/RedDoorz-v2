<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /auth/login.php"); exit();
}

if (isset($_POST['cancel_booking'])) {
    $bookId = (int) $_POST['book_id'];
    $custId = (int) $_SESSION['customer_id'];

    $bRow = fs_find('bookings', [['id', '=', $bookId], ['custId', '=', $custId]]);

    $cancelError = '';
    if (!$bRow) {
        $cancelError = 'Booking not found.';
    } elseif (!in_array($bRow['status'], ['pending', 'confirmed'])) {
        $cancelError = 'This booking cannot be cancelled.';
    } else {
        $checkInTs = strtotime($bRow['checkIn']);
        $hoursUntilCheckIn = ($checkInTs - time()) / 3600;
        if ($hoursUntilCheckIn < 24) {
            $cancelError = 'Cancellations must be made at least 24 hours before check-in.';
        }
    }

    if (!$cancelError) {
        fs_update('bookings', $bookId, ['status' => 'cancelled']);
        // Void the Earnings record if it exists
        $earnRow = fs_find('earnings', [['bookId', '=', $bookId]]);
        if ($earnRow) {
            fs_update('earnings', (int)$earnRow['id'], ['status' => 'voided']);
        }
        header("Location: dashboard.php?cancelled=1"); exit();
    } else {
        $_SESSION['cancel_error'] = $cancelError;
        header("Location: dashboard.php"); exit();
    }
}

$custId      = (int) $_SESSION['customer_id'];
$cancelError = $_SESSION['cancel_error'] ?? '';
unset($_SESSION['cancel_error']);
$justCancelled = isset($_GET['cancelled']);

$bookings = fs_query('bookings', [['custId', '=', $custId]], [['createdAt', 'DESC']]);

// Enrich bookings with hotel, room, and payment info
foreach ($bookings as &$b) {
    $hotel = fs_get('hotels', (int)$b['hotelId']);
    $b['hotelName'] = $hotel['name'] ?? '';
    $b['hotelCity'] = $hotel['city'] ?? '';

    $room = fs_get('rooms', (int)$b['roomId']);
    $b['roomType']  = $room['type']  ?? '';
    $b['roomPrice'] = $room['price'] ?? 0;

    $payment = fs_find('payments', [['bookId', '=', $b['id']]]);
    $b['paymtId']     = $payment['id']     ?? null;
    $b['paymtStatus'] = $payment['status'] ?? null;
}
unset($b);

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

        <?php if (empty($bookings)): ?>
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

            <?php foreach ($bookings as $b):
                $nights = max(1, (new DateTime($b['checkIn']))->diff(new DateTime($b['checkOut']))->days);
                $badge  = match($b['status']) {
                    'confirmed' => '<span class="badge-confirmed">Confirmed</span>',
                    'cancelled' => '<span class="badge-cancelled">Cancelled</span>',
                    'completed' => '<span class="badge-completed">Completed</span>',
                    default     => '<span class="badge-pending">Pending</span>',
                };
                $imgSeed = 'reddoorz' . $b['hotelId'];
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
                            <?= htmlspecialchars($b['hotelName']) ?>
                        </div>
                        <div style="font-size:12px; color:var(--rd-muted); margin-bottom:8px; display:flex; align-items:center; gap:5px;">
                            <i class="bi bi-geo-alt-fill" style="font-size:10px; color:var(--rd-red);"></i>
                            <?= htmlspecialchars($b['hotelCity']) ?>
                            &nbsp;&bull;&nbsp;
                            <?= htmlspecialchars($b['roomType']) ?>
                        </div>
                        <div style="font-size:12px; color:#555; display:flex; gap:14px; flex-wrap:wrap;">
                            <span style="display:flex; align-items:center; gap:4px;">
                                <i class="bi bi-calendar-event" style="color:var(--rd-red);"></i>
                                <?= date('M d', strtotime($b['checkIn'])) ?> &rarr; <?= date('M d, Y', strtotime($b['checkOut'])) ?>
                            </span>
                            <span style="display:flex; align-items:center; gap:4px;">
                                <i class="bi bi-moon" style="color:var(--rd-red);"></i>
                                <?= $nights ?> night<?= $nights != 1 ? 's' : '' ?>
                            </span>
                            <span style="display:flex; align-items:center; gap:4px;">
                                <i class="bi bi-people" style="color:var(--rd-red);"></i>
                                <?= $b['guests'] ?> guest<?= $b['guests'] > 1 ? 's' : '' ?>
                            </span>
                        </div>
                    </div>

                    <!-- Price + Status + Actions -->
                    <div style="text-align:right; min-width:140px;">
                        <div class="price-tag" style="font-size:17px;">&#8369;<?= number_format($b['totalPrice']) ?></div>
                        <div style="margin:6px 0;"><?= $badge ?></div>
                        <div style="display:flex; gap:7px; justify-content:flex-end; margin-top:8px; flex-wrap:wrap;">
                            <a href="/customer/booking_detail.php?id=<?= $b['id'] ?>"
                               class="btn-rd-outline" style="font-size:12px; padding:5px 14px; border-radius:6px;">
                                View
                            </a>
                            <?php if ($b['status'] === 'pending'): ?>
                                <?php $needsPayment = empty($b['paymtId']); ?>
                                <?php if ($needsPayment): ?>
                                <a href="/hotels/payment.php?id=<?= $b['id'] ?>"
                                   class="btn-rd" style="font-size:12px; padding:5px 14px; border-radius:6px;">
                                    <i class="bi bi-credit-card me-1"></i>Pay Now
                                </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (in_array($b['status'], ['pending', 'confirmed'])): ?>
                                <form method="POST" onsubmit="return confirm('Cancel this booking? This cannot be undone.');" style="margin:0;">
                                    <input type="hidden" name="book_id" value="<?= $b['id'] ?>">
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
            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
