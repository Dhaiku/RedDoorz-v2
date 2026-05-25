<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth/login.php"); exit();
}

// Process payout
if (isset($_POST['process_payout'])) {
    $payoutId  = (int) $_POST['payout_id'];
    $newStatus = $_POST['new_status'] ?? '';
    $note      = $_POST['admin_note'] ?? '';

    if (in_array($newStatus, ['approved', 'rejected'])) {
        fs_update('payoutrequests', $payoutId, ['status' => $newStatus, 'adminNote' => $note]);
        if ($newStatus === 'approved') {
            // Mark related earnings as paid_out
            $pReq = fs_get('payoutrequests', $payoutId);
            if ($pReq) {
                $hId = (int)($pReq['hotelId'] ?? 0);
                $pendingEarns = fs_query('earnings', [['hotelId', '=', $hId], ['status', '=', 'pending_payout']]);
                foreach ($pendingEarns as $pe) {
                    fs_update('earnings', (int)$pe['id'], ['status' => 'paid_out']);
                }
            }
        }
        header("Location: manage_payouts.php?msg=Payout+" . ($newStatus === 'approved' ? 'approved' : 'rejected') . "."); exit();
    }
}

// Stats
$totalPending  = fs_sum('payoutrequests', 'amount', [['status', '=', 'pending']]);
$totalApproved = fs_sum('payoutrequests', 'amount', [['status', '=', 'approved']]);
$totalPlatform = fs_sum('earnings', 'platformFee', [['status', '!=', 'voided']]);

// Filter
$statusFilter = $_GET['status'] ?? '';
$wheres = $statusFilter ? [['status', '=', $statusFilter]] : [];
$payoutsRaw = fs_query('payoutrequests', $wheres, [['createdAt', 'DESC']]);

// Enrich
$payouts = [];
foreach ($payoutsRaw as $p) {
    $hotel = fs_get('hotels', (int)($p['hotelId'] ?? 0));
    $acct  = fs_get('accounts', (int)($p['ownerId'] ?? 0));
    $p['hotelName'] = $hotel['name'] ?? '';
    $p['hotelCity'] = $hotel['city'] ?? '';
    $p['acctEmail'] = $acct['email']  ?? '';
    $payouts[] = $p;
}

$title = "Manage Payouts";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:auto;">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <div class="page-header">
            <h1>Payout Requests</h1>
            <p>Review and process hotel owner payout requests.</p>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg']): ?>
        <div class="alert-rd-success mb-4" style="display:flex; align-items:center; gap:9px;">
            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($_GET['msg']) ?>
        </div>
        <?php endif; ?>

        <!-- Summary stats -->
        <div class="row g-4 mb-4">
            <div class="col-sm-4">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value" style="font-size:22px;">&#8369;<?= number_format($totalPending, 2) ?></div>
                            <div class="stat-label">Pending Payouts</div>
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
                            <div class="stat-value" style="font-size:22px;">&#8369;<?= number_format($totalApproved, 2) ?></div>
                            <div class="stat-label">Total Paid Out</div>
                        </div>
                        <div class="stat-icon" style="background:#ECFDF5; color:#047857;">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value" style="font-size:22px;">&#8369;<?= number_format($totalPlatform, 2) ?></div>
                            <div class="stat-label">Platform Revenue (15%)</div>
                        </div>
                        <div class="stat-icon" style="background:var(--rd-red-pale); color:var(--rd-red);">
                            <i class="bi bi-graph-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter tabs -->
        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px;">
            <?php
            $statuses = ['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'];
            foreach ($statuses as $val => $lbl):
                $active = $statusFilter === $val;
            ?>
            <a href="?status=<?= $val ?>" style="
                padding:7px 16px; border-radius:8px; font-size:13px; font-weight:600;
                text-decoration:none; border:1.5px solid <?= $active ? 'var(--rd-red)' : 'var(--rd-border)' ?>;
                background:<?= $active ? 'var(--rd-red)' : '#fff' ?>;
                color:<?= $active ? '#fff' : '#555' ?>;
                transition:all 0.15s;
            "><?= $lbl ?></a>
            <?php endforeach; ?>
        </div>

        <!-- Payouts table -->
        <div class="table-rd">
            <div style="overflow-x:auto;">
                <table class="table mb-0" style="font-size:14px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Owner Email</th>
                            <th>Hotel</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Account</th>
                            <th>Requested</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($payouts)): ?>
                    <tr><td colspan="9" style="text-align:center; padding:40px; color:#999;">No payout requests found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($payouts as $p):
                        $pBadge = match($p['status'] ?? '') {
                            'approved' => '<span class="badge-confirmed">Approved</span>',
                            'rejected' => '<span class="badge-cancelled">Rejected</span>',
                            default    => '<span class="badge-pending">Pending</span>',
                        };
                    ?>
                    <tr>
                        <td style="color:#999;">#<?= str_pad($p['id'],4,'0',STR_PAD_LEFT) ?></td>
                        <td><?= htmlspecialchars($p['acctEmail']) ?></td>
                        <td style="font-weight:600;"><?= htmlspecialchars($p['hotelName']) ?></td>
                        <td style="font-weight:700; color:var(--rd-red);">&#8369;<?= number_format($p['amount'], 2) ?></td>
                        <td><?= htmlspecialchars(ucfirst(str_replace('_',' ',$p['method'] ?? ''))) ?></td>
                        <td style="font-family:monospace; font-size:12px;"><?= htmlspecialchars($p['accountNo'] ?? '—') ?></td>
                        <td style="color:#999;"><?= isset($p['createdAt']) ? date('M d, Y', strtotime($p['createdAt'])) : '—' ?></td>
                        <td><?= $pBadge ?></td>
                        <td>
                            <?php if (($p['status'] ?? '') === 'pending'): ?>
                            <button type="button" class="btn-rd" style="font-size:12px; padding:5px 12px;"
                                    data-bs-toggle="modal" data-bs-target="#processModal"
                                    data-id="<?= $p['id'] ?>"
                                    data-amount="<?= number_format($p['amount'],2) ?>"
                                    data-hotel="<?= htmlspecialchars($p['hotelName']) ?>">
                                Process
                            </button>
                            <?php else: ?>
                            <span style="font-size:12px; color:#aaa;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Process Payout Modal -->
<div class="modal fade" id="processModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none;">
            <div class="modal-header" style="border-bottom:1px solid var(--rd-border); padding:20px 24px;">
                <h5 class="modal-title" style="font-size:16px; font-weight:700;">Process Payout Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body" style="padding:24px;">
                    <input type="hidden" name="payout_id" id="modal_payout_id">
                    <p style="font-size:14px; color:#555; margin-bottom:16px;">
                        Payout of <strong id="modal_payout_amount"></strong> for <strong id="modal_payout_hotel"></strong>.
                    </p>
                    <div class="mb-3">
                        <label class="form-label">Decision</label>
                        <select name="new_status" class="form-select">
                            <option value="approved">Approve &amp; Release</option>
                            <option value="rejected">Reject</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Note <span style="color:var(--rd-muted); font-weight:400;">(optional)</span></label>
                        <textarea name="admin_note" class="form-control" rows="3" placeholder="e.g. Transfer reference or rejection reason"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--rd-border); padding:16px 24px;">
                    <button type="button" class="btn-rd-outline" data-bs-dismiss="modal" style="padding:9px 22px;">Cancel</button>
                    <button type="submit" name="process_payout" class="btn-rd" style="padding:9px 22px;">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('processModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('modal_payout_id').value       = btn.dataset.id;
    document.getElementById('modal_payout_amount').textContent = '₱' + btn.dataset.amount;
    document.getElementById('modal_payout_hotel').textContent  = btn.dataset.hotel;
});
</script>

<?php include "../layout/footer.php"; ?>
