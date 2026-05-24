<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'hotel_owner') {
    header("Location: /auth/login.php"); exit();
}

$hotelId = (int) ($_SESSION['hotel_id'] ?? 0);
if (!$hotelId) { header("Location: /auth/logout.php"); exit(); }

// Check if Hotel_Amenities column exists
$hasAmenities = $conn->query("SHOW COLUMNS FROM Hotels LIKE 'Hotel_Amenities'")->num_rows > 0;

$allAmenities = [
    'free_wifi'       => ['bi-wifi',             'Free WiFi'],
    'air_conditioning'=> ['bi-thermometer-half', 'Air Conditioning'],
    'hot_shower'      => ['bi-droplet',          'Hot Shower'],
    'cable_tv'        => ['bi-tv',               'Cable TV'],
    'breakfast'       => ['bi-cup-hot',          'Breakfast Option'],
    'parking'         => ['bi-p-square',         'Parking Available'],
    'swimming_pool'   => ['bi-water',            'Swimming Pool'],
    'gym'             => ['bi-bicycle',          'Fitness Center'],
    'restaurant'      => ['bi-shop',             'Restaurant'],
    'room_service'    => ['bi-bell',             'Room Service'],
    'laundry'         => ['bi-bag',              'Laundry Service'],
    'airport_shuttle' => ['bi-bus-front',        'Airport Shuttle'],
];

$msg = ''; $error = '';

// Handle hotel profile update
if (isset($_POST['save_profile'])) {
    $name  = $conn->real_escape_string(trim($_POST['hotel_name']        ?? ''));
    $city  = $conn->real_escape_string(trim($_POST['hotel_city']        ?? ''));
    $addr  = $conn->real_escape_string(trim($_POST['hotel_address']     ?? ''));
    $desc  = $conn->real_escape_string(trim($_POST['hotel_description'] ?? ''));

    if ($name === '' || $city === '') {
        $error = 'Hotel name and city are required.';
    } else {
        if ($hasAmenities) {
            $selectedAmenities = $_POST['amenities'] ?? [];
            $validKeys = array_keys($allAmenities);
            $selectedAmenities = array_filter($selectedAmenities, fn($k) => in_array($k, $validKeys));
            $amenitiesStr = $conn->real_escape_string(implode(',', array_keys($selectedAmenities)));
            $conn->query("UPDATE Hotels SET Hotel_Name='$name', Hotel_City='$city', Hotel_Address='$addr', Hotel_Description='$desc', Hotel_Amenities='$amenitiesStr' WHERE Hotel_Id=$hotelId AND Hotel_OwnerId={$_SESSION['account_id']}");
        } else {
            $conn->query("UPDATE Hotels SET Hotel_Name='$name', Hotel_City='$city', Hotel_Address='$addr', Hotel_Description='$desc' WHERE Hotel_Id=$hotelId AND Hotel_OwnerId={$_SESSION['account_id']}");
        }
        $msg = 'Hotel profile updated successfully.';
        header("Location: hotel_profile.php?msg=" . urlencode($msg)); exit();
    }
}

// Fetch current hotel data
$hotel = $conn->query("SELECT * FROM Hotels WHERE Hotel_Id=$hotelId LIMIT 1")->fetch_assoc();
if (!$hotel) { header("Location: /auth/logout.php"); exit(); }

$savedAmenities = [];
if ($hasAmenities && !empty($hotel['Hotel_Amenities'])) {
    $savedAmenities = explode(',', $hotel['Hotel_Amenities']);
}

$title = "Hotel Profile";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:auto;">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible; max-width:860px;">

        <div class="page-header">
            <h1>Hotel Profile</h1>
            <p>Edit your hotel's public listing details — name, location, description, and amenities.</p>
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

        <form method="POST">

            <!-- Basic Info -->
            <div style="background:#fff; border-radius:14px; padding:28px; box-shadow:var(--rd-shadow); margin-bottom:24px; border:1px solid rgba(228,223,223,0.5);">
                <h5 style="font-size:15px; font-weight:700; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                    <i class="bi bi-building" style="color:var(--rd-red);"></i> Basic Information
                </h5>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Hotel Name <span style="color:var(--rd-red)">*</span></label>
                        <input type="text" name="hotel_name" class="form-control"
                               value="<?= htmlspecialchars($hotel['Hotel_Name']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City <span style="color:var(--rd-red)">*</span></label>
                        <input type="text" name="hotel_city" class="form-control"
                               value="<?= htmlspecialchars($hotel['Hotel_City']) ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Full Address</label>
                        <input type="text" name="hotel_address" class="form-control"
                               placeholder="e.g. Station 1, White Beach, Boracay Island"
                               value="<?= htmlspecialchars($hotel['Hotel_Address'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Hotel Description</label>
                        <textarea name="hotel_description" class="form-control" rows="4"
                                  placeholder="Describe your hotel — location highlights, unique features, atmosphere..."></textarea>
                        <script>
                        // Pre-fill textarea (avoids HTML escaping issues with value attr)
                        document.currentScript.previousElementSibling.value = <?= json_encode($hotel['Hotel_Description'] ?? '') ?>;
                        </script>
                        <div style="font-size:12px; color:var(--rd-muted); margin-top:5px;">
                            This appears in the "About This Hotel" section on your listing.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amenities -->
            <div style="background:#fff; border-radius:14px; padding:28px; box-shadow:var(--rd-shadow); margin-bottom:24px; border:1px solid rgba(228,223,223,0.5);">
                <h5 style="font-size:15px; font-weight:700; margin-bottom:6px; display:flex; align-items:center; gap:8px;">
                    <i class="bi bi-stars" style="color:var(--rd-red);"></i> Hotel Amenities
                </h5>
                <p style="font-size:13px; color:var(--rd-muted); margin-bottom:20px;">
                    Select all amenities available at your hotel. These are shown on your public listing.
                </p>

                <?php if (!$hasAmenities): ?>
                <div style="background:#FFF8E1; border:1px solid #FFE082; border-radius:8px; padding:14px; color:#7B5800; font-size:13px;">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Run <code>config/migration_amenities.sql</code> to enable amenity selection.
                    Amenities will show as the default set until then.
                </div>
                <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($allAmenities as $key => [$icon, $label]):
                        $checked = in_array($key, $savedAmenities);
                    ?>
                    <div class="col-6 col-md-4">
                        <label style="
                            display:flex; align-items:center; gap:10px;
                            padding:12px 14px; border-radius:10px; cursor:pointer;
                            border:1.5px solid <?= $checked ? 'var(--rd-red)' : 'var(--rd-border)' ?>;
                            background:<?= $checked ? 'var(--rd-red-pale)' : '#fff' ?>;
                            transition:all 0.15s;
                            font-size:13px; font-weight:500; color:#333;
                            user-select:none;
                        " onclick="toggleAmenity(this)">
                            <input type="checkbox" name="amenities[<?= $key ?>]" value="1"
                                   <?= $checked ? 'checked' : '' ?>
                                   style="display:none;">
                            <i class="bi <?= $icon ?>" style="font-size:16px; color:<?= $checked ? 'var(--rd-red)' : '#aaa' ?>; width:20px; text-align:center; flex-shrink:0;"></i>
                            <?= $label ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Save button -->
            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <a href="/owner/dashboard.php" class="btn-rd-outline" style="padding:10px 28px;">Cancel</a>
                <button type="submit" name="save_profile" class="btn-rd" style="padding:10px 28px;">
                    <i class="bi bi-check-lg me-1"></i>Save Changes
                </button>
            </div>

        </form>

    </div>
</div>

<script>
function toggleAmenity(label) {
    const cb   = label.querySelector('input[type="checkbox"]');
    const icon = label.querySelector('i.bi');
    cb.checked = !cb.checked;
    if (cb.checked) {
        label.style.borderColor = 'var(--rd-red)';
        label.style.background  = 'var(--rd-red-pale)';
        icon.style.color        = 'var(--rd-red)';
    } else {
        label.style.borderColor = 'var(--rd-border)';
        label.style.background  = '#fff';
        icon.style.color        = '#aaa';
    }
}
</script>

<?php include "../layout/footer.php"; ?>
