<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'hotel_owner') {
    header("Location: /auth/login.php"); exit();
}

$hotelId = (int) ($_SESSION['hotel_id'] ?? 0);
$acctId  = (int) $_SESSION['account_id'];
if (!$hotelId) { header("Location: /auth/logout.php"); exit(); }

$totalEarnings  = fs_sum('earnings', 'ownerShare', [['hotelId', '=', $hotelId], ['status', '!=', 'voided']]);
$pendingBalance = fs_sum('earnings', 'ownerShare', [['hotelId', '=', $hotelId], ['status', '=', 'pending']]);
$paidOut        = fs_sum('earnings', 'ownerShare', [['hotelId', '=', $hotelId], ['status', '=', 'paid_out']]);

$earningsList = fs_query('earnings', [['hotelId', '=', $hotelId]], [['createdAt', 'DESC']]);

// Enrich earnings with booking and customer info
foreach ($earningsList as &$e) {
    $booking = fs_get('bookings', (int)$e['bookId']);
    $e['checkIn']  = $booking['checkIn']  ?? '';
    $e['checkOut'] = $booking['checkOut'] ?? '';
    $cust = $booking ? fs_get('customers', (int)$booking['custId']) : null;
    $e['custFirstName'] = $cust['firstName'] ?? '';
    $e['custLastName']  = $cust['lastName']  ?? '';
}
unset($e);

// Handle payout request
$payoutMsg = ''; $payoutError = '';
if (isset($_POST['request_payout']) && $pendingBalance > 0) {
    $method  = $_POST['payout_method']     ?? 'bank_transfer';
    $acctNo  = $_POST['payout_account_no'] ?? '';
    $amount  = (float) $pendingBalance;
    fs_insert('payoutrequests', [
        'ownerId'   => $acctId,
        'hotelId'   => $hotelId,
        'amount'    => $amount,
        'method'    => $method,
        'accountNo' => $acctNo,
        'status'    => 'pending',
    ]);
    // Mark earnings as pending_payout
    $pendingEarns = fs_query('earnings', [['hotelId', '=', $hotelId], ['status', '=', 'pending']]);
    foreach ($pendingEarns as $pe) {
        fs_update('earnings', (int)$pe['id'], ['status' => 'pending_payout']);
    }
    $payoutMsg = 'Payout request submitted. The admin will process it within 3–5 business days.';
    header("Location: earnings.php?msg=" . urlencode($payoutMsg)); exit();
}

$title = "Earnings";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:auto;">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <div class="page-header">
            <h1>Earnings</h1>
            <p>Track your hotel's booking revenue and request payouts.</p>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg']): ?>
        <div class="alert-rd-success mb-4" style="display:flex; align-items:center; gap:9px;">
            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($_GET['msg']) ?>
        </div>
        <?php endif; ?>

        <!-- Summary cards -->
        <div class="row g-4 mb-4">
            <div class="col-sm-4">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value" style="font-size:22px;">&#8369;<?= number_format($totalEarnings, 2) ?></div>
                            <div class="stat-label">Total Earned (85%)</div>
                        </div>
                        <div class="stat-icon" style="background:#FFFBEB; color:#D97706;">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value" style="font-size:22px;">&#8369;<?= number_format($pendingBalance, 2) ?></div>
                            <div class="stat-label">Pending Balance</div>
                        </div>
                        <div class="stat-icon" style="background:#FFF3CD; color:#856404;">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value" style="font-size:22px;">&#8369;<?= number_format($paidOut, 2) ?></div>
                            <div class="stat-label">Total Paid Out</div>
                        </div>
                        <div class="stat-icon" style="background:#ECFDF5; color:#047857;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request payout -->
        <?php if ($pendingBalance > 0): ?>
        <div style="background:#fff; border-radius:14px; padding:22px 24px; box-shadow:var(--rd-shadow); margin-bottom:24px; border:1px solid rgba(228,223,223,0.5); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
            <div>
                <div style="font-size:15px; font-weight:700; margin-bottom:4px;">
                    Available for payout: <span style="color:var(--rd-red);">&#8369;<?= number_format($pendingBalance, 2) ?></span>
                </div>
                <div style="font-size:13px; color:var(--rd-muted);">Request a withdrawal to your bank or e-wallet.</div>
            </div>
            <button type="button" class="btn-rd" data-bs-toggle="modal" data-bs-target="#payoutModal">
                <i class="bi bi-send me-1"></i>Request Payout
            </button>
        </div>
        <?php endif; ?>

        <!-- Earnings table -->
        <div class="table-rd">
            <div style="padding:18px 22px; border-bottom:1px solid #F0F0F0;">
                <h5 style="font-size:15px; font-weight:700; margin:0;">Earnings History</h5>
            </div>
            <div style="overflow-x:auto;">
                <table class="table mb-0" style="font-size:14px;">
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Guest</th>
                            <th>Stay</th>
                            <th>Total Paid</th>
                            <th>Your 85%</th>
                            <th>Platform 15%</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($earningsList)): ?>
                    <tr><td colspan="7" style="text-align:center; padding:40px; color:#999;">No earnings yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($earningsList as $e):
                        $earnBadge = match($e['status']) {
                            'paid_out'       => '<span class="badge-completed">Paid Out</span>',
                            'pending_payout' => '<span class="badge-confirmed">Processing</span>',
                            'voided'         => '<span class="badge-voided">Voided</span>',
                            default          => '<span class="badge-pending">Pending</span>',
                        };
                    ?>
                    <tr>
                        <td style="color:#999;">#<?= str_pad($e['bookId'],4,'0',STR_PAD_LEFT) ?></td>
                        <td style="font-weight:600;"><?= htmlspecialchars($e['custFirstName'].' '.$e['custLastName']) ?></td>
                        <td style="color:#555;">
                            <?= date('M d', strtotime($e['checkIn'])) ?> &ndash; <?= date('M d, Y', strtotime($e['checkOut'])) ?>
                        </td>
                        <td>&#8369;<?= number_format($e['totalAmount'], 2) ?></td>
                        <td style="font-weight:700; color:var(--rd-red);">&#8369;<?= number_format($e['ownerShare'], 2) ?></td>
                        <td style="color:#999;">&#8369;<?= number_format($e['platformFee'], 2) ?></td>
                        <td><?= $earnBadge ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Payout Request Modal -->
<div class="modal fade" id="payoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none;">
            <div class="modal-header" style="border-bottom:1px solid var(--rd-border); padding:20px 24px;">
                <h5 class="modal-title" style="font-size:16px; font-weight:700;">Request Payout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body" style="padding:24px;">
                    <p style="font-size:14px; color:#555; margin-bottom:16px;">
                        Requesting payout of <strong style="color:var(--rd-red);">&#8369;<?= number_format($pendingBalance, 2) ?></strong>.
                    </p>
                    <div class="mb-3">
                        <label class="form-label">Payout Method</label>
                        <select name="payout_method" class="form-select">
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="gcash">GCash</option>
                            <option value="maya">Maya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Account / Mobile Number</label>
                        <input type="text" name="payout_account_no" class="form-control" placeholder="Account number or mobile number">
                    </div>
                    <div style="background:var(--rd-red-pale); border:1px solid rgba(184,0,32,0.14); border-radius:8px; padding:11px 14px; font-size:12px; color:#7A001A;">
                        <i class="bi bi-clock me-1"></i>Processing takes 3–5 business days after admin approval.
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--rd-border); padding:16px 24px;">
                    <button type="button" class="btn-rd-outline" data-bs-dismiss="modal" style="padding:9px 22px;">Cancel</button>
                    <button type="submit" name="request_payout" class="btn-rd" style="padding:9px 22px;">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "../layout/footer.php"; ?>
