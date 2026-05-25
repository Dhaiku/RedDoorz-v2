<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth/login.php"); exit();
}

$message = "";

// Toggle account status
if (isset($_POST['toggle_status'])) {
    $acctId    = (int) $_POST['acct_id'];
    $newStatus = $_POST['current_status'] === 'active' ? 'inactive' : 'active';
    fs_update('accounts', $acctId, ['status' => $newStatus]);
    $message = "Account " . ($newStatus === 'active' ? 'activated' : 'deactivated') . ".";
}

$search       = strtolower(trim($_GET['search'] ?? ''));
$filterStatus = $_GET['status'] ?? '';

// Fetch all customer accounts
$custAccounts = fs_query('accounts', array_filter([
    ['role', '=', 'customer'],
    $filterStatus ? ['status', '=', $filterStatus] : null,
], fn($x) => $x !== null));

// Build enriched list
$customers = [];
foreach ($custAccounts as $acct) {
    $cust = fs_find('customers', [['acctId', '=', $acct['id']]]);
    if (!$cust) continue;

    // PHP-side search filter
    if ($search) {
        $haystack = strtolower(($cust['firstName'] ?? '').' '.($cust['lastName'] ?? '').' '.($acct['email'] ?? ''));
        if (strpos($haystack, $search) === false) continue;
    }

    // Booking stats
    $custBookings = fs_query('bookings', [['custId', '=', $cust['id']]]);
    $bookingCount = count($custBookings);
    $totalSpent   = array_sum(array_map(
        fn($b) => in_array($b['status'], ['confirmed','completed']) ? (float)($b['totalPrice'] ?? 0) : 0,
        $custBookings
    ));

    $customers[] = [
        'acctId'       => $acct['id'],
        'email'        => $acct['email'],
        'status'       => $acct['status'],
        'createdAt'    => $acct['createdAt'] ?? '',
        'custId'       => $cust['id'],
        'firstName'    => $cust['firstName'] ?? '',
        'lastName'     => $cust['lastName']  ?? '',
        'phone'        => $cust['phone']     ?? '',
        'bookingCount' => $bookingCount,
        'totalSpent'   => $totalSpent,
    ];
}

$title = "Manage Customers";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:calc(100vh - 64px);">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <div class="page-header">
            <h1>Customers</h1>
            <p>View and manage all registered customer accounts.</p>
        </div>

        <?php if ($message): ?>
            <div class="alert-rd-success mb-4"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Filters -->
        <form method="GET" class="d-flex flex-wrap gap-2 mb-4 align-items-end">
            <div style="min-width:220px; flex:1;">
                <input type="text" name="search" class="form-control" placeholder="Search name or email..."
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <select name="status" class="form-select" style="max-width:160px; font-size:14px;">
                <option value="">All Statuses</option>
                <option value="active"   <?= $filterStatus==='active'   ? 'selected':'' ?>>Active</option>
                <option value="inactive" <?= $filterStatus==='inactive' ? 'selected':'' ?>>Inactive</option>
            </select>
            <button type="submit" class="btn-rd" style="padding:10px 20px;">Filter</button>
            <a href="manage_customers.php" style="font-size:13px; color:#999; align-self:center; text-decoration:none;">Clear</a>
        </form>

        <p style="font-size:13px; color:#999; margin-bottom:16px;"><?= count($customers) ?> customer<?= count($customers) != 1 ? 's' : '' ?> found</p>

        <!-- Table -->
        <div style="background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,0.07); overflow:hidden;">
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:14px;">
                    <thead>
                        <tr style="background:#FAFAFA; border-bottom:1px solid #EBEBEB;">
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Customer</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Contact</th>
                            <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Bookings</th>
                            <th style="padding:12px 16px; text-align:right; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Total Spent</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Joined</th>
                            <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Status</th>
                            <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($customers)): ?>
                        <tr><td colspan="7" style="padding:40px; text-align:center; color:#999;">No customers found.</td></tr>
                    <?php else: foreach ($customers as $c): ?>
                    <tr style="border-bottom:1px solid #F8F8F8;">
                        <td style="padding:14px 16px;">
                            <div style="font-weight:700; font-size:14px;"><?= htmlspecialchars($c['firstName'].' '.$c['lastName']) ?></div>
                            <div style="font-size:12px; color:var(--rd-muted); margin-top:2px;"><?= htmlspecialchars($c['email']) ?></div>
                        </td>
                        <td style="padding:14px 16px; font-size:13px; color:#555;">
                            <?= $c['phone'] ? htmlspecialchars($c['phone']) : '<span style="color:#bbb;">—</span>' ?>
                        </td>
                        <td style="padding:14px 16px; text-align:center; font-weight:700; color:#444;"><?= $c['bookingCount'] ?></td>
                        <td style="padding:14px 16px; text-align:right; font-weight:700; color:var(--rd-red); white-space:nowrap;">
                            &#8369;<?= number_format($c['totalSpent']) ?>
                        </td>
                        <td style="padding:14px 16px; color:#555; font-size:13px; white-space:nowrap;">
                            <?= $c['createdAt'] ? date('M d, Y', strtotime($c['createdAt'])) : '—' ?>
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            <?php if ($c['status'] === 'active'): ?>
                                <span class="badge-confirmed">Active</span>
                            <?php else: ?>
                                <span class="badge-cancelled">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            <form method="POST" style="margin:0;" onsubmit="return confirm('<?= $c['status']==='active' ? 'Deactivate' : 'Activate' ?> this account?')">
                                <input type="hidden" name="acct_id" value="<?= $c['acctId'] ?>">
                                <input type="hidden" name="current_status" value="<?= $c['status'] ?>">
                                <button type="submit" name="toggle_status" style="
                                    font-size:12px; border:1.5px solid <?= $c['status']==='active' ? '#DDD' : 'var(--rd-red)' ?>;
                                    background:none; color:<?= $c['status']==='active' ? '#888' : 'var(--rd-red)' ?>;
                                    border-radius:6px; padding:5px 14px; cursor:pointer;
                                    font-weight:600; font-family:'DM Sans',sans-serif;
                                ">
                                    <?= $c['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
