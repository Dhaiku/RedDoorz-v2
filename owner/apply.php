<?php
$title = "Become a Hotel Partner";
require_once "../config/db.php";
include "../layout/layout.php";

$success = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    $fullName    = trim($conn->real_escape_string($_POST['full_name']    ?? ''));
    $email       = trim($conn->real_escape_string($_POST['email']        ?? ''));
    $phone       = trim($conn->real_escape_string($_POST['phone']        ?? ''));
    $hotelName   = trim($conn->real_escape_string($_POST['hotel_name']   ?? ''));
    $hotelCity   = trim($conn->real_escape_string($_POST['hotel_city']   ?? ''));
    $hotelAddr   = trim($conn->real_escape_string($_POST['hotel_address']?? ''));
    $roomCount   = max(1, (int)($_POST['room_count'] ?? 1));
    $message     = trim($conn->real_escape_string($_POST['message']      ?? ''));

    if (!$fullName || !$email || !$phone || !$hotelName || !$hotelCity) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var(str_replace('\x27','',$_POST['email']), FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $hasTable = $conn->query("SHOW TABLES LIKE 'OwnerApplications'")->num_rows > 0;
        if ($hasTable) {
            $conn->query("
                INSERT INTO OwnerApplications
                    (App_FullName, App_Email, App_Phone, App_HotelName, App_HotelCity, App_HotelAddress, App_RoomCount, App_Message)
                VALUES
                    ('$fullName','$email','$phone','$hotelName','$hotelCity','$hotelAddr',$roomCount,'$message')
            ");
            $success = true;
        } else {
            $error = 'Applications are currently unavailable. Please try again later.';
        }
    }
}
?>

<div class="container" style="max-width:760px; padding:52px 16px 80px;">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-rd mb-4">
        <a href="/index.php">Home</a>
        <i class="bi bi-chevron-right" style="font-size:10px;"></i>
        <span style="color:#333;">Become a Partner</span>
    </nav>

    <!-- Header -->
    <div style="text-align:center; margin-bottom:40px;" data-aos="fade-up">
        <div style="
            width:60px; height:60px; margin:0 auto 16px;
            background:var(--rd-red-pale); border-radius:16px;
            display:flex; align-items:center; justify-content:center;
            font-size:26px; color:var(--rd-red);
        "><i class="bi bi-building-fill-add"></i></div>
        <div class="section-label">Hotel Partner Program</div>
        <h1 class="section-title" style="font-size:26px;">Apply to Become a RedDoorz Partner</h1>
        <p class="section-subtitle" style="max-width:480px; margin:0 auto;">
            Fill in the form below and our team will review your application within 2–3 business days.
        </p>
    </div>

    <?php if ($success): ?>
    <div style="background:#F0FFF4; border:1px solid #BBF7D0; color:#15803D; border-radius:14px; padding:28px 32px; text-align:center;" data-aos="fade-up">
        <div style="font-size:44px; margin-bottom:12px;"><i class="bi bi-check-circle-fill"></i></div>
        <h4 style="font-size:18px; font-weight:700; margin:0 0 8px;">Application Submitted!</h4>
        <p style="font-size:14px; margin:0 0 20px; opacity:0.85;">
            Thank you! We'll review your application and get back to you at <strong><?= htmlspecialchars($_POST['email'] ?? '') ?></strong> within 2–3 business days.
        </p>
        <a href="/index.php" class="btn-rd" style="padding:10px 28px; justify-content:center;">
            <i class="bi bi-house me-1"></i>Back to Home
        </a>
    </div>

    <?php else: ?>

    <?php if ($error): ?>
    <div class="alert-rd-danger mb-4" style="display:flex; align-items:center; gap:9px;">
        <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <div class="card-form" data-aos="fade-up">
        <form method="POST" novalidate>

            <h5 style="font-size:15px; font-weight:700; margin:0 0 20px; padding-bottom:14px; border-bottom:1px solid var(--rd-border);">
                <i class="bi bi-person me-2" style="color:var(--rd-red);"></i>Your Information
            </h5>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Full Name <span style="color:var(--rd-red)">*</span></label>
                    <input type="text" name="full_name" class="form-control"
                           placeholder="Juan dela Cruz"
                           value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Address <span style="color:var(--rd-red)">*</span></label>
                    <input type="email" name="email" class="form-control"
                           placeholder="you@email.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone Number <span style="color:var(--rd-red)">*</span></label>
                    <input type="tel" name="phone" class="form-control"
                           placeholder="0917-xxx-xxxx"
                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                </div>
            </div>

            <h5 style="font-size:15px; font-weight:700; margin:0 0 20px; padding-bottom:14px; border-bottom:1px solid var(--rd-border);">
                <i class="bi bi-building me-2" style="color:var(--rd-red);"></i>Hotel Information
            </h5>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Hotel Name <span style="color:var(--rd-red)">*</span></label>
                    <input type="text" name="hotel_name" class="form-control"
                           placeholder="e.g. Sunset Inn"
                           value="<?= htmlspecialchars($_POST['hotel_name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">City / Location <span style="color:var(--rd-red)">*</span></label>
                    <input type="text" name="hotel_city" class="form-control"
                           placeholder="e.g. Cebu City"
                           value="<?= htmlspecialchars($_POST['hotel_city'] ?? '') ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Hotel Address</label>
                    <input type="text" name="hotel_address" class="form-control"
                           placeholder="Street address, barangay"
                           value="<?= htmlspecialchars($_POST['hotel_address'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Number of Rooms <span style="color:var(--rd-red)">*</span></label>
                    <input type="number" name="room_count" class="form-control"
                           min="1" max="500"
                           value="<?= htmlspecialchars($_POST['room_count'] ?? '1') ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Additional Message <span style="color:var(--rd-muted); font-weight:400;">(optional)</span></label>
                    <textarea name="message" class="form-control" rows="4"
                              placeholder="Tell us more about your property, special features, or any questions..."
                              style="resize:vertical;"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Info note -->
            <div style="background:var(--rd-red-pale); border:1px solid rgba(184,0,32,0.14); border-radius:8px; padding:12px 16px; font-size:13px; color:#7A001A; margin-bottom:24px; display:flex; align-items:flex-start; gap:9px;">
                <i class="bi bi-info-circle" style="flex-shrink:0; margin-top:1px; font-size:15px;"></i>
                <span>
                    By submitting, you agree that our team will contact you to verify your property details.
                    Hotel owners earn <strong>85%</strong> of every confirmed booking.
                </span>
            </div>

            <button type="submit" name="apply" class="btn-rd w-100"
                    style="justify-content:center; padding:13px; font-size:15px;">
                <i class="bi bi-send me-1"></i>Submit Application
            </button>

        </form>
    </div>
    <?php endif; ?>

</div>

<?php include "../layout/footer.php"; ?>
