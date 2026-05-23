<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id'])) {
    header("Location: login.php"); exit();
}

$error   = "";
$success = "";

if (isset($_POST['change_password'])) {
    $newPwd  = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (strlen($newPwd) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($newPwd !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hashed = password_hash($newPwd, PASSWORD_DEFAULT);
        $id     = (int) $_SESSION['account_id'];
        $stmt   = $conn->prepare("UPDATE Accounts SET Acct_Password=?, Acct_MustChangePassword=0 WHERE Acct_Id=?");
        $stmt->bind_param("si", $hashed, $id);
        if ($stmt->execute()) {
            session_destroy();
            header("Location: login.php"); exit();
        } else {
            $error = "Error updating password.";
        }
    }
}

$title = "Change Password";
include "../layout/layout.php";
?>

<div style="min-height:calc(100vh - 64px); display:flex; align-items:center; justify-content:center; padding:40px 16px; background:#F5F5F5;">
    <div style="width:100%; max-width:420px;">

        <div class="text-center mb-4">
            <a href="/index.php" style="color:#E8002D; font-size:22px; font-weight:700; text-decoration:none;">
                <i class="bi bi-door-open-fill me-1"></i>RedDoorz
            </a>
            <p style="color:#757575; font-size:14px; margin:8px 0 0;">Set a new password</p>
        </div>

        <div class="card-form">

            <?php if ($error): ?>
                <div class="alert-rd-danger mb-4">
                    <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control"
                           placeholder="Min. 6 characters" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control"
                           placeholder="Repeat new password" required>
                </div>

                <button type="submit" name="change_password" class="btn-rd w-100" style="justify-content:center; padding:12px; font-size:15px;">
                    Update Password
                </button>
            </form>

        </div>
    </div>
</div>

<?php include "../layout/footer.php"; ?>
