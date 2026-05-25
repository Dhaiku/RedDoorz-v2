<?php
session_start();
require_once "../config/db.php";

if (isset($_SESSION['account_id'])) {
    header("Location: /index.php"); exit();
}

$error = "";

if (isset($_POST['login'])) {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $account = fs_find('accounts', [['email', '=', $email], ['status', '=', 'active']]);

    if ($account && password_verify($password, $account['password'])) {
        $_SESSION['account_id'] = $account['id'];
        $_SESSION['role']       = $account['role'];
        $_SESSION['email']      = $account['email'];

        if ($account['role'] === 'admin') {
            $_SESSION['display_name'] = 'Admin';
            if (!empty($account['mustChangePassword'])) {
                header("Location: change_password.php"); exit();
            }
            header("Location: /admin/dashboard.php"); exit();

        } elseif ($account['role'] === 'hotel_owner') {
            $hotel = fs_find('hotels', [['ownerId', '=', $account['id']]]);
            $_SESSION['display_name'] = $hotel['name'] ?? 'Hotel Owner';
            $_SESSION['hotel_id']     = $hotel['id']   ?? null;
            if (!empty($account['mustChangePassword'])) {
                header("Location: change_password.php"); exit();
            }
            header("Location: /owner/dashboard.php"); exit();

        } else {
            $cust = fs_find('customers', [['acctId', '=', $account['id']]]);
            $_SESSION['display_name'] = ($cust['firstName'] ?? '') . ' ' . ($cust['lastName'] ?? '');
            $_SESSION['customer_id']  = $cust['id'] ?? null;
            if (!empty($account['mustChangePassword'])) {
                header("Location: change_password.php"); exit();
            }
            header("Location: /customer/dashboard.php"); exit();
        }
    } else {
        $error = $account ? "Incorrect password. Please try again." : "No account found with that email address.";
    }
}

$title = "Login";
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
                <p style="font-size:15px; color:rgba(255,255,255,0.78); line-height:1.65; margin:0; max-width:320px;">
                    Comfortable stays at honest prices &mdash; across every major destination in the Philippines.
                </p>
                <div style="display:flex; gap:20px; margin-top:24px;">
                    <?php foreach ([['200+','Properties'],['4.8','Guest Rating'],['50k+','Bookings']] as [$val,$lbl]): ?>
                    <div>
                        <div style="font-size:22px; font-weight:700; color:#fff;"><?= $val ?></div>
                        <div style="font-size:12px; color:rgba(255,255,255,0.55);"><?= $lbl ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="auth-panel-form">
        <div style="width:100%; max-width:400px;">
            <div class="text-center mb-5">
                <a href="/index.php" style="color:var(--rd-red); font-size:22px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:7px;">
                    <span style="background:var(--rd-red-pale); border:1px solid rgba(184,0,32,0.18); border-radius:8px; width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; font-size:17px;"><i class="bi bi-door-open-fill"></i></span>
                    RedDoorz
                </a>
                <p style="color:var(--rd-muted); font-size:14px; margin:10px 0 0;">Sign in to your account</p>
            </div>
            <div class="card-form">
                <?php if ($error): ?>
                <div class="alert-rd-danger mb-4"><i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST" novalidate autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="you@email.com" autocomplete="off" value="" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" autocomplete="new-password" required>
                    </div>
                    <button type="submit" name="login" class="btn-rd w-100" style="justify-content:center; padding:12px; font-size:15px;">
                        <i class="bi bi-box-arrow-in-right"></i> Sign In
                    </button>
                </form>
                <hr class="divider">
                <p class="text-center" style="font-size:14px; margin:0; color:#666;">
                    No account yet? <a href="/auth/register.php" style="color:var(--rd-red); font-weight:600;">Create one free</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include "../layout/footer.php"; ?>
