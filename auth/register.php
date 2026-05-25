<?php
session_start();
require_once "../config/db.php";

if (isset($_SESSION['account_id'])) { header("Location: /index.php"); exit(); }

$error = ""; $success = "";

if (isset($_POST['register'])) {
    $fname    = trim($_POST['fname']   ?? '');
    $lname    = trim($_POST['lname']   ?? '');
    $phone    = trim($_POST['phone']   ?? '');
    $email    = trim($_POST['email']   ?? '');
    $password = $_POST['password']     ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $phoneDigits = preg_replace('/\D/', '', $phone);

    if (empty($fname) || empty($lname) || empty($email) || empty($password)) {
        $error = "All required fields must be filled.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/\.com$/i', $email)) {
        $error = "Please enter a valid email address ending in .com (e.g. you@example.com).";
    } elseif ($phone !== '' && (strlen($phoneDigits) !== 11 || substr($phoneDigits, 0, 2) !== '09')) {
        $error = "Phone number must be a valid 11-digit PH number starting with 09 (e.g. 0917-123-4567).";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $existing = fs_find('accounts', [['email', '=', $email]]);
        if ($existing) {
            $error = "An account with this email already exists.";
        } else {
            if ($phoneDigits) {
                $phone = substr($phoneDigits,0,4).'-'.substr($phoneDigits,4,3).'-'.substr($phoneDigits,7,4);
            }
            $acctId = fs_insert('accounts', [
                'email'    => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role'     => 'customer',
                'status'   => 'active',
                'mustChangePassword' => false,
            ]);
            fs_insert('customers', [
                'acctId'    => $acctId,
                'firstName' => $fname,
                'lastName'  => $lname,
                'phone'     => $phone,
            ]);
            $success = "Account created successfully. You can now log in.";
        }
    }
}

$title = "Sign Up";
include "../layout/layout.php";
?>

<style>
.auth-wrapper { flex:1; display:flex; align-items:stretch; }
.auth-panel-img { display:none; flex:0 0 46%; position:relative; overflow:hidden; min-height:100%; }
.auth-panel-img img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
.auth-panel-img .img-overlay {
    position:absolute; inset:0;
    background:linear-gradient(160deg, rgba(100,0,12,0.72) 0%, rgba(184,0,32,0.55) 100%);
    display:flex; flex-direction:column; justify-content:flex-end; padding:48px;
}
.auth-panel-form { flex:1; display:flex; align-items:center; justify-content:center; background:#F5F2F2; padding:48px 24px; }
@media (min-width:960px) { .auth-panel-img { display:flex; } }
</style>

<div class="auth-wrapper">
    <div class="auth-panel-img">
        <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?q=80&w=800&auto=format&fit=crop" alt="Hotel interior">
        <div class="img-overlay">
            <div style="color:#fff;">
                <div style="font-size:22px; font-weight:700; margin-bottom:10px; display:flex; align-items:center; gap:8px;">
                    <i class="bi bi-door-open-fill"></i> RedDoorz
                </div>
                <p style="font-size:15px; color:rgba(255,255,255,0.78); line-height:1.65; margin:0 0 20px; max-width:320px;">
                    Join thousands of travelers who book affordable, quality stays across the Philippines.
                </p>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <?php foreach ([['bi-check-circle-fill','No booking fees, ever'],['bi-check-circle-fill','Free cancellation on most bookings'],['bi-check-circle-fill','Exclusive member-only deals']] as [$ic,$txt]): ?>
                    <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:rgba(255,255,255,0.82);">
                        <i class="bi <?= $ic ?>" style="color:rgba(255,255,255,0.65); font-size:12px;"></i> <?= $txt ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="auth-panel-form">
        <div style="width:100%; max-width:440px;">
            <div class="text-center mb-4">
                <a href="/index.php" style="color:var(--rd-red); font-size:22px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:7px;">
                    <span style="background:var(--rd-red-pale); border:1px solid rgba(184,0,32,0.18); border-radius:8px; width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; font-size:17px;"><i class="bi bi-door-open-fill"></i></span>
                    RedDoorz
                </a>
                <p style="color:var(--rd-muted); font-size:14px; margin:10px 0 0;">Create your free account</p>
            </div>
            <div class="card-form">
                <?php if ($error): ?>
                <div class="alert-rd-danger mb-4"><i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                <div class="alert-rd-success mb-4">
                    <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                    <a href="/auth/login.php" style="color:#166534; font-weight:600; margin-left:6px;">Go to Login &rarr;</a>
                </div>
                <?php endif; ?>
                <?php if (!$success): ?>
                <form method="POST" novalidate>
                    <div class="row g-3 mb-0">
                        <div class="col-6">
                            <label class="form-label">First Name <span style="color:var(--rd-red)">*</span></label>
                            <input type="text" name="fname" class="form-control" placeholder="Juan" value="<?= htmlspecialchars($_POST['fname'] ?? '') ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Last Name <span style="color:var(--rd-red)">*</span></label>
                            <input type="text" name="lname" class="form-control" placeholder="Dela Cruz" value="<?= htmlspecialchars($_POST['lname'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" id="reg_phone" class="form-control" placeholder="e.g. 0917-123-4567" maxlength="13" oninput="formatPhone(this)" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                        <div style="font-size:11px; color:#aaa; margin-top:4px;">Format: xxxx-xxx-xxxx</div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Email Address <span style="color:var(--rd-red)">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="you@email.com" pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Password <span style="color:var(--rd-red)">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                    </div>
                    <div class="mt-3 mb-4">
                        <label class="form-label">Confirm Password <span style="color:var(--rd-red)">*</span></label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
                    </div>
                    <button type="submit" name="register" class="btn-rd w-100" style="justify-content:center; padding:12px; font-size:15px;">
                        <i class="bi bi-person-check"></i> Create Account
                    </button>
                </form>
                <?php endif; ?>
                <hr class="divider">
                <p class="text-center" style="font-size:14px; margin:0; color:#666;">
                    Already have an account? <a href="/auth/login.php" style="color:var(--rd-red); font-weight:600;">Sign in</a>
                </p>
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
</script>
<?php include "../layout/footer.php"; ?>
