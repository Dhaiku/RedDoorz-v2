<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth/login.php"); exit();
}

$msg = ''; $error = '';

// Approve application: create account + hotel, link owner
if (isset($_POST['approve_app'])) {
    $appId = (int) $_POST['app_id'];
    $app   = fs_get('ownerapplications', $appId);
    if ($app && $app['status'] === 'pending') {
        $email   = $app['acctEmail']  ?? $app['email'] ?? '';
        $tmpPass = password_hash($_POST['tmp_pass'] ?? 'reddoorz123', PASSWORD_DEFAULT);

        // Check for duplicate email
        $emailExists = fs_find('accounts', [['email', '=', $email]]);
        if ($emailExists) {
            $error = "An account with email '{$email}' already exists. Cannot create duplicate.";
        } else {
            $newAcctId = fs_insert('accounts', [
                'email'              => $email,
                'password'           => $tmpPass,
                'role'               => 'hotel_owner',
                'status'             => 'active',
                'mustChangePassword' => true,
            ]);

            fs_insert('hotels', [
                'name'    => $app['hotelName']    ?? '',
                'city'    => $app['city']         ?? '',
                'address' => $app['address']      ?? '',
                'ownerId' => $newAcctId,
                'status'  => 'active',
                'rating'  => 0.0,
            ]);

            fs_update('ownerapplications', $appId, ['status' => 'approved']);
            $msg = "Application approved. Account created with temporary password: reddoorz123";
        }
    }
    if ($msg)   { header("Location: manage_owners.php?msg=" . urlencode($msg)); exit(); }
    if ($error) { header("Location: manage_owners.php?err=" . urlencode($error)); exit(); }
}

// Reject application
if (isset($_POST['reject_app'])) {
    $appId = (int) $_POST['app_id'];
    fs_update('ownerapplications', $appId, ['status' => 'rejected']);
    header("Location: manage_owners.php?msg=Application+rejected."); exit();
}

// Toggle owner account status
if (isset($_POST['toggle_owner'])) {
    $acctId    = (int) $_POST['acct_id'];
    $newStatus = $_POST['new_status'] === 'active' ? 'active' : 'suspended';
    fs_update('accounts', $acctId, ['status' => $newStatus]);
    header("Location: manage_owners.php?msg=Owner+account+updated."); exit();
}

// Fetch owners
$ownerAccounts = fs_query('accounts', [['role', '=', 'hotel_owner']], [['id', 'DESC']]);
$owners = [];
foreach ($ownerAccounts as $o) {
    $hotel = fs_find('hotels', [['ownerId', '=', $o['id']]]);
    $owners[] = [
        'acctId'    => $o['id'],
        'email'     => $o['email'],
        'status'    => $o['status'],
        'hotelId'   => $hotel['id']   ?? null,
        'hotelName' => $hotel['name'] ?? '',
        'hotelCity' => $hotel['city'] ?? '',
    ];
}

// Fetch pending applications
$applications = fs_all('ownerapplications', [['createdAt', 'DESC']]);

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
        <?php
        $displayError = $error ?: (isset($_GET['err']) ? $_GET['err'] : '');
        if ($displayError): ?>
        <div class="alert-rd-danger mb-4" style="display:flex; align-items:center; gap:9px;">
            <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($displayError) ?>
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
                    <?php if (empty($owners)): ?>
                    <tr><td colspan="5" style="text-align:center; padding:30px; color:#999;">No hotel owner accounts yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($owners as $o): ?>
                    <tr>
                        <td><?= htmlspecialchars($o['email']) ?></td>
                        <td style="font-weight:600;"><?= htmlspecialchars($o['hotelName'] ?: '—') ?></td>
                        <td style="color:#555;"><?= htmlspecialchars($o['hotelCity'] ?: '—') ?></td>
                        <td>
                            <?= $o['status'] === 'active'
                                ? '<span class="badge-confirmed">Active</span>'
                                : '<span class="badge-cancelled">Suspended</span>' ?>
                        </td>
                        <td>
                            <form method="POST" style="margin:0;" onsubmit="return confirm('Toggle owner account status?');">
                                <input type="hidden" name="acct_id" value="<?= $o['acctId'] ?>">
                                <input type="hidden" name="new_status" value="<?= $o['status'] === 'active' ? 'suspended' : 'active' ?>">
                                <button type="submit" name="toggle_owner" class="btn-rd-outline" style="font-size:12px; padding:5px 14px;">
                                    <?= $o['status'] === 'active' ? 'Suspend' : 'Activate' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Partner Applications -->
        <h5 style="font-size:15px; font-weight:700; margin-bottom:14px;">Partner Applications</h5>
        <div class="table-rd">
            <div style="overflow-x:auto;">
                <table class="table mb-0" style="font-size:14px;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Hotel</th>
                            <th>City</th>
                            <th>Applied</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($applications)): ?>
                    <tr><td colspan="7" style="text-align:center; padding:30px; color:#999;">No applications yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($applications as $app):
                        $appBadge = match($app['status'] ?? '') {
                            'approved' => '<span class="badge-confirmed">Approved</span>',
                            'rejected' => '<span class="badge-cancelled">Rejected</span>',
                            default    => '<span class="badge-pending">Pending</span>',
                        };
                        $applicantName = $app['applicantName'] ?? $app['acctFullName'] ?? '';
                        $applicantEmail = $app['acctEmail'] ?? $app['email'] ?? '';
                    ?>
                    <tr>
                        <td style="font-weight:600;"><?= htmlspecialchars($applicantName) ?></td>
                        <td style="color:#555;"><?= htmlspecialchars($applicantEmail) ?></td>
                        <td><?= htmlspecialchars($app['hotelName'] ?? '') ?></td>
                        <td style="color:#555;"><?= htmlspecialchars($app['city'] ?? '') ?></td>
                        <td style="color:#999;"><?= isset($app['createdAt']) ? date('M d, Y', strtotime($app['createdAt'])) : '—' ?></td>
                        <td><?= $appBadge ?></td>
                        <td>
                            <?php if (($app['status'] ?? '') === 'pending'): ?>
                            <div style="display:flex; gap:7px;">
                                <form method="POST" style="margin:0;" onsubmit="return confirm('Approve this application and create an owner account?');">
                                    <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
                                    <button type="submit" name="approve_app" class="btn-rd" style="font-size:12px; padding:5px 12px;">
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" style="margin:0;" onsubmit="return confirm('Reject this application?');">
                                    <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
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
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
