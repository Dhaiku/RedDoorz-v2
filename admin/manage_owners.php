<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth/login.php"); exit();
}

$hasAppsTable = $conn->query("SHOW TABLES LIKE 'OwnerApplications'")->num_rows > 0;
$msg = ''; $error = '';

// Approve application: create account + hotel, link owner
if (isset($_POST['approve_app']) && $hasAppsTable) {
    $appId = (int) $_POST['app_id'];
    $app   = $conn->query("SELECT * FROM OwnerApplications WHERE App_Id=$appId LIMIT 1")->fetch_assoc();
    if ($app && $app['App_Status'] === 'pending') {
        // Create account with hotel_owner role
        $email    = $conn->real_escape_string($app['App_Email']);
        $name     = $conn->real_escape_string($app['App_FullName']);
        $tmpPass  = password_hash('reddoorz123', PASSWORD_DEFAULT); // temp password
        $conn->query("INSERT INTO Accounts (Acct_Email, Acct_Password, Acct_Role, Acct_Status, Acct_MustChangePassword) VALUES ('$email','$tmpPass','hotel_owner','active',1)");
        $newAcctId = $conn->insert_id;

        if ($newAcctId) {
            // Create hotel record
            $hotelName = $conn->real_escape_string($app['App_HotelName']);
            $hotelCity = $conn->real_escape_string($app['App_HotelCity']);
            $hotelAddr = $conn->real_escape_string($app['App_HotelAddress']);
            $conn->query("INSERT INTO Hotels (Hotel_Name, Hotel_City, Hotel_Address, Hotel_OwnerId, Hotel_Status) VALUES ('$hotelName','$hotelCity','$hotelAddr',$newAcctId,'active')");
            // Mark app as approved
            $conn->query("UPDATE OwnerApplications SET App_Status='approved' WHERE App_Id=$appId");
            $msg = "Application approved. Account created for {$app['App_FullName']} with temporary password: reddoorz123";
        } else {
            $error = "Email already exists. Cannot create duplicate account.";
        }
    }
    if ($msg) { header("Location: manage_owners.php?msg=" . urlencode($msg)); exit(); }
}

// Reject application
if (isset($_POST['reject_app']) && $hasAppsTable) {
    $appId = (int) $_POST['app_id'];
    $conn->query("UPDATE OwnerApplications SET App_Status='rejected' WHERE App_Id=$appId");
    header("Location: manage_owners.php?msg=Application+rejected."); exit();
}

// Toggle owner account status
if (isset($_POST['toggle_owner'])) {
    $acctId    = (int) $_POST['acct_id'];
    $newStatus = $_POST['new_status'] === 'active' ? 'active' : 'suspended';
    $conn->query("UPDATE Accounts SET Acct_Status='$newStatus' WHERE Acct_Id=$acctId AND Acct_Role='hotel_owner'");
    header("Location: manage_owners.php?msg=Owner+account+updated."); exit();
}

// Fetch owners
$owners = $conn->query("
    SELECT a.Acct_Id, a.Acct_Email, a.Acct_Status, h.Hotel_Id, h.Hotel_Name, h.Hotel_City
    FROM Accounts a
    LEFT JOIN Hotels h ON h.Hotel_OwnerId = a.Acct_Id
    WHERE a.Acct_Role = 'hotel_owner'
    ORDER BY a.Acct_Id DESC
");

// Fetch pending applications
$applications = $hasAppsTable ? $conn->query("SELECT * FROM OwnerApplications ORDER BY App_CreatedAt DESC") : null;

$title = "Manage Hotel Owners";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:auto;">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <div class="page-header">
            <h1>Hotel Owners</h1>
            <p>Manage hotel owner accounts and review partner applications.</p>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg']): ?>
        <div class="alert-rd-success mb-4" style="display:flex; align-items:center; gap:9px;">
            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($_GET['msg']) ?>
        </div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert-rd-danger mb-4" style="display:flex; align-items:center; gap:9px;">
            <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Owner Accounts -->
        <h5 style="font-size:15px; font-weight:700; margin-bottom:14px;">Active Owner Accounts</h5>
        <div class="table-rd mb-4">
            <div style="overflow-x:auto;">
                <table class="table mb-0" style="font-size:14px;">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Hotel Name</th>
                            <th>City</th>
                            <th>Account Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!$owners || $owners->num_rows === 0): ?>
                    <tr><td colspan="5" style="text-align:center; padding:30px; color:#999;">No hotel owner accounts yet.</td></tr>
                    <?php endif; ?>
                    <?php while ($o = $owners ? $owners->fetch_assoc() : null):
                        if (!$o) break;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($o['Acct_Email']) ?></td>
                        <td style="font-weight:600;"><?= htmlspecialchars($o['Hotel_Name'] ?? '—') ?></td>
                        <td style="color:#555;"><?= htmlspecialchars($o['Hotel_City'] ?? '—') ?></td>
                        <td>
                            <?= $o['Acct_Status'] === 'active'
                                ? '<span class="badge-confirmed">Active</span>'
                                : '<span class="badge-cancelled">Suspended</span>' ?>
                        </td>
                        <td>
                            <form method="POST" style="margin:0;" onsubmit="return confirm('Toggle owner account status?');">
                                <input type="hidden" name="acct_id" value="<?= $o['Acct_Id'] ?>">
                                <input type="hidden" name="new_status" value="<?= $o['Acct_Status'] === 'active' ? 'suspended' : 'active' ?>">
                                <button type="submit" name="toggle_owner" class="btn-rd-outline" style="font-size:12px; padding:5px 14px;">
                                    <?= $o['Acct_Status'] === 'active' ? 'Suspend' : 'Activate' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Partner Applications -->
        <h5 style="font-size:15px; font-weight:700; margin-bottom:14px;">Partner Applications</h5>
        <?php if (!$hasAppsTable): ?>
        <div style="background:#FFF8E1; border:1px solid #FFE082; border-radius:8px; padding:14px; color:#7B5800; font-size:13px;">
            <i class="bi bi-exclamation-triangle me-2"></i>Run <code>config/migration_owner.sql</code> to enable partner applications.
        </div>
        <?php else: ?>
        <div class="table-rd">
            <div style="overflow-x:auto;">
                <table class="table mb-0" style="font-size:14px;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Hotel</th>
                            <th>City</th>
                            <th>Rooms</th>
                            <th>Applied</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($applications->num_rows === 0): ?>
                    <tr><td colspan="8" style="text-align:center; padding:30px; color:#999;">No applications yet.</td></tr>
                    <?php endif; ?>
                    <?php while ($app = $applications->fetch_assoc()):
                        $appBadge = match($app['App_Status']) {
                            'approved' => '<span class="badge-confirmed">Approved</span>',
                            'rejected' => '<span class="badge-cancelled">Rejected</span>',
                            default    => '<span class="badge-pending">Pending</span>',
                        };
                    ?>
                    <tr>
                        <td style="font-weight:600;"><?= htmlspecialchars($app['App_FullName']) ?></td>
                        <td style="color:#555;"><?= htmlspecialchars($app['App_Email']) ?></td>
                        <td><?= htmlspecialchars($app['App_HotelName']) ?></td>
                        <td style="color:#555;"><?= htmlspecialchars($app['App_HotelCity']) ?></td>
                        <td><?= $app['App_RoomCount'] ?></td>
                        <td style="color:#999;"><?= date('M d, Y', strtotime($app['App_CreatedAt'])) ?></td>
                        <td><?= $appBadge ?></td>
                        <td>
                            <?php if ($app['App_Status'] === 'pending'): ?>
                            <div style="display:flex; gap:7px;">
                                <form method="POST" style="margin:0;" onsubmit="return confirm('Approve this application and create an owner account?');">
                                    <input type="hidden" name="app_id" value="<?= $app['App_Id'] ?>">
                                    <button type="submit" name="approve_app" class="btn-rd" style="font-size:12px; padding:5px 12px;">
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" style="margin:0;" onsubmit="return confirm('Reject this application?');">
                                    <input type="hidden" name="app_id" value="<?= $app['App_Id'] ?>">
                                    <button type="submit" name="reject_app" style="font-size:12px; background:#FEF2F2; color:#B91C1C; border:1px solid #FECACA; border-radius:6px; padding:5px 12px; cursor:pointer; font-weight:600; font-family:'DM Sans',sans-serif;">
                                        Reject
                                    </button>
                                </form>
                            </div>
                            <?php else: ?>
                            <span style="font-size:12px; color:#aaa;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
