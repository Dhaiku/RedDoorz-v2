<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /auth/login.php?redirect=book"); exit();
}

$roomId   = (int) ($_GET['room']  ?? $_POST['room_id']  ?? 0);
$hotelId  = (int) ($_GET['hotel'] ?? $_POST['hotel_id'] ?? 0);
$checkin  = $_GET['checkin']  ?? $_POST['checkin']  ?? '';
$checkout = $_GET['checkout'] ?? $_POST['checkout'] ?? '';

if (!$roomId || !$hotelId) { header("Location: search.php"); exit(); }

$room = $conn->query("
    SELECT r.*, h.Hotel_Name, h.Hotel_City, h.Hotel_Address
    FROM Rooms r
    JOIN Hotels h ON h.Hotel_Id = r.Room_HotelId
    WHERE r.Room_Id=$roomId AND r.Room_HotelId=$hotelId
    LIMIT 1
")->fetch_assoc();

if (!$room) { header("Location: search.php"); exit(); }

$error = "";

if (isset($_POST['confirm_booking'])) {
    $checkin  = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $guests   = (int) $_POST['guests'];

    $dtIn    = new DateTime($checkin);
    $dtOut   = new DateTime($checkout);
    $dtToday = new DateTime(date('Y-m-d'));
    $dtMax   = new DateTime(date('Y-m-d', strtotime('+2 years')));
    $nights  = max(1, $dtIn->diff($dtOut)->days);

    if ($dtIn < $dtToday) {
        $error = "Check-in date cannot be in the past.";

    } elseif ($dtOut <= $dtIn) {
        $error = "Check-out date must be after check-in date.";

    } elseif ($dtOut < $dtToday) {
        $error = "Check-out date cannot be in the past.";

    } elseif ($dtIn > $dtMax || $dtOut > $dtMax) {
        $error = "Dates cannot be more than 2 years in the future.";

    } elseif ($guests < 1 || $guests > $room['Room_Capacity']) {
        $error = "Guests must be between 1 and {$room['Room_Capacity']}.";

    } else {
        // =====================================================
        // DATE CONFLICT CHECK
        // Find any active booking for this room that overlaps
        // with the requested dates. Overlap condition:
        //   existing_checkin  < requested_checkout
        //   existing_checkout > requested_checkin
        // =====================================================
        $ciEsc = $conn->real_escape_string($checkin);
        $coEsc = $conn->real_escape_string($checkout);

        $conflict = $conn->query("
            SELECT COUNT(*) AS cnt
            FROM Bookings
            WHERE Book_RoomId  = $roomId
              AND Book_Status  NOT IN ('cancelled')
              AND Book_CheckIn  < '$coEsc'
              AND Book_CheckOut > '$ciEsc'
        ")->fetch_assoc()['cnt'];

        // Check BlockedDates (maintenance / walk-in blocks set by owner)
        $hasBlockTable = $conn->query("SHOW TABLES LIKE 'BlockedDates'")->num_rows > 0;
        $blockedConflict = 0;
        if ($hasBlockTable) {
            $blockedConflict = $conn->query("
                SELECT COUNT(*) AS cnt
                FROM BlockedDates
                WHERE Block_RoomId  = $roomId
                  AND Block_DateFrom < '$coEsc'
                  AND Block_DateTo   > '$ciEsc'
            ")->fetch_assoc()['cnt'];
        }

        if ($conflict > 0) {
            $error = "This room is already booked for the selected dates. Please choose different dates or another room.";

        } elseif ($blockedConflict > 0) {
            $error = "This room is unavailable for the selected dates (blocked for maintenance or walk-in). Please choose different dates.";

        } else {
            // Generate unique booking reference code
            $refCode = 'RD-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

            $custId     = (int) $_SESSION['customer_id'];
            $totalPrice = $room['Room_Price'] * $nights;

            // Check if Book_RefCode column exists (migration may not have run yet)
            $hasRefCol = $conn->query("SHOW COLUMNS FROM Bookings LIKE 'Book_RefCode'")->num_rows > 0;

            if ($hasRefCol) {
                $conn->query("
                    INSERT INTO Bookings
                        (Book_CustId, Book_HotelId, Book_RoomId, Book_CheckIn, Book_CheckOut,
                         Book_Guests, Book_TotalPrice, Book_RefCode)
                    VALUES
                        ($custId, $hotelId, $roomId, '$ciEsc', '$coEsc',
                         $guests, $totalPrice, '$refCode')
                ");
            } else {
                $conn->query("
                    INSERT INTO Bookings
                        (Book_CustId, Book_HotelId, Book_RoomId, Book_CheckIn, Book_CheckOut,
                         Book_Guests, Book_TotalPrice)
                    VALUES
                        ($custId, $hotelId, $roomId, '$ciEsc', '$coEsc',
                         $guests, $totalPrice)
                ");
            }

            $bookingId = $conn->insert_id;
            header("Location: /hotels/payment.php?id=$bookingId");
            exit();
        }
    }
}

// Calculate nights for display
$nights = 1;
if ($checkin && $checkout) {
    $dtIn  = new DateTime($checkin);
    $dtOut = new DateTime($checkout);
    $nights = max(1, $dtIn->diff($dtOut)->days);
}
$total = $room['Room_Price'] * $nights;

// Check for existing conflicting dates to show a warning upfront
$conflictWarning = false;
if ($checkin && $checkout) {
    $ciEsc = $conn->real_escape_string($checkin);
    $coEsc = $conn->real_escape_string($checkout);
    $conflictCheck = $conn->query("
        SELECT COUNT(*) AS cnt
        FROM Bookings
        WHERE Book_RoomId  = $roomId
          AND Book_Status  NOT IN ('cancelled')
          AND Book_CheckIn  < '$coEsc'
          AND Book_CheckOut > '$ciEsc'
    ")->fetch_assoc()['cnt'];
    $conflictWarning = $conflictCheck > 0;
}

$title = "Book — " . $room['Hotel_Name'];
include "../layout/layout.php";
?>

<div class="container" style="padding:36px 12px 80px; max-width:920px;">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-rd mb-4">
        <a href="/index.php">Home</a>
        <i class="bi bi-chevron-right" style="font-size:10px;"></i>
        <a href="/hotels/hotel_detail.php?id=<?= $hotelId ?>"><?= htmlspecialchars($room['Hotel_Name']) ?></a>
        <i class="bi bi-chevron-right" style="font-size:10px;"></i>
        <span style="color:#333;">Complete Booking</span>
    </nav>

    <div class="row g-4">

        <!-- ===== LEFT: Booking Form ===== -->
        <div class="col-lg-7" data-aos="fade-right">
            <div style="background:#fff; border-radius:14px; padding:28px; box-shadow:var(--rd-shadow); border:1px solid rgba(228,223,223,0.5);">
                <h4 style="font-size:18px; font-weight:700; margin-bottom:5px;">Complete Your Booking</h4>
                <p style="font-size:13px; color:var(--rd-muted); margin-bottom:20px; border-bottom:1px solid var(--rd-border); padding-bottom:16px;">
                    You will be taken to the payment page after confirming your details.
                </p>

                <?php if ($error): ?>
                    <div class="alert-rd-danger mb-4">
                        <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($conflictWarning && !$error): ?>
                    <div style="background:#FFF8E1; border:1px solid #FFE082; color:#7B5800; border-radius:8px; padding:12px 16px; font-size:13px; margin-bottom:18px;">
                        <i class="bi bi-calendar-x me-2"></i>
                        This room has existing bookings near your selected dates. Please verify your dates carefully.
                    </div>
                <?php endif; ?>

                <form method="POST" id="bookingForm">
                    <input type="hidden" name="room_id"  value="<?= $roomId ?>">
                    <input type="hidden" name="hotel_id" value="<?= $hotelId ?>">

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Check-in Date <span style="color:var(--rd-red)">*</span></label>
                            <input type="date" name="checkin" id="checkin" class="form-control"
                                   min="<?= date('Y-m-d') ?>"
                                   max="<?= date('Y-m-d', strtotime('+2 years')) ?>"
                                   value="<?= htmlspecialchars($checkin ?: date('Y-m-d')) ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Check-out Date <span style="color:var(--rd-red)">*</span></label>
                            <input type="date" name="checkout" id="checkout" class="form-control"
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                   max="<?= date('Y-m-d', strtotime('+2 years')) ?>"
                                   value="<?= htmlspecialchars($checkout) ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Number of Guests <span style="color:var(--rd-red)">*</span></label>
                        <select name="guests" class="form-select" required>
                            <?php for ($g = 1; $g <= $room['Room_Capacity']; $g++): ?>
                                <option value="<?= $g ?>"><?= $g ?> Guest<?= $g > 1 ? 's' : '' ?></option>
                            <?php endfor; ?>
                        </select>
                        <div style="font-size:12px; color:#aaa; margin-top:5px;">Max capacity: <?= $room['Room_Capacity'] ?> guests</div>
                    </div>

                    <!-- Booking for -->
                    <div style="background:var(--rd-bg); border-radius:10px; padding:14px 16px; margin-bottom:22px; font-size:14px; border:1px solid var(--rd-border);">
                        <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:var(--rd-muted); margin-bottom:6px;">Booking For</div>
                        <div style="display:flex; align-items:center; gap:9px; color:#444;">
                            <i class="bi bi-person-circle" style="color:var(--rd-red); font-size:18px;"></i>
                            <span>
                                <strong><?= htmlspecialchars($_SESSION['display_name'] ?? '') ?></strong>
                                <span style="color:var(--rd-muted); margin-left:6px;"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></span>
                            </span>
                        </div>
                    </div>

                    <!-- Availability notice -->
                    <div style="background:#F0FFF4; border:1px solid #BBF7D0; border-radius:8px; padding:11px 14px; font-size:12px; color:#15803D; margin-bottom:20px; display:flex; align-items:center; gap:7px;">
                        <i class="bi bi-shield-check" style="font-size:14px;"></i>
                        Room availability will be verified before your booking is confirmed.
                    </div>

                    <button type="submit" name="confirm_booking" class="btn-rd w-100"
                            style="justify-content:center; padding:13px; font-size:15px;">
                        <i class="bi bi-lock me-1"></i>Continue to Payment
                    </button>
                </form>
            </div>
        </div>

        <!-- ===== RIGHT: Price Summary ===== -->
        <div class="col-lg-5" data-aos="fade-left">
            <div style="background:#fff; border-radius:14px; padding:24px; box-shadow:var(--rd-shadow); position:sticky; top:80px; border:1px solid rgba(228,223,223,0.5);">

                <!-- Hotel card -->
                <div style="
                    background: linear-gradient(135deg, #880016 0%, #B80020 100%);
                    border-radius:10px; padding:18px 20px; color:#fff; margin-bottom:18px;
                ">
                    <div style="font-size:11px; opacity:0.7; margin-bottom:2px; text-transform:uppercase; letter-spacing:0.6px;">
                        <?= htmlspecialchars($room['Hotel_City']) ?>
                    </div>
                    <div style="font-size:16px; font-weight:700; line-height:1.3; margin-bottom:2px;">
                        <?= htmlspecialchars($room['Hotel_Name']) ?>
                    </div>
                    <div style="font-size:13px; opacity:0.8;">
                        <?= htmlspecialchars($room['Room_Type']) ?>
                    </div>
                </div>

                <!-- Price breakdown -->
                <div style="font-size:14px; color:#555;">
                    <div style="display:flex; justify-content:space-between; padding:9px 0; border-bottom:1px solid #F0EEE8;">
                        <span>Room rate</span>
                        <span style="font-weight:600;">&#8369;<?= number_format($room['Room_Price']) ?>/night</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:9px 0; border-bottom:1px solid #F0EEE8;" id="nightsRow">
                        <span>Duration</span>
                        <span style="font-weight:600;" id="nightsDisplay"><?= $nights ?> night<?= $nights != 1 ? 's' : '' ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:9px 0; border-bottom:1px solid #F0EEE8;">
                        <span>Taxes &amp; fees</span>
                        <span style="color:#888;">Included</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:12px 0;">
                        <span style="font-weight:700; font-size:15px; color:#111;">Total</span>
                        <span class="price-tag" id="totalDisplay">&#8369;<?= number_format($total) ?></span>
                    </div>
                </div>

                <!-- Room info -->
                <div style="display:flex; flex-direction:column; gap:8px; font-size:12px; color:#777; border-top:1px solid var(--rd-border); padding-top:14px;">
                    <span style="display:flex; align-items:center; gap:7px;">
                        <i class="bi bi-people" style="color:var(--rd-red);"></i>Max <?= $room['Room_Capacity'] ?> guests
                    </span>
                    <span style="display:flex; align-items:center; gap:7px;">
                        <i class="bi bi-wifi" style="color:var(--rd-red);"></i>Free WiFi included
                    </span>
                    <span style="display:flex; align-items:center; gap:7px;">
                        <i class="bi bi-arrow-counterclockwise" style="color:var(--rd-red);"></i>Free cancellation before check-in
                    </span>
                    <span style="display:flex; align-items:center; gap:7px;">
                        <i class="bi bi-lock" style="color:var(--rd-red);"></i>Secure payment on next step
                    </span>
                </div>

            </div>
        </div>

    </div>
</div>


<script>
const pricePerNight = <?= $room['Room_Price'] ?>;
function recalc() {
    const ci = document.getElementById('checkin').value;
    const co = document.getElementById('checkout').value;
    if (!ci || !co) return;
    const nights = Math.max(1, Math.round((new Date(co) - new Date(ci)) / 86400000));
    document.getElementById('nightsDisplay').textContent = nights + ' night' + (nights !== 1 ? 's' : '');
    document.getElementById('totalDisplay').textContent  = '₱' + (pricePerNight * nights).toLocaleString();
}
document.getElementById('checkin').addEventListener('change', function() {
    const ci = this.value;
    if (ci) {
        // checkout min = checkin + 1 day
        const nextDay = new Date(ci);
        nextDay.setDate(nextDay.getDate() + 1);
        const minCo = nextDay.toISOString().split('T')[0];
        const coInput = document.getElementById('checkout');
        coInput.min = minCo;
        // if checkout is now before the new min, clear it
        if (coInput.value && coInput.value <= ci) {
            coInput.value = '';
        }
    }
    recalc();
});
document.getElementById('checkout').addEventListener('change', recalc);
</script>

<?php include "../layout/footer.php"; ?>
