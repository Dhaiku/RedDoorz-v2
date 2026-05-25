<?php
require_once "../config/db.php";
if (($_SESSION['role'] ?? '') === 'hotel_owner') {
    header("Location: /owner/dashboard.php"); exit();
}
if (($_SESSION['role'] ?? '') === 'admin') {
    header("Location: /admin/dashboard.php"); exit();
}

$hotelId  = (int) ($_GET['id'] ?? 0);
$checkin  = $_GET['checkin']  ?? '';
$checkout = $_GET['checkout'] ?? '';

if (!$hotelId) { header("Location: search.php"); exit(); }

$hotel = $conn->query("SELECT * FROM Hotels WHERE Hotel_Id=$hotelId AND Hotel_Status='active' LIMIT 1")->fetch_assoc();
if (!$hotel) { header("Location: search.php"); exit(); }

$datesGiven = ($checkin && $checkout && $checkin < $checkout);
$ciEsc = $datesGiven ? $conn->real_escape_string($checkin)  : '';
$coEsc = $datesGiven ? $conn->real_escape_string($checkout) : '';

if ($datesGiven) {
    $rooms = $conn->query("
        SELECT r.*,
            (SELECT COUNT(*) FROM Bookings bk
             WHERE bk.Book_RoomId   = r.Room_Id
               AND bk.Book_Status  NOT IN ('cancelled')
               AND bk.Book_CheckIn  < '$coEsc'
               AND bk.Book_CheckOut > '$ciEsc'
            ) AS ConflictCount
        FROM Rooms r
        WHERE r.Room_HotelId=$hotelId AND r.Room_Status='available'
        ORDER BY ConflictCount ASC, r.Room_Price ASC
    ");
} else {
    $rooms = $conn->query("
        SELECT r.*, 0 AS ConflictCount
        FROM Rooms r
        WHERE r.Room_HotelId=$hotelId AND r.Room_Status='available'
        ORDER BY r.Room_Price ASC
    ");
}

$title = htmlspecialchars($hotel['Hotel_Name']);
include "../layout/layout.php";

$imgSeed = 'reddoorz' . $hotelId;
?>

<!-- ===== HOTEL HERO IMAGE ===== -->
<div style="height:340px; position:relative; overflow:hidden; margin-top:0;">
    <img src="https://picsum.photos/seed/<?= $imgSeed ?>/1400/600"
         alt="<?= htmlspecialchars($hotel['Hotel_Name']) ?>"
         style="width:100%; height:100%; object-fit:cover;">
    <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.68) 0%, rgba(0,0,0,0.15) 60%, transparent 100%);"></div>
    <div style="position:absolute; bottom:28px; left:0; right:0;">
        <div class="container">
            <div class="breadcrumb-rd" style="color:rgba(255,255,255,0.65); margin-bottom:8px;">
                <a href="/index.php" style="color:rgba(255,255,255,0.6);">Home</a>
                <i class="bi bi-chevron-right" style="font-size:10px;"></i>
                <a href="/hotels/search.php" style="color:rgba(255,255,255,0.6);">Hotels</a>
                <i class="bi bi-chevron-right" style="font-size:10px;"></i>
                <span style="color:rgba(255,255,255,0.9);"><?= htmlspecialchars($hotel['Hotel_Name']) ?></span>
            </div>
            <h1 style="font-size:clamp(22px,3.5vw,34px); font-weight:700; color:#fff; margin:0 0 6px; letter-spacing:-0.3px;">
                <?= htmlspecialchars($hotel['Hotel_Name']) ?>
            </h1>
            <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
                <span style="font-size:13px; color:rgba(255,255,255,0.78); display:flex; align-items:center; gap:5px;">
                    <i class="bi bi-geo-alt-fill" style="font-size:12px;"></i>
                    <?= htmlspecialchars($hotel['Hotel_City']) ?>
                </span>
                <span style="
                    background:rgba(184,0,32,0.85); backdrop-filter:blur(4px);
                    color:#fff; font-size:12px; font-weight:700;
                    padding:4px 12px; border-radius:20px;
                    display:inline-flex; align-items:center; gap:5px;
                ">
                    <i class="bi bi-star-fill" style="font-size:10px; color:#F5C842;"></i>
                    <?= number_format($hotel['Hotel_Rating'], 1) ?> / 5.0
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container" style="padding: 36px 12px 80px;">
    <div class="row g-4">

        <!-- ===== LEFT: Hotel Info ===== -->
        <div class="col-lg-8">

            <!-- About -->
            <div style="background:#fff; border-radius:14px; padding:24px; box-shadow:var(--rd-shadow); margin-bottom:20px; border:1px solid rgba(228,223,223,0.5);" data-aos="fade-up">
                <h5 style="font-size:15px; font-weight:700; margin-bottom:12px;">About This Hotel</h5>
                <p style="font-size:14px; color:#555; line-height:1.72; margin-bottom:14px;">
                    <?= htmlspecialchars($hotel['Hotel_Description'] ?? 'A comfortable and affordable stay awaits you at this property.') ?>
                </p>
                <div style="display:flex; align-items:center; gap:7px; font-size:13px; color:#555;">
                    <i class="bi bi-geo-alt" style="color:var(--rd-red);"></i>
                    <?= htmlspecialchars($hotel['Hotel_Address'] ?? $hotel['Hotel_City']) ?>
                </div>
            </div>

            <!-- Amenities -->
            <?php
            $allAmenityMap = [
                'free_wifi'        => ['bi-wifi',             'Free WiFi'],
                'air_conditioning' => ['bi-thermometer-half', 'Air Conditioning'],
                'hot_shower'       => ['bi-droplet',          'Hot Shower'],
                'cable_tv'         => ['bi-tv',               'Cable TV'],
                'breakfast'        => ['bi-cup-hot',          'Breakfast Option'],
                'parking'          => ['bi-p-square',         'Parking Available'],
                'swimming_pool'    => ['bi-water',            'Swimming Pool'],
                'gym'              => ['bi-bicycle',          'Fitness Center'],
                'restaurant'       => ['bi-shop',             'Restaurant'],
                'room_service'     => ['bi-bell',             'Room Service'],
                'laundry'          => ['bi-bag',              'Laundry Service'],
                'airport_shuttle'  => ['bi-bus-front',        'Airport Shuttle'],
            ];
            $hasAmenCol = $conn->query("SHOW COLUMNS FROM Hotels LIKE 'Hotel_Amenities'")->num_rows > 0;
            if ($hasAmenCol && !empty($hotel['Hotel_Amenities'])) {
                $amenityKeys = explode(',', $hotel['Hotel_Amenities']);
                $amenities   = array_filter($allAmenityMap, fn($k) => in_array($k, $amenityKeys), ARRAY_FILTER_USE_KEY);
            } else {
                // Default set shown before migration or if none selected
                $amenities = array_slice($allAmenityMap, 0, 6, true);
            }
            ?>
            <?php if (!empty($amenities)): ?>
            <div style="background:#fff; border-radius:14px; padding:24px; box-shadow:var(--rd-shadow); margin-bottom:20px; border:1px solid rgba(228,223,223,0.5);" data-aos="fade-up">
                <h5 style="font-size:15px; font-weight:700; margin-bottom:18px;">Hotel Amenities</h5>
                <div class="row g-3">
                    <?php foreach ($amenities as [$icon, $label]): ?>
                    <div class="col-6 col-md-4">
                        <div style="
                            display:flex; align-items:center; gap:9px;
                            font-size:13px; color:#444;
                            padding:10px 12px; border-radius:8px;
                            background: var(--rd-bg);
                            border:1px solid var(--rd-border);
                        ">
                            <i class="bi <?= $icon ?>" style="color:var(--rd-red); font-size:15px;"></i>
                            <?= $label ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Available Rooms -->
            <div style="background:#fff; border-radius:14px; padding:24px; box-shadow:var(--rd-shadow); border:1px solid rgba(228,223,223,0.5);" data-aos="fade-up">
                <h5 style="font-size:15px; font-weight:700; margin-bottom:20px;">Available Rooms</h5>

                <?php if ($rooms->num_rows === 0): ?>
                    <div style="text-align:center; padding:40px 0; color:var(--rd-muted); font-size:14px;">
                        <i class="bi bi-door-closed" style="font-size:40px; display:block; margin-bottom:12px; color:#ddd;"></i>
                        No rooms available at this time.
                    </div>
                <?php else: ?>
                    <?php while ($room = $rooms->fetch_assoc()): ?>
                    <div style="
                        border:1.5px solid var(--rd-border); border-radius:12px;
                        padding:18px 20px; margin-bottom:14px;
                        display:flex; justify-content:space-between; align-items:center;
                        gap:16px; flex-wrap:wrap;
                        transition: border-color 0.18s, box-shadow 0.18s;
                    "
                    onmouseover="this.style.borderColor='var(--rd-red)';this.style.boxShadow='0 4px 18px rgba(184,0,32,0.1)'"
                    onmouseout="this.style.borderColor='var(--rd-border)';this.style.boxShadow='none'">
                        <div style="flex:1; min-width:180px;">
                            <div style="font-size:15px; font-weight:700; margin-bottom:4px;">
                                <?= htmlspecialchars($room['Room_Type']) ?>
                            </div>
                            <div style="font-size:13px; color:var(--rd-muted); margin-bottom:8px;">
                                <?= htmlspecialchars($room['Room_Description'] ?? '') ?>
                            </div>
                            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                                <span style="font-size:12px; color:#666; display:flex; align-items:center; gap:4px;">
                                    <i class="bi bi-people" style="color:var(--rd-red);"></i>Up to <?= $room['Room_Capacity'] ?> guests
                                </span>
                                <span style="font-size:12px; color:#666; display:flex; align-items:center; gap:4px;">
                                    <i class="bi bi-wifi" style="color:var(--rd-red);"></i>Free WiFi
                                </span>
                                <span style="font-size:12px; color:#666; display:flex; align-items:center; gap:4px;">
                                    <i class="bi bi-thermometer-half" style="color:var(--rd-red);"></i>AC
                                </span>
                            </div>
                        </div>
                        <div style="text-align:right; min-width:130px;">
                            <div class="price-tag">&#8369;<?= number_format($room['Room_Price']) ?></div>
                            <div style="font-size:11px; color:#bbb; margin-bottom:10px;">per night</div>
                            <?php if ($datesGiven && $room['ConflictCount'] > 0): ?>
                                <span style="display:inline-flex; align-items:center; gap:5px; background:#FEE2E2; color:#991B1B; font-size:11px; font-weight:700; border-radius:6px; padding:5px 12px;">
                                    <i class="bi bi-calendar-x"></i> Not available
                                </span>
                            <?php else: ?>
                                <a href="/hotels/book.php?room=<?= $room['Room_Id'] ?>&hotel=<?= $hotelId ?><?= $checkin ? '&checkin='.urlencode($checkin) : '' ?><?= $checkout ? '&checkout='.urlencode($checkout) : '' ?>"
                                   class="btn-rd" style="padding:8px 20px; font-size:13px; border-radius:8px;">
                                    Book Room
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

        </div>

        <!-- ===== RIGHT: Sticky Summary ===== -->
        <div class="col-lg-4" data-aos="fade-left">
            <div style="
                background:#fff; border-radius:14px; padding:24px;
                box-shadow:var(--rd-shadow); position:sticky; top:80px;
                border:1px solid rgba(228,223,223,0.5);
            ">
                <h6 style="font-size:14px; font-weight:700; margin-bottom:16px; color:#333;">Plan Your Stay</h6>

                <form method="GET" action="/hotels/hotel_detail.php">
                    <input type="hidden" name="id" value="<?= $hotelId ?>">
                    <div class="mb-3">
                        <label class="form-label">Check-in</label>
                        <input type="date" name="checkin" class="form-control"
                               min="<?= date('Y-m-d') ?>"
                               max="<?= date('Y-m-d', strtotime('+2 years')) ?>"
                               value="<?= htmlspecialchars($checkin ?: date('Y-m-d')) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Check-out</label>
                        <input type="date" name="checkout" class="form-control"
                               min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                               max="<?= date('Y-m-d', strtotime('+2 years')) ?>"
                               value="<?= htmlspecialchars($checkout) ?>">
                    </div>
                    <button type="submit" class="btn-rd w-100" style="justify-content:center; padding:10px;">
                        <i class="bi bi-arrow-repeat me-1"></i>Update Dates
                    </button>
                </form>

                <hr class="divider">

                <div style="display:flex; flex-direction:column; gap:10px; font-size:13px; color:#555;">
                    <div style="display:flex; align-items:center; gap:9px;">
                        <i class="bi bi-geo-alt-fill" style="color:var(--rd-red); width:16px;"></i>
                        <?= htmlspecialchars($hotel['Hotel_City']) ?>
                    </div>
                    <div style="display:flex; align-items:center; gap:9px;">
                        <i class="bi bi-star-fill" style="color:#C98A00; width:16px;"></i>
                        <?= $hotel['Hotel_Rating'] ?> Guest Rating
                    </div>
                    <div style="display:flex; align-items:center; gap:9px;">
                        <i class="bi bi-door-closed" style="color:var(--rd-red); width:16px;"></i>
                        <?= $rooms->num_rows ?> room type<?= $rooms->num_rows != 1 ? 's' : '' ?> available
                    </div>
                </div>

                <div style="margin-top:20px; padding:14px; background:var(--rd-red-pale); border-radius:10px; font-size:12px; color:var(--rd-red); line-height:1.55;">
                    <i class="bi bi-info-circle me-1"></i>
                    Free cancellation available on most rooms before check-in.
                </div>
            </div>
        </div>

    </div>
</div>


<?php include "../layout/footer.php"; ?>
