<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /auth/login.php"); exit();
}

$bookId = (int) ($_GET['id'] ?? 0);
$custId = (int) $_SESSION['customer_id'];

// Handle cancellation from this page
if (isset($_POST['cancel_booking']) && $bookId) {
    $bRow = $conn->query("SELECT Book_Status, Book_CheckIn FROM Bookings WHERE Book_Id=$bookId AND Book_CustId=$custId LIMIT 1")->fetch_assoc();
    $detailCancelMsg = '';
    if ($bRow && in_array($bRow['Book_Status'], ['pending', 'confirmed'])) {
        $hoursUntilCI = (strtotime($bRow['Book_CheckIn']) - time()) / 3600;
        if ($hoursUntilCI >= 24) {
            $conn->query("UPDATE Bookings SET Book_Status='cancelled' WHERE Book_Id=$bookId AND Book_CustId=$custId");
            $hasEarningsTable = $conn->query("SHOW TABLES LIKE 'Earnings'")->num_rows > 0;
            if ($hasEarningsTable) {
                $conn->query("UPDATE Earnings SET Earn_Status='voided' WHERE Earn_BookId=$bookId");
            }
            header("Location: dashboard.php?cancelled=1"); exit();
        } else {
            $detailCancelMsg = 'Cancellations must be made at least 24 hours before check-in.';
        }
    } else {
        $detailCancelMsg = 'This booking cannot be cancelled.';
    }
}

$booking = $conn->query("
    SELECT b.*, h.Hotel_Name, h.Hotel_City, h.Hotel_Address, h.Hotel_Rating,
           r.Room_Type, r.Room_Price, r.Room_Capacity, r.Room_Description,
           c.Cust_FName, c.Cust_LName, c.Cust_Phone,
           a.Acct_Email
    FROM Bookings b
    JOIN Hotels    h ON h.Hotel_Id  = b.Book_HotelId
    JOIN Rooms     r ON r.Room_Id   = b.Book_RoomId
    JOIN Customers c ON c.Cust_Id   = b.Book_CustId
    JOIN Accounts  a ON a.Acct_Id   = c.Cust_AcctId
    WHERE b.Book_Id=$bookId AND b.Book_CustId=$custId
    LIMIT 1
")->fetch_assoc();

if (!$booking) { header("Location: dashboard.php"); exit(); }

$nights = max(1, (new DateTime($booking['Book_CheckIn']))->diff(new DateTime($booking['Book_CheckOut']))->days);

$statusConfig = match($booking['Book_Status']) {
    'confirmed' => ['#0A3622', '#D1E7DD', 'Confirmed'],
    'cancelled' => ['#842029', '#F8D7DA', 'Cancelled'],
    'completed' => ['#084298', '#CFE2FF', 'Completed'],
    default     => ['#856404', '#FFF3CD', 'Pending'],
};
[$statusColor, $statusBg, $statusLabel] = $statusConfig;

// Fetch payment info if Payments table exists
$payment = null;
$hasPaymentsTable = $conn->query("SHOW TABLES LIKE 'Payments'")->num_rows > 0;
if ($hasPaymentsTable) {
    $payment = $conn->query("SELECT * FROM Payments WHERE Paymt_BookId=$bookId LIMIT 1")->fetch_assoc();
}

$justPaid = isset($_GET['paid']) && $_GET['paid'] == '1';

// Handle review submission
$reviewMsg = '';
$reviewError = '';
if (isset($_POST['submit_review']) && $booking['Book_Status'] === 'completed') {
    $rating  = (int) ($_POST['review_rating'] ?? 0);
    $comment = trim($conn->real_escape_string($_POST['review_comment'] ?? ''));
    if ($rating < 1 || $rating > 5) {
        $reviewError = 'Please select a rating from 1 to 5 stars.';
    } else {
        $hasReviews = $conn->query("SHOW TABLES LIKE 'Reviews'")->num_rows > 0;
        if ($hasReviews) {
            $alreadyReviewed = $conn->query("SELECT Review_Id FROM Reviews WHERE Review_BookId=$bookId LIMIT 1")->num_rows > 0;
            if ($alreadyReviewed) {
                $reviewError = 'You have already rated this booking.';
            } else {
                $hId = (int) $booking['Book_HotelId'];
                $cId = (int) $custId;
                $conn->query("
                    INSERT INTO Reviews (Review_BookId, Review_HotelId, Review_CustId, Review_Rating, Review_Comment)
                    VALUES ($bookId, $hId, $cId, $rating, '$comment')
                ");
                // Recalculate hotel average rating
                $avg = $conn->query("SELECT ROUND(AVG(Review_Rating),1) AS avg FROM Reviews WHERE Review_HotelId=$hId")->fetch_assoc()['avg'];
                $conn->query("UPDATE Hotels SET Hotel_Rating=$avg WHERE Hotel_Id=$hId");
                header("Location: booking_detail.php?id=$bookId&reviewed=1"); exit();
            }
        }
    }
}

// Check if already reviewed
$hasReviews = $conn->query("SHOW TABLES LIKE 'Reviews'")->num_rows > 0;
$existingReview = null;
if ($hasReviews) {
    $existingReview = $conn->query("SELECT * FROM Reviews WHERE Review_BookId=$bookId LIMIT 1")->fetch_assoc();
}

$justReviewed = isset($_GET['reviewed']) && $_GET['reviewed'] == '1';

$title = "Booking #" . $bookId;
include "../layout/layout.php";
$imgSeed = 'reddoorz' . $booking['Book_HotelId'];
?>

<div style="display:flex; min-height:calc(100vh - 64px);">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">
        <div style="max-width:740px;">

            <?php if ($justPaid): ?>
            <div style="background:#F0FFF4; border:1px solid #BBF7D0; border-radius:12px; padding:16px 20px; margin-bottom:24px; display:flex; align-items:center; gap:12px; color:#15803D;">
                <i class="bi bi-check-circle-fill" style="font-size:22px; flex-shrink:0;"></i>
                <div>
                    <div style="font-weight:700; font-size:15px;">Payment Successful</div>
                    <div style="font-size:13px; opacity:0.85;">Your booking has been confirmed. A summary is shown below.</div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($justReviewed): ?>
            <div style="background:#F0FFF4; border:1px solid #BBF7D0; border-radius:12px; padding:16px 20px; margin-bottom:24px; display:flex; align-items:center; gap:12px; color:#15803D;">
                <i class="bi bi-star-fill" style="font-size:22px; flex-shrink:0;"></i>
                <div>
                    <div style="font-weight:700; font-size:15px;">Thank you for your review!</div>
                    <div style="font-size:13px; opacity:0.85;">Your rating helps other guests find great hotels.</div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Header row -->
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:28px; flex-wrap:wrap; gap:12px;">
                <div>
                    <a href="dashboard.php" style="font-size:13px; color:var(--rd-muted); text-decoration:none; display:inline-flex; align-items:center; gap:5px; margin-bottom:8px;">
                        <i class="bi bi-arrow-left"></i> Back to My Bookings
                    </a>
                    <h1 style="font-size:22px; font-weight:700; margin:0;">Booking Confirmation</h1>
                    <p style="color:var(--rd-muted); font-size:14px; margin:4px 0 0;">
                        Reference #<?= str_pad($bookId, 6, '0', STR_PAD_LEFT) ?>
                    </p>
                </div>
                <span style="
                    background:<?= $statusBg ?>; color:<?= $statusColor ?>;
                    padding:8px 20px; border-radius:8px;
                    font-size:14px; font-weight:700;
                    display:inline-flex; align-items:center; gap:6px;
                ">
                    <?= $statusLabel ?>
                </span>
            </div>

            <!-- Hotel banner -->
            <div style="
                border-radius:16px; overflow:hidden; margin-bottom:20px;
                position:relative; height:160px;
            ">
                <img src="https://picsum.photos/seed/<?= $imgSeed ?>/740/320"
                     alt="" style="width:100%; height:100%; object-fit:cover;">
                <div style="position:absolute; inset:0; background:linear-gradient(to right, rgba(136,0,22,0.82) 0%, rgba(184,0,32,0.45) 60%, transparent 100%);"></div>
                <div style="position:absolute; inset:0; padding:24px 28px; display:flex; flex-direction:column; justify-content:flex-end; color:#fff;">
                    <div style="font-size:11px; opacity:0.7; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">Hotel</div>
                    <div style="font-size:22px; font-weight:700; margin-bottom:3px;"><?= htmlspecialchars($booking['Hotel_Name']) ?></div>
                    <div style="font-size:13px; opacity:0.82; display:flex; align-items:center; gap:5px;">
                        <i class="bi bi-geo-alt-fill" style="font-size:11px;"></i>
                        <?= htmlspecialchars($booking['Hotel_Address'] ?? $booking['Hotel_City']) ?>
                    </div>
                </div>
            </div>

            <!-- Details grid -->
            <div class="row g-4 mb-4">

                <!-- Stay Details -->
                <div class="col-md-6">
                    <div style="background:#fff; border-radius:14px; padding:22px; box-shadow:var(--rd-shadow); height:100%; border:1px solid rgba(228,223,223,0.5);">
                        <h6 style="font-size:11px; text-transform:uppercase; letter-spacing:0.6px; color:var(--rd-muted); margin-bottom:16px;">Stay Details</h6>
                        <div style="display:flex; flex-direction:column; gap:11px; font-size:14px;">
                            <?php
                            $stayDetails = [
                                ['Room Type',  htmlspecialchars($booking['Room_Type'])],
                                ['Check-in',   date('D, M d Y', strtotime($booking['Book_CheckIn']))],
                                ['Check-out',  date('D, M d Y', strtotime($booking['Book_CheckOut']))],
                                ['Duration',   $nights . ' night' . ($nights != 1 ? 's' : '')],
                                ['Guests',     $booking['Book_Guests'] . ' guest' . ($booking['Book_Guests'] > 1 ? 's' : '')],
                            ];
                            foreach ($stayDetails as [$lbl, $val]):
                            ?>
                            <div style="display:flex; justify-content:space-between; gap:10px;">
                                <span style="color:var(--rd-muted);"><?= $lbl ?></span>
                                <span style="font-weight:600; text-align:right;"><?= $val ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Price Breakdown -->
                <div class="col-md-6">
                    <div style="background:#fff; border-radius:14px; padding:22px; box-shadow:var(--rd-shadow); height:100%; border:1px solid rgba(228,223,223,0.5);">
                        <h6 style="font-size:11px; text-transform:uppercase; letter-spacing:0.6px; color:var(--rd-muted); margin-bottom:16px;">Price Breakdown</h6>
                        <div style="display:flex; flex-direction:column; gap:11px; font-size:14px;">
                            <div style="display:flex; justify-content:space-between;">
                                <span style="color:var(--rd-muted);">Room rate</span>
                                <span>&#8369;<?= number_format($booking['Room_Price']) ?>/night</span>
                            </div>
                            <div style="display:flex; justify-content:space-between;">
                                <span style="color:var(--rd-muted);">Nights</span>
                                <span>&times; <?= $nights ?></span>
                            </div>
                            <div style="display:flex; justify-content:space-between;">
                                <span style="color:var(--rd-muted);">Taxes &amp; fees</span>
                                <span style="color:#888;">Included</span>
                            </div>
                            <hr style="margin:4px 0; border-color:var(--rd-border);">
                            <div style="display:flex; justify-content:space-between;">
                                <span style="font-weight:700; color:#111;">Total Paid</span>
                                <span class="price-tag" style="font-size:18px;">&#8369;<?= number_format($booking['Book_TotalPrice']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Guest Info -->
            <div style="background:#fff; border-radius:14px; padding:22px; box-shadow:var(--rd-shadow); margin-bottom:20px; border:1px solid rgba(228,223,223,0.5);">
                <h6 style="font-size:11px; text-transform:uppercase; letter-spacing:0.6px; color:var(--rd-muted); margin-bottom:16px;">Guest Information</h6>
                <div class="row g-3" style="font-size:14px;">
                    <div class="col-sm-6">
                        <span style="color:var(--rd-muted); font-size:12px;">Full Name</span>
                        <div style="font-weight:600; margin-top:2px;">
                            <?= htmlspecialchars($booking['Cust_FName'] . ' ' . $booking['Cust_LName']) ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <span style="color:var(--rd-muted); font-size:12px;">Email</span>
                        <div style="font-weight:600; margin-top:2px;">
                            <?= htmlspecialchars($booking['Acct_Email']) ?>
                        </div>
                    </div>
                    <?php if ($booking['Cust_Phone']): ?>
                    <div class="col-sm-6">
                        <span style="color:var(--rd-muted); font-size:12px;">Phone</span>
                        <div style="font-weight:600; margin-top:2px;">
                            <?= htmlspecialchars($booking['Cust_Phone']) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="col-sm-6">
                        <span style="color:var(--rd-muted); font-size:12px;">Booked On</span>
                        <div style="font-weight:600; margin-top:2px;">
                            <?= date('M d, Y', strtotime($booking['Book_CreatedAt'])) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <?php if ($payment): ?>
            <?php
            $methodLabels = [
                'gcash'          => ['GCash',              'bi-phone',         '#00A650', '#ECFDF5'],
                'maya'           => ['Maya',               'bi-phone-fill',    '#0066CC', '#EFF6FF'],
                'credit_card'    => ['Credit / Debit Card','bi-credit-card',   '#7C3AED', '#F5F3FF'],
                'pay_at_hotel'   => ['Pay at Hotel',       'bi-building-check','#92400E', '#FFFBEB'],
            ];
            [$mLabel, $mIcon, $mColor, $mBg] = $methodLabels[$payment['Paymt_Method']] ?? [$payment['Paymt_Method'], 'bi-cash', '#555', '#F5F5F5'];
            $isPaid = $payment['Paymt_Status'] === 'paid';
            ?>
            <div style="background:#fff; border-radius:14px; padding:22px; box-shadow:var(--rd-shadow); margin-bottom:20px; border:1px solid rgba(228,223,223,0.5);">
                <h6 style="font-size:11px; text-transform:uppercase; letter-spacing:0.6px; color:var(--rd-muted); margin-bottom:16px;">Payment Information</h6>
                <div style="display:flex; flex-direction:column; gap:12px; font-size:14px;">

                    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
                        <span style="color:var(--rd-muted);">Method</span>
                        <span style="display:inline-flex; align-items:center; gap:7px; background:<?= $mBg ?>; color:<?= $mColor ?>; border-radius:6px; padding:4px 10px; font-weight:600; font-size:13px;">
                            <i class="bi <?= $mIcon ?>"></i><?= $mLabel ?>
                        </span>
                    </div>

                    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
                        <span style="color:var(--rd-muted);">Payment Status</span>
                        <?php if ($isPaid): ?>
                        <span style="background:#D1E7DD; color:#0A3622; border-radius:6px; padding:4px 10px; font-weight:700; font-size:13px;">
                            <i class="bi bi-check-circle me-1"></i>Paid
                        </span>
                        <?php else: ?>
                        <span style="background:#FFF3CD; color:#856404; border-radius:6px; padding:4px 10px; font-weight:700; font-size:13px;">
                            <i class="bi bi-clock me-1"></i>Pay at Hotel
                        </span>
                        <?php endif; ?>
                    </div>

                    <?php if ($payment['Paymt_RefCode']): ?>
                    <div style="display:flex; justify-content:space-between; gap:10px;">
                        <span style="color:var(--rd-muted);">Reference No.</span>
                        <span style="font-weight:600; font-family:monospace;"><?= htmlspecialchars($payment['Paymt_RefCode']) ?></span>
                    </div>
                    <?php endif; ?>

                    <div style="display:flex; justify-content:space-between; gap:10px;">
                        <span style="color:var(--rd-muted);">Amount</span>
                        <span class="price-tag" style="font-size:16px;">&#8369;<?= number_format($payment['Paymt_Amount'], 2) ?></span>
                    </div>

                    <div style="display:flex; justify-content:space-between; gap:10px;">
                        <span style="color:var(--rd-muted);">Date</span>
                        <span style="font-weight:600;"><?= date('M d, Y g:i A', strtotime($payment['Paymt_Date'])) ?></span>
                    </div>

                </div>
            </div>
            <?php elseif ($booking['Book_Status'] === 'pending'): ?>
            <div style="background:#FFF8E1; border:1px solid #FFE082; color:#7B5800; border-radius:12px; padding:14px 18px; margin-bottom:20px; font-size:13px; display:flex; align-items:center; gap:10px;">
                <i class="bi bi-exclamation-triangle" style="font-size:16px; flex-shrink:0;"></i>
                <span>Payment not yet completed. <a href="/hotels/payment.php?id=<?= $bookId ?>" style="color:#B80020; font-weight:600;">Complete payment now</a></span>
            </div>
            <?php endif; ?>

            <!-- Actions -->
            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <a href="dashboard.php" class="btn-rd-outline" style="padding:10px 24px; font-size:14px;">
                    <i class="bi bi-arrow-left me-1"></i>All Bookings
                </a>
                <a href="/hotels/search.php" class="btn-rd" style="padding:10px 24px; font-size:14px;">
                    <i class="bi bi-search me-1"></i>Book Another Stay
                </a>
                <?php
                $checkInTs = strtotime($booking['Book_CheckIn']);
                $hoursUntilCI = ($checkInTs - time()) / 3600;
                $canCancel = in_array($booking['Book_Status'], ['pending', 'confirmed']) && $hoursUntilCI >= 24;
                if ($canCancel):
                ?>
                <button type="button" class="btn btn-outline-danger" style="font-size:14px; padding:10px 24px; border-radius:8px; font-family:'DM Sans',sans-serif; font-weight:600;"
                        data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="bi bi-x-circle me-1"></i>Cancel Booking
                </button>
                <?php endif; ?>
            </div>

            <?php if (in_array($booking['Book_Status'], ['pending','confirmed']) && $hoursUntilCI < 24 && $hoursUntilCI >= 0): ?>
            <div class="alert-rd-danger mt-3" style="display:flex; align-items:center; gap:9px;">
                <i class="bi bi-clock"></i>
                Cancellation is no longer available — check-in is less than 24 hours away.
            </div>
            <?php endif; ?>

            <?php if ($booking['Book_Status'] === 'completed'): ?>
            <!-- ===== Rate This Hotel ===== -->
            <div style="background:#fff; border-radius:14px; padding:22px; box-shadow:var(--rd-shadow); margin-top:24px; border:1px solid rgba(228,223,223,0.5);">
                <h6 style="font-size:13px; font-weight:700; margin-bottom:14px; display:flex; align-items:center; gap:7px;">
                    <i class="bi bi-star-half" style="color:var(--rd-red);"></i> Rate Your Stay
                </h6>

                <?php if ($existingReview): ?>
                    <!-- Already reviewed -->
                    <div style="display:flex; align-items:flex-start; gap:16px;">
                        <div>
                            <div style="display:flex; gap:3px; margin-bottom:6px;">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                <i class="bi bi-star<?= $s <= $existingReview['Review_Rating'] ? '-fill' : '' ?>"
                                   style="font-size:20px; color:<?= $s <= $existingReview['Review_Rating'] ? '#C98A00' : '#DDD' ?>;"></i>
                                <?php endfor; ?>
                                <span style="font-size:14px; font-weight:700; color:#333; margin-left:6px; line-height:22px;"><?= $existingReview['Review_Rating'] ?>/5</span>
                            </div>
                            <?php if ($existingReview['Review_Comment']): ?>
                            <p style="font-size:13px; color:#555; margin:0; line-height:1.6;">
                                &ldquo;<?= htmlspecialchars($existingReview['Review_Comment']) ?>&rdquo;
                            </p>
                            <?php endif; ?>
                            <div style="font-size:11px; color:#aaa; margin-top:6px;">
                                Reviewed on <?= date('M d, Y', strtotime($existingReview['Review_CreatedAt'])) ?>
                            </div>
                        </div>
                    </div>

                <?php elseif ($reviewError): ?>
                    <div class="alert-rd-danger mb-3" style="display:flex; align-items:center; gap:9px; font-size:13px;">
                        <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($reviewError) ?>
                    </div>
                    <button type="button" class="btn-rd" style="padding:9px 22px; font-size:13px;"
                            data-bs-toggle="modal" data-bs-target="#rateModal">
                        <i class="bi bi-star me-1"></i>Rate This Hotel
                    </button>

                <?php else: ?>
                    <p style="font-size:13px; color:var(--rd-muted); margin:0 0 14px;">
                        How was your stay at <strong><?= htmlspecialchars($booking['Hotel_Name']) ?></strong>? Your review helps other guests.
                    </p>
                    <button type="button" class="btn-rd" style="padding:9px 22px; font-size:13px;"
                            data-bs-toggle="modal" data-bs-target="#rateModal">
                        <i class="bi bi-star me-1"></i>Rate This Hotel
                    </button>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- ===== Cancel Booking Modal ===== -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.18);">
            <div class="modal-header" style="border-bottom:1px solid var(--rd-border); padding:20px 24px;">
                <h5 class="modal-title" id="cancelModalLabel" style="font-size:16px; font-weight:700; color:#111;">
                    <i class="bi bi-exclamation-triangle-fill me-2" style="color:#D97706;"></i>Cancel Booking
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <p style="font-size:14px; color:#555; margin-bottom:16px; line-height:1.65;">
                    Are you sure you want to cancel booking <strong style="color:#111;">#<?= str_pad($bookId, 6, '0', STR_PAD_LEFT) ?></strong>?
                </p>
                <div style="background:#FFF8E1; border:1px solid #FFE082; border-radius:8px; padding:12px 14px; font-size:13px; color:#7B5800; display:flex; align-items:flex-start; gap:8px;">
                    <i class="bi bi-info-circle" style="flex-shrink:0; margin-top:1px;"></i>
                    <span>This action cannot be undone. Cancellations are only allowed at least 24 hours before check-in.</span>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--rd-border); padding:16px 24px; gap:10px;">
                <button type="button" class="btn-rd-outline" data-bs-dismiss="modal" style="padding:9px 22px; font-size:14px;">
                    Keep Booking
                </button>
                <form method="POST" style="margin:0;">
                    <button type="submit" name="cancel_booking" class="btn-rd" style="padding:9px 22px; font-size:14px; background:#C0392B;">
                        <i class="bi bi-x-circle me-1"></i>Yes, Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($booking['Book_Status'] === 'completed' && !$existingReview): ?>
<!-- ===== Rate Hotel Modal ===== -->
<div class="modal fade" id="rateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.18);">
            <div class="modal-header" style="border-bottom:1px solid var(--rd-border); padding:20px 24px;">
                <h5 class="modal-title" style="font-size:16px; font-weight:700;">
                    <i class="bi bi-star-fill me-2" style="color:#C98A00;"></i>Rate Your Stay
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body" style="padding:24px;">
                    <p style="font-size:13px; color:var(--rd-muted); margin-bottom:20px;">
                        How would you rate <strong><?= htmlspecialchars($booking['Hotel_Name']) ?></strong>?
                    </p>

                    <!-- Star picker -->
                    <div style="display:flex; justify-content:center; gap:8px; margin-bottom:20px;" id="starPicker">
                        <?php for ($s = 1; $s <= 5; $s++): ?>
                        <i class="bi bi-star" data-val="<?= $s ?>"
                           style="font-size:36px; color:#DDD; cursor:pointer; transition:color 0.12s;"
                           onclick="setRating(<?= $s ?>)"
                           onmouseover="hoverRating(<?= $s ?>)"
                           onmouseout="resetHover()"></i>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="review_rating" id="review_rating" value="0">
                    <div id="ratingLabel" style="text-align:center; font-size:13px; color:var(--rd-muted); margin-bottom:18px;">
                        Click a star to rate
                    </div>

                    <div class="mb-2">
                        <label class="form-label" style="font-size:13px;">Comment <span style="color:#aaa;">(optional)</span></label>
                        <textarea name="review_comment" class="form-control" rows="3"
                                  placeholder="Share your experience..." maxlength="500"
                                  style="font-size:13px; resize:vertical;"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--rd-border); padding:16px 24px;">
                    <button type="button" class="btn-rd-outline" data-bs-dismiss="modal" style="padding:9px 22px;">Cancel</button>
                    <button type="submit" name="submit_review" id="submitReviewBtn" class="btn-rd" style="padding:9px 22px;" disabled>
                        <i class="bi bi-check-lg me-1"></i>Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const ratingLabels = ['','Terrible','Poor','Okay','Good','Excellent'];
let selectedRating = 0;

function setRating(val) {
    selectedRating = val;
    document.getElementById('review_rating').value = val;
    document.getElementById('ratingLabel').textContent = ratingLabels[val] + ' (' + val + '/5)';
    document.getElementById('ratingLabel').style.color = '#C98A00';
    document.getElementById('submitReviewBtn').disabled = false;
    paintStars(val, true);
}

function hoverRating(val) { paintStars(val, false); }

function resetHover() { paintStars(selectedRating, true); }

function paintStars(upTo, selected) {
    document.querySelectorAll('#starPicker i').forEach(function(el) {
        const v = parseInt(el.dataset.val);
        if (v <= upTo) {
            el.classList.replace('bi-star', 'bi-star-fill');
            el.style.color = '#C98A00';
        } else {
            el.classList.replace('bi-star-fill', 'bi-star');
            el.style.color = '#DDD';
        }
    });
}
</script>
<?php endif; ?>

<?php include "../layout/footer.php"; ?>


