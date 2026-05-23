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
    $conn->query("UPDATE Accounts SET Acct_Status='$newStatus' WHERE Acct_Id=$acctId AND Acct_Role='customer'");
    $message = "Account " . ($newStatus === 'active' ? 'activated' : 'deactivated') . ".";
}

$search = $conn->real_escape_string(trim($_GET['search'] ?? ''));
$filterStatus = $conn->real_escape_string($_GET['status'] ?? '');

$where = "WHERE a.Acct_Role='customer'";
if ($search) $where .= " AND (c.Cust_FName LIKE '%$search%' OR c.Cust_LName LIKE '%$search%' OR a.Acct_Email LIKE '%$search%')";
if ($filterStatus) $where .= " AND a.Acct_Status='$filterStatus'";

$customers = $conn->query("
    SELECT a.Acct_Id, a.Acct_Email, a.Acct_Status, a.Acct_CreatedAt,
           c.Cust_Id, c.Cust_FName, c.Cust_LName, c.Cust_Phone,
           COUNT(b.Book_Id) AS BookingCount,
           COALESCE(SUM(CASE WHEN b.Book_Status IN ('confirmed','completed') THEN b.Book_TotalPrice ELSE 0 END), 0) AS TotalSpent
    FROM Accounts a
    JOIN Customers c ON c.Cust_AcctId = a.Acct_Id
    LEFT JOIN Bookings b ON b.Book_CustId = c.Cust_Id
    $where
    GROUP BY a.Acct_Id
    ORDER BY a.Acct_CreatedAt DESC
");

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

        <p style="font-size:13px; color:#999; margin-bottom:16px;"><?= $customers->num_rows ?> customer<?= $customers->num_rows != 1 ? 's' : '' ?> found</p>

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
                    <?php if ($customers->num_rows === 0): ?>
                        <tr><td colspan="7" style="padding:40px; text-align:center; color:#999;">No customers found.</td></tr>
                    <?php else: while ($c = $customers->fetch_assoc()): ?>
                    <tr style="border-bottom:1px solid #F8F8F8;">
                        <td style="padding:14px 16px;">
                            <div style="font-weight:700; font-size:14px;"><?= htmlspecialchars($c['Cust_FName'].' '.$c['Cust_LName']) ?></div>
                            <div style="font-size:12px; color:var(--rd-muted); margin-top:2px;"><?= htmlspecialchars($c['Acct_Email']) ?></div>
                        </td>
                        <td style="padding:14px 16px; font-size:13px; color:#555;">
                            <?= $c['Cust_Phone'] ? htmlspecialchars($c['Cust_Phone']) : '<span style="color:#bbb;">—</span>' ?>
                        </td>
                        <td style="padding:14px 16px; text-align:center; font-weight:700; color:#444;"><?= $c['BookingCount'] ?></td>
                        <td style="padding:14px 16px; text-align:right; font-weight:700; color:var(--rd-red); white-space:nowrap;">
                            &#8369;<?= number_format($c['TotalSpent']) ?>
                        </td>
                        <td style="padding:14px 16px; color:#555; font-size:13px; white-space:nowrap;">
                            <?= date('M d, Y', strtotime($c['Acct_CreatedAt'])) ?>
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            <?php if ($c['Acct_Status'] === 'active'): ?>
                                <span class="badge-confirmed">Active</span>
                            <?php else: ?>
                                <span class="badge-cancelled">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            <form method="POST" style="margin:0;" onsubmit="return confirm('<?= $c['Acct_Status']==='active' ? 'Deactivate' : 'Activate' ?> this account?')">
                                <input type="hidden" name="acct_id" value="<?= $c['Acct_Id'] ?>">
                                <input type="hidden" name="current_status" value="<?= $c['Acct_Status'] ?>">
                                <button type="submit" name="toggle_status" style="
                                    font-size:12px; border:1.5px solid <?= $c['Acct_Status']==='active' ? '#DDD' : 'var(--rd-red)' ?>;
                                    background:none; color:<?= $c['Acct_Status']==='active' ? '#888' : 'var(--rd-red)' ?>;
                                    border-radius:6px; padding:5px 14px; cursor:pointer;
                                    font-weight:600; font-family:'DM Sans',sans-serif;
                                ">
                                    <?= $c['Acct_Status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
