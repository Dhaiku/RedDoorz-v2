<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /auth/login.php"); exit();
}

$acctId = (int) $_SESSION['account_id'];
$custId = (int) $_SESSION['customer_id'];

$success = "";
$error   = "";

// Load current data
$acct = $conn->query("SELECT a.*, c.Cust_FName, c.Cust_LName, c.Cust_Phone
    FROM Accounts a JOIN Customers c ON c.Cust_AcctId = a.Acct_Id
    WHERE a.Acct_Id=$acctId LIMIT 1")->fetch_assoc();

// UPDATE PROFILE
if (isset($_POST['update_profile'])) {
    $fname = trim($conn->real_escape_string($_POST['fname']));
    $lname = trim($conn->real_escape_string($_POST['lname']));
    $phone = trim($conn->real_escape_string($_POST['phone'] ?? ''));

    // Validate phone: strip dashes, must be empty or exactly 11 digits starting with 09
    $phoneDigits = preg_replace('/\D/', '', $phone);
    if ($phone !== '' && (strlen($phoneDigits) !== 11 || substr($phoneDigits, 0, 2) !== '09')) {
        $error = "Phone number must be a valid 11-digit PH number starting with 09 (e.g. 0917-123-4567).";
    } elseif (!$fname || !$lname) {
        $error = "First and last name are required.";
    } else {
        // Store in xxxx-xxx-xxxx format
        if ($phoneDigits) {
            $phone = substr($phoneDigits,0,4).'-'.substr($phoneDigits,4,3).'-'.substr($phoneDigits,7,4);
        }
        $phone = $conn->real_escape_string($phone);
        $conn->query("UPDATE Customers SET Cust_FName='$fname', Cust_LName='$lname', Cust_Phone='$phone' WHERE Cust_Id=$custId");
        $_SESSION['display_name'] = $fname . ' ' . $lname;
        $success = "Profile updated successfully.";
        $acct['Cust_FName'] = $fname;
        $acct['Cust_LName'] = $lname;
        $acct['Cust_Phone'] = $phone;
    }
}

// CHANGE PASSWORD
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new     = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (!password_verify($current, $acct['Acct_Password'])) {
        $error = "Current password is incorrect.";
    } elseif (strlen($new) < 6) {
        $error = "New password must be at least 6 characters.";
    } elseif ($new !== $confirm) {
        $error = "New passwords do not match.";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE Accounts SET Acct_Password='$hashed' WHERE Acct_Id=$acctId");
        $success = "Password changed successfully.";
    }
}

// Booking stats
$stats = $conn->query("
    SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN Book_Status='confirmed' THEN 1 ELSE 0 END) AS confirmed,
        SUM(CASE WHEN Book_Status='completed' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN Book_Status='cancelled' THEN 1 ELSE 0 END) AS cancelled,
        COALESCE(SUM(CASE WHEN Book_Status IN ('confirmed','completed') THEN Book_TotalPrice ELSE 0 END), 0) AS spent
    FROM Bookings WHERE Book_CustId=$custId
")->fetch_assoc();

$title = "My Profile";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:calc(100vh - 64px);">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <div class="page-header">
            <h1>My Profile</h1>
            <p>Manage your personal information and account settings.</p>
        </div>

        <?php if ($success): ?>
        <div class="alert-rd-success mb-4"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert-rd-danger mb-4"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- ── Account Card + Stats ── -->
        <div style="display:flex; gap:24px; margin-bottom:24px; flex-wrap:wrap;">

            <!-- Left: Avatar / Account summary -->
            <div style="width:260px; flex-shrink:0; display:flex; flex-direction:column; gap:16px;">

                <!-- Avatar card -->
                <div style="background:#fff; border-radius:16px; padding:28px 20px; box-shadow:var(--rd-shadow); border:1px solid rgba(228,223,223,0.5); text-align:center;">
                    <div style="width:80px; height:80px; border-radius:50%; background:var(--rd-red-pale); border:3px solid var(--rd-red); display:flex; align-items:center; justify-content:center; font-size:34px; color:var(--rd-red); margin:0 auto 14px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div style="font-size:17px; font-weight:700; color:#111; margin-bottom:3px;">
                        <?= htmlspecialchars($acct['Cust_FName'] . ' ' . $acct['Cust_LName']) ?>
                    </div>
                    <div style="font-size:12px; color:var(--rd-muted); margin-bottom:12px;">
                        <?= htmlspecialchars($acct['Acct_Email']) ?>
                    </div>
                    <span style="background:var(--rd-red-pale); color:var(--rd-red); font-size:11px; font-weight:600; padding:4px 12px; border-radius:20px; border:1px solid rgba(184,0,32,0.15);">
                        <i class="bi bi-patch-check-fill me-1"></i>Customer
                    </span>
                    <?php if ($acct['Cust_Phone']): ?>
                    <div style="margin-top:14px; font-size:12px; color:#666; display:flex; align-items:center; justify-content:center; gap:6px;">
                        <i class="bi bi-telephone-fill" style="color:var(--rd-red); font-size:11px;"></i>
                        <?= htmlspecialchars($acct['Cust_Phone']) ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Quick stats column -->
                <?php
                $sideStats = [
                    ['bi-calendar-check', '#EFF6FF', '#2563EB', $stats['total'],     'Total Bookings'],
                    ['bi-check-circle',   '#F0FFF4', '#16A34A', $stats['confirmed'], 'Confirmed'],
                    ['bi-house-check',    '#FFFBEB', '#D97706', $stats['completed'], 'Completed'],
                    ['bi-x-circle',       '#FFF1F1', '#B80020', $stats['cancelled'] ?? 0, 'Cancelled'],
                ];
                foreach ($sideStats as [$icon, $bg, $col, $val, $lbl]):
                ?>
                <div style="background:#fff; border-radius:12px; padding:14px 18px; box-shadow:var(--rd-shadow); border:1px solid rgba(228,223,223,0.5); display:flex; align-items:center; gap:14px;">
                    <div style="width:40px; height:40px; border-radius:10px; background:<?= $bg ?>; color:<?= $col ?>; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0;">
                        <i class="bi <?= $icon ?>"></i>
                    </div>
                    <div>
                        <div style="font-size:20px; font-weight:800; color:#111; line-height:1;"><?= $val ?></div>
                        <div style="font-size:11px; color:var(--rd-muted); margin-top:2px;"><?= $lbl ?></div>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Total spent -->
                <div style="background:var(--rd-red); border-radius:12px; padding:16px 18px; box-shadow:0 4px 16px rgba(184,0,32,0.25); display:flex; align-items:center; gap:14px;">
                    <div style="width:40px; height:40px; border-radius:10px; background:rgba(255,255,255,0.2); color:#fff; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0;">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div>
                        <div style="font-size:18px; font-weight:800; color:#fff; line-height:1;">&#8369;<?= number_format($stats['spent']) ?></div>
                        <div style="font-size:11px; color:rgba(255,255,255,0.75); margin-top:2px;">Total Spent</div>
                    </div>
                </div>

            </div>

            <!-- Right: Forms stacked -->
            <div style="flex:1; min-width:0; display:flex; flex-direction:column; gap:20px;">

                <!-- Personal Info -->
                <div style="background:#fff; border-radius:14px; padding:28px; box-shadow:var(--rd-shadow); border:1px solid rgba(228,223,223,0.5);">
                    <h5 style="font-size:14px; font-weight:700; margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid var(--rd-border);">
                        <i class="bi bi-person me-2" style="color:var(--rd-red);"></i>Personal Information
                    </h5>
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label">First Name <span style="color:var(--rd-red)">*</span></label>
                                <input type="text" name="fname" class="form-control"
                                       value="<?= htmlspecialchars($acct['Cust_FName']) ?>" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Last Name <span style="color:var(--rd-red)">*</span></label>
                                <input type="text" name="lname" class="form-control"
                                       value="<?= htmlspecialchars($acct['Cust_LName']) ?>" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control"
                                       value="<?= htmlspecialchars($acct['Acct_Email']) ?>" disabled
                                       style="background:#F5F2F2; color:#888;">
                                <div style="font-size:11px; color:#aaa; margin-top:4px;"><i class="bi bi-lock-fill me-1"></i>Email cannot be changed</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" id="phone" class="form-control"
                                       placeholder="e.g. 0917-123-4567" maxlength="13"
                                       oninput="formatPhone(this)"
                                       value="<?= htmlspecialchars($acct['Cust_Phone'] ?? '') ?>">
                                <div style="font-size:11px; color:#aaa; margin-top:4px;">Format: xxxx-xxx-xxxx</div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" name="update_profile" class="btn-rd" style="padding:10px 28px;">
                                <i class="bi bi-check-lg me-1"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password -->
                <div style="background:#fff; border-radius:14px; padding:28px; box-shadow:var(--rd-shadow); border:1px solid rgba(228,223,223,0.5);">
                    <h5 style="font-size:14px; font-weight:700; margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid var(--rd-border);">
                        <i class="bi bi-lock me-2" style="color:var(--rd-red);"></i>Change Password
                    </h5>
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Current Password <span style="color:var(--rd-red)">*</span></label>
                                <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">New Password <span style="color:var(--rd-red)">*</span></label>
                                <input type="password" name="new_password" class="form-control" required minlength="6" autocomplete="new-password">
                                <div style="font-size:11px; color:#aaa; margin-top:4px;">Minimum 6 characters</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Confirm New Password <span style="color:var(--rd-red)">*</span></label>
                                <input type="password" name="confirm_password" class="form-control" required autocomplete="new-password">
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" name="change_password" class="btn-rd" style="padding:10px 28px;">
                                <i class="bi bi-shield-lock me-1"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
function formatPhone(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 11);
    if (v.length > 7)      v = v.substring(0,4) + '-' + v.substring(4,7) + '-' + v.substring(7);
    else if (v.length > 4) v = v.substring(0,4) + '-' + v.substring(4);
    input.value = v;
}
// Format on page load in case value is already stored without dashes
(function() {
    var el = document.getElementById('phone');
    if (el && el.value) formatPhone(el);
})();
</script>

<?php include "../layout/footer.php"; ?>
