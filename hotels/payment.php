<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /auth/login.php"); exit();
}

$bookingId = (int) ($_GET['id'] ?? 0);
$custId    = (int) $_SESSION['customer_id'];

if (!$bookingId) { header("Location: /customer/dashboard.php"); exit(); }

// Fetch booking — must belong to this customer and still be pending
$booking = $conn->query("
    SELECT b.*, h.Hotel_Name, h.Hotel_City, h.Hotel_Address,
           r.Room_Type, r.Room_Price, r.Room_Capacity
    FROM Bookings b
    JOIN Hotels h ON h.Hotel_Id = b.Book_HotelId
    JOIN Rooms  r ON r.Room_Id  = b.Book_RoomId
    WHERE b.Book_Id = $bookingId
      AND b.Book_CustId = $custId
    LIMIT 1
")->fetch_assoc();

if (!$booking) { header("Location: /customer/dashboard.php"); exit(); }

// Already paid — redirect straight to detail
if ($booking['Book_Status'] === 'confirmed' || $booking['Book_Status'] === 'completed') {
    header("Location: /customer/booking_detail.php?id=$bookingId"); exit();
}

// Cancelled booking — nothing to pay
if ($booking['Book_Status'] === 'cancelled') {
    header("Location: /customer/dashboard.php"); exit();
}

$nights = max(1, (new DateTime($booking['Book_CheckIn']))->diff(new DateTime($booking['Book_CheckOut']))->days);
$total  = $booking['Book_TotalPrice'];

$error   = "";
$success = false;

// =====================================================
// PROCESS PAYMENT SUBMISSION
// =====================================================
if (isset($_POST['pay'])) {
    $method = $conn->real_escape_string($_POST['method'] ?? '');
    $valid  = in_array($method, ['gcash', 'maya', 'credit_card', 'pay_at_hotel']);

    if (!$valid) {
        $error = "Please select a valid payment method.";

    } else {
        $refCode    = "";
        $paymtStatus = "paid";

        // Validate method-specific fields
        if ($method === 'gcash' || $method === 'maya') {
            $fieldKey = $method === 'gcash' ? 'gcash_number' : 'maya_number';
            $phone = preg_replace('/\D/', '', $_POST[$fieldKey] ?? '');
            if (strlen($phone) !== 11) {
                $label = $method === 'gcash' ? 'GCash' : 'Maya';
                $error = "Please enter a valid 11-digit $label mobile number.";
            } else {
                // Store in xxxx-xxx-xxxx format
                $formatted = substr($phone,0,4).'-'.substr($phone,4,3).'-'.substr($phone,7,4);
                $refCode = $conn->real_escape_string($formatted);
            }

        } elseif ($method === 'credit_card') {
            $cardNum = preg_replace('/\s+/', '', $_POST['card_number'] ?? '');
            $expiry  = trim($_POST['card_expiry'] ?? '');
            $cvv     = trim($_POST['card_cvv']    ?? '');
            if (strlen($cardNum) < 13 || empty($expiry) || !preg_match('/^\d{3}$/', $cvv)) {
                $error = "Please fill in all card details correctly.";
            } else {
                // Simulated — store only last 4 digits
                $last4   = substr($cardNum, -4);
                $refCode = $conn->real_escape_string("CARD-****$last4");
            }

        } elseif ($method === 'pay_at_hotel') {
            $paymtStatus = "pending_collection";
            $refCode     = "";
        }

        if (!$error) {
            // Check if Payments table exists (migration may not have run)
            $hasPaymtTable = $conn->query("SHOW TABLES LIKE 'Payments'")->num_rows > 0;

            if ($hasPaymtTable) {
                $conn->query("
                    INSERT INTO Payments
                        (Paymt_BookId, Paymt_Amount, Paymt_Method, Paymt_Status, Paymt_RefCode)
                    VALUES
                        ($bookingId, $total, '$method', '$paymtStatus',
                         " . ($refCode ? "'$refCode'" : "NULL") . ")
                ");
            }

            // Confirm the booking
            $conn->query("UPDATE Bookings SET Book_Status='confirmed' WHERE Book_Id=$bookingId");

            header("Location: /customer/booking_detail.php?id=$bookingId&paid=1");
            exit();
        }
    }
}

$title = "Payment — " . $booking['Hotel_Name'];
include "../layout/layout.php";

// Ref code display
$refCode = $booking['Book_RefCode'] ?? ('RD-' . strtoupper(substr(md5($bookingId . 'rd'), 0, 8)));
$imgSeed = 'reddoorz' . $booking['Book_HotelId'];
?>

<style>
/* =============================================
   PAYMENT METHOD CARDS
============================================= */
.method-card {
    border: 2px solid var(--rd-border);
    border-radius: 12px;
    padding: 16px 18px;
    cursor: pointer;
    transition: border-color 0.18s, background 0.18s, box-shadow 0.18s;
    display: flex;
    align-items: center;
    gap: 14px;
    background: #fff;
    user-select: none;
}

.method-card:hover {
    border-color: var(--rd-red);
    background: var(--rd-red-pale);
}

.method-card.selected {
    border-color: var(--rd-red);
    background: var(--rd-red-pale);
    box-shadow: 0 0 0 3px rgba(184,0,32,0.08);
}

.method-card input[type="radio"] { display: none; }

.method-icon {
    width: 44px; height: 44px; flex-shrink: 0;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
}

.method-details-panel {
    display: none;
    margin-top: 14px;
    padding: 16px;
    background: var(--rd-bg);
    border-radius: 10px;
    border: 1px solid var(--rd-border);
}

.method-details-panel.visible { display: block; }

/* Step indicator */
.step-bar {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 32px;
}

.step-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 500;
    color: var(--rd-muted);
}

.step-item.done  .step-num { background: #16A34A; color: #fff; }
.step-item.active .step-num { background: var(--rd-red); color: #fff; }
.step-item.active { color: var(--rd-red); font-weight: 700; }

.step-num {
    width: 26px; height: 26px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700;
    background: #DDD; color: #888;
    flex-shrink: 0;
}

.step-line {
    flex: 1; height: 1px; background: var(--rd-border);
    margin: 0 10px;
}
</style>

<div class="container" style="padding:36px 12px 80px; max-width:960px;">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-rd mb-3">
        <a href="/index.php">Home</a>
        <i class="bi bi-chevron-right" style="font-size:10px;"></i>
        <a href="/hotels/hotel_detail.php?id=<?= $booking['Book_HotelId'] ?>"><?= htmlspecialchars($booking['Hotel_Name']) ?></a>
        <i class="bi bi-chevron-right" style="font-size:10px;"></i>
        <span style="color:#333;">Payment</span>
    </nav>

    <!-- Step progress bar -->
    <div class="step-bar">
        <div class="step-item done">
            <div class="step-num"><i class="bi bi-check" style="font-size:13px;"></i></div>
            <span>Select Room</span>
        </div>
        <div class="step-line"></div>
        <div class="step-item done">
            <div class="step-num"><i class="bi bi-check" style="font-size:13px;"></i></div>
            <span>Booking Details</span>
        </div>
        <div class="step-line"></div>
        <div class="step-item active">
            <div class="step-num">3</div>
            <span>Payment</span>
        </div>
        <div class="step-line"></div>
        <div class="step-item">
            <div class="step-num">4</div>
            <span>Confirmed</span>
        </div>
    </div>

    <div class="row g-4">

        <!-- ===== LEFT: Payment Form ===== -->
        <div class="col-lg-7">

            <div style="background:#fff; border-radius:14px; padding:28px; box-shadow:var(--rd-shadow); border:1px solid rgba(228,223,223,0.5);">

                <h4 style="font-size:18px; font-weight:700; margin-bottom:4px;">Choose Payment Method</h4>
                <p style="font-size:13px; color:var(--rd-muted); margin-bottom:24px; padding-bottom:18px; border-bottom:1px solid var(--rd-border);">
                    Your booking reference is <strong style="color:var(--rd-red);"><?= htmlspecialchars($refCode) ?></strong>. Select how you would like to pay.
                </p>

                <?php if ($error): ?>
                    <div class="alert-rd-danger mb-4">
                        <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" id="paymentForm">

                    <!-- GCash -->
                    <label class="method-card mb-3 <?= ($_POST['method'] ?? '') === 'gcash' ? 'selected' : '' ?>"
                           id="card-gcash" onclick="selectMethod('gcash')">
                        <input type="radio" name="method" value="gcash"
                               <?= ($_POST['method'] ?? '') === 'gcash' ? 'checked' : '' ?>>
                        <div class="method-icon" style="background:#E8F5E9; color:#2E7D32;">
                            <i class="bi bi-phone-fill"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:14px; font-weight:700; color:#1A1A1A;">GCash</div>
                            <div style="font-size:12px; color:var(--rd-muted);">Pay via GCash e-wallet</div>
                        </div>
                        <div id="check-gcash" style="display:none; color:var(--rd-red);">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                    </label>

                    <div class="method-details-panel <?= ($_POST['method'] ?? '') === 'gcash' ? 'visible' : '' ?>" id="panel-gcash">
                        <p style="font-size:13px; color:#555; margin-bottom:12px; line-height:1.55;">
                            Enter your <strong>GCash-registered mobile number</strong> to confirm payment of
                            <strong>&#8369;<?= number_format($total) ?></strong>.
                        </p>
                        <label class="form-label">GCash Mobile Number <span style="color:var(--rd-red)">*</span></label>
                        <input type="tel" name="gcash_number" id="gcash_number" class="form-control"
                               placeholder="0917-xxx-xxxx" maxlength="13"
                               oninput="formatPhone(this)"
                               value="<?= htmlspecialchars($_POST['gcash_number'] ?? '') ?>">
                        <div style="font-size:11px; color:#aaa; margin-top:5px;">Format: xxxx-xxx-xxxx (e.g. 0917-123-4567)</div>
                    </div>

                    <!-- Maya -->
                    <label class="method-card mb-3 <?= ($_POST['method'] ?? '') === 'maya' ? 'selected' : '' ?>"
                           id="card-maya" onclick="selectMethod('maya')">
                        <input type="radio" name="method" value="maya"
                               <?= ($_POST['method'] ?? '') === 'maya' ? 'checked' : '' ?>>
                        <div class="method-icon" style="background:#E8EAF6; color:#3949AB;">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:14px; font-weight:700; color:#1A1A1A;">Maya</div>
                            <div style="font-size:12px; color:var(--rd-muted);">Pay via Maya (PayMaya)</div>
                        </div>
                        <div id="check-maya" style="display:none; color:var(--rd-red);">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                    </label>

                    <div class="method-details-panel <?= ($_POST['method'] ?? '') === 'maya' ? 'visible' : '' ?>" id="panel-maya">
                        <p style="font-size:13px; color:#555; margin-bottom:12px; line-height:1.55;">
                            Enter your <strong>Maya-registered mobile number</strong> to confirm payment of
                            <strong>&#8369;<?= number_format($total) ?></strong>.
                        </p>
                        <label class="form-label">Maya Mobile Number <span style="color:var(--rd-red)">*</span></label>
                        <input type="tel" name="maya_number" id="maya_number" class="form-control"
                               placeholder="0998-xxx-xxxx" maxlength="13"
                               oninput="formatPhone(this)"
                               value="<?= htmlspecialchars($_POST['maya_number'] ?? '') ?>">
                        <div style="font-size:11px; color:#aaa; margin-top:5px;">Format: xxxx-xxx-xxxx (e.g. 0998-123-4567)</div>
                    </div>

                    <!-- Credit / Debit Card -->
                    <label class="method-card mb-3 <?= ($_POST['method'] ?? '') === 'credit_card' ? 'selected' : '' ?>"
                           id="card-credit_card" onclick="selectMethod('credit_card')">
                        <input type="radio" name="method" value="credit_card"
                               <?= ($_POST['method'] ?? '') === 'credit_card' ? 'checked' : '' ?>>
                        <div class="method-icon" style="background:#FFF3E0; color:#E65100;">
                            <i class="bi bi-credit-card-fill"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:14px; font-weight:700; color:#1A1A1A;">Credit / Debit Card</div>
                            <div style="font-size:12px; color:var(--rd-muted);">Visa, Mastercard, JCB</div>
                        </div>
                        <div id="check-credit_card" style="display:none; color:var(--rd-red);">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                    </label>

                    <div class="method-details-panel <?= ($_POST['method'] ?? '') === 'credit_card' ? 'visible' : '' ?>" id="panel-credit_card">
                        <div class="mb-3">
                            <label class="form-label">Card Number</label>
                            <input type="text" name="card_number" class="form-control"
                                   placeholder="1234 5678 9012 3456" maxlength="19"
                                   oninput="formatCard(this)"
                                   value="<?= htmlspecialchars($_POST['card_number'] ?? '') ?>">
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Expiry Date</label>
                                <input type="text" name="card_expiry" class="form-control"
                                       placeholder="MM / YY" maxlength="7"
                                       oninput="formatExpiry(this)"
                                       value="<?= htmlspecialchars($_POST['card_expiry'] ?? '') ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label">CVV</label>
                                <input type="password" name="card_cvv" class="form-control"
                                       placeholder="3-digit code" maxlength="3"
                                       oninput="this.value=this.value.replace(/\D/g,'')"
                                       value="<?= htmlspecialchars($_POST['card_cvv'] ?? '') ?>">
                            </div>
                        </div>
                        <p style="font-size:11px; color:#aaa; margin-top:10px; margin-bottom:0; display:flex; align-items:center; gap:5px;">
                            <i class="bi bi-lock-fill"></i> Your card details are encrypted and never stored.
                        </p>
                    </div>

                    <!-- Pay at Hotel -->
                    <label class="method-card mb-3 <?= ($_POST['method'] ?? '') === 'pay_at_hotel' ? 'selected' : '' ?>"
                           id="card-pay_at_hotel" onclick="selectMethod('pay_at_hotel')">
                        <input type="radio" name="method" value="pay_at_hotel"
                               <?= ($_POST['method'] ?? '') === 'pay_at_hotel' ? 'checked' : '' ?>>
                        <div class="method-icon" style="background:var(--rd-red-pale); color:var(--rd-red);">
                            <i class="bi bi-building-fill"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:14px; font-weight:700; color:#1A1A1A;">Pay at Hotel</div>
                            <div style="font-size:12px; color:var(--rd-muted);">Pay cash on arrival at check-in</div>
                        </div>
                        <div id="check-pay_at_hotel" style="display:none; color:var(--rd-red);">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                    </label>

                    <div class="method-details-panel <?= ($_POST['method'] ?? '') === 'pay_at_hotel' ? 'visible' : '' ?>" id="panel-pay_at_hotel">
                        <div style="display:flex; align-items:flex-start; gap:10px; font-size:13px; color:#555; line-height:1.6;">
                            <i class="bi bi-info-circle" style="color:var(--rd-red); font-size:16px; margin-top:1px;"></i>
                            <div>
                                Your room will be reserved. Pay <strong>&#8369;<?= number_format($total) ?></strong> in cash upon arrival at the front desk.
                                Please bring a valid ID for check-in.
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="mt-4">
                        <button type="submit" name="pay" class="btn-rd w-100"
                                style="justify-content:center; padding:14px; font-size:15px;">
                            <i class="bi bi-check-circle me-1"></i>
                            Confirm Payment &mdash; &#8369;<?= number_format($total) ?>
                        </button>
                        <a href="/customer/dashboard.php"
                           style="display:block; text-align:center; margin-top:12px; font-size:13px; color:var(--rd-muted);">
                            Cancel and return to my bookings
                        </a>
                    </div>

                </form>
            </div>

        </div>

        <!-- ===== RIGHT: Booking Summary ===== -->
        <div class="col-lg-5">
            <div style="background:#fff; border-radius:14px; box-shadow:var(--rd-shadow); overflow:hidden; border:1px solid rgba(228,223,223,0.5); position:sticky; top:80px;">

                <!-- Hotel image -->
                <div style="height:130px; position:relative; overflow:hidden;">
                    <img src="https://picsum.photos/seed/<?= $imgSeed ?>/640/260"
                         alt="" style="width:100%; height:100%; object-fit:cover;">
                    <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 60%);"></div>
                    <div style="position:absolute; bottom:12px; left:16px; color:#fff;">
                        <div style="font-size:15px; font-weight:700; line-height:1.2;"><?= htmlspecialchars($booking['Hotel_Name']) ?></div>
                        <div style="font-size:12px; opacity:0.8;"><?= htmlspecialchars($booking['Hotel_City']) ?></div>
                    </div>
                </div>

                <!-- Summary details -->
                <div style="padding:18px 20px;">

                    <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.7px; color:var(--rd-muted); margin-bottom:12px;">
                        Booking Summary
                    </div>

                    <?php
                    $summaryRows = [
                        ['Reference',  htmlspecialchars($refCode)],
                        ['Room',       htmlspecialchars($booking['Room_Type'])],
                        ['Check-in',   date('D, M d Y', strtotime($booking['Book_CheckIn']))],
                        ['Check-out',  date('D, M d Y', strtotime($booking['Book_CheckOut']))],
                        ['Duration',   $nights . ' night' . ($nights != 1 ? 's' : '')],
                        ['Guests',     $booking['Book_Guests'] . ' guest' . ($booking['Book_Guests'] > 1 ? 's' : '')],
                    ];
                    foreach ($summaryRows as [$lbl, $val]):
                    ?>
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:7px 0; border-bottom:1px solid #F5F2F2; font-size:13px;">
                        <span style="color:var(--rd-muted);"><?= $lbl ?></span>
                        <span style="font-weight:600; text-align:right; color:#333;"><?= $val ?></span>
                    </div>
                    <?php endforeach; ?>

                    <!-- Total -->
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:14px 0 4px;">
                        <span style="font-size:15px; font-weight:700; color:#111;">Amount Due</span>
                        <span class="price-tag" style="font-size:20px;">&#8369;<?= number_format($total) ?></span>
                    </div>

                </div>

                <!-- Security note -->
                <div style="background:var(--rd-bg); padding:12px 20px; border-top:1px solid var(--rd-border); font-size:12px; color:#888; display:flex; align-items:center; gap:7px;">
                    <i class="bi bi-shield-lock-fill" style="color:var(--rd-red);"></i>
                    Secured with 256-bit SSL encryption.
                </div>

            </div>
        </div>

    </div>
</div>


<script>
const methods = ['gcash', 'maya', 'credit_card', 'pay_at_hotel'];

function selectMethod(selected) {
    methods.forEach(m => {
        const card  = document.getElementById('card-' + m);
        const panel = document.getElementById('panel-' + m);
        const check = document.getElementById('check-' + m);
        const radio = card.querySelector('input[type="radio"]');

        if (m === selected) {
            card.classList.add('selected');
            panel.classList.add('visible');
            check.style.display = 'block';
            radio.checked = true;
        } else {
            card.classList.remove('selected');
            panel.classList.remove('visible');
            check.style.display = 'none';
            radio.checked = false;
        }
    });
}

// Restore selection on page reload (after POST error)
(function() {
    const checked = document.querySelector('input[name="method"]:checked');
    if (checked) selectMethod(checked.value);
})();

// Phone number formatting: xxxx-xxx-xxxx
function formatPhone(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 11);
    if (v.length > 7)      v = v.substring(0,4) + '-' + v.substring(4,7) + '-' + v.substring(7);
    else if (v.length > 4) v = v.substring(0,4) + '-' + v.substring(4);
    input.value = v;
}

// Card number formatting
function formatCard(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 16);
    input.value = v.replace(/(.{4})/g, '$1 ').trim();
}

// Expiry formatting
function formatExpiry(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 4);
    if (v.length >= 3) v = v.substring(0, 2) + ' / ' + v.substring(2);
    input.value = v;
}
</script>

<?php include "../layout/footer.php"; ?>
