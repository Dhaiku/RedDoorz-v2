<?php
$title = "Find Hotels";
require_once "../config/db.php";
if (($_SESSION['role'] ?? '') === 'hotel_owner') {
    header("Location: /owner/dashboard.php"); exit();
}
if (($_SESSION['role'] ?? '') === 'admin') {
    header("Location: /admin/dashboard.php"); exit();
}
include "../layout/layout.php";

$city     = trim($conn->real_escape_string($_GET['city']    ?? ''));
$checkin  = $_GET['checkin']  ?? '';
$checkout = $_GET['checkout'] ?? '';

$where = "WHERE h.Hotel_Status='active'";
if ($city) {
    $where .= " AND (h.Hotel_City LIKE '%$city%' OR h.Hotel_Name LIKE '%$city%')";
}

// Date availability filter: when both dates given, only include hotels
// that have at least one room with no conflicting active booking
$datesGiven = ($checkin && $checkout && $checkin < $checkout);
$ciEsc = $datesGiven ? $conn->real_escape_string($checkin)  : '';
$coEsc = $datesGiven ? $conn->real_escape_string($checkout) : '';

if ($datesGiven) {
    // count available rooms per hotel = rooms with no overlapping non-cancelled booking
    $hotels = $conn->query("
        SELECT h.*,
               MIN(r.Room_Price) AS MinPrice,
               COUNT(DISTINCT r.Room_Id) AS RoomCount,
               SUM(
                   CASE WHEN NOT EXISTS (
                       SELECT 1 FROM Bookings bk
                       WHERE bk.Book_RoomId   = r.Room_Id
                         AND bk.Book_Status  NOT IN ('cancelled')
                         AND bk.Book_CheckIn  < '$coEsc'
                         AND bk.Book_CheckOut > '$ciEsc'
                   ) THEN 1 ELSE 0 END
               ) AS AvailRooms
        FROM Hotels h
        LEFT JOIN Rooms r ON r.Room_HotelId = h.Hotel_Id AND r.Room_Status='available'
        $where
        GROUP BY h.Hotel_Id
        HAVING AvailRooms > 0
        ORDER BY h.Hotel_Rating DESC
    ");
} else {
    $hotels = $conn->query("
        SELECT h.*,
               MIN(r.Room_Price) AS MinPrice,
               COUNT(DISTINCT r.Room_Id) AS RoomCount,
               COUNT(DISTINCT r.Room_Id) AS AvailRooms
        FROM Hotels h
        LEFT JOIN Rooms r ON r.Room_HotelId = h.Hotel_Id AND r.Room_Status='available'
        $where
        GROUP BY h.Hotel_Id
        ORDER BY h.Hotel_Rating DESC
    ");
}
?>

<!-- ===== SEARCH BAR ===== -->
<div style="background:#fff; border-bottom:1px solid var(--rd-border); padding:20px 0; position:sticky; top:64px; z-index:100; box-shadow:0 2px 12px rgba(0,0,0,0.05);">
    <div class="container">
        <form method="GET" class="d-flex flex-wrap gap-2 align-items-end">
            <div style="flex:1; min-width:180px;">
                <label class="form-label mb-1" style="font-size:12px;">Destination</label>
                <input type="text" name="city" class="form-control" placeholder="City or hotel name"
                       value="<?= htmlspecialchars($city) ?>">
            </div>
            <div style="min-width:145px;">
                <label class="form-label mb-1" style="font-size:12px;">Check-in</label>
                <input type="date" name="checkin" class="form-control"
                       min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($checkin) ?>">
            </div>
            <div style="min-width:145px;">
                <label class="form-label mb-1" style="font-size:12px;">Check-out</label>
                <input type="date" name="checkout" class="form-control"
                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= htmlspecialchars($checkout) ?>">
            </div>
            <button type="submit" class="btn-rd" style="padding:10px 22px; white-space:nowrap;">
                <i class="bi bi-search me-1"></i>Search
            </button>
        </form>
    </div>
</div>

<!-- ===== RESULTS ===== -->
<div class="container" style="padding: 40px 12px 80px;">

    <!-- Results header -->
    <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
        <div>
            <?php if ($city): ?>
                <h2 style="font-size:20px; font-weight:700; margin:0 0 4px;">
                    Hotels in &ldquo;<?= htmlspecialchars($city) ?>&rdquo;
                </h2>
            <?php else: ?>
                <h2 style="font-size:20px; font-weight:700; margin:0 0 4px;">All Available Hotels</h2>
            <?php endif; ?>
            <p style="font-size:14px; color:var(--rd-muted); margin:0;">
                <?= $hotels->num_rows ?> propert<?= $hotels->num_rows != 1 ? 'ies' : 'y' ?>
                <?= $datesGiven ? 'available for <strong>' . date('M d', strtotime($checkin)) . ' &ndash; ' . date('M d, Y', strtotime($checkout)) . '</strong>' : 'listed' ?>
            </p>
        </div>
        <!-- City/Country quick filters -->
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ([
                'Philippines' => ['Manila','Cebu City','Baguio','Boracay','Palawan'],
                'Indonesia'   => ['Bali','Jakarta','Yogyakarta','Bandung'],
                'Singapore'   => ['Singapore'],
                'Vietnam'     => ['Ho Chi Minh City','Hanoi','Da Nang','Hoi An'],
            ] as $country => $cities):
                foreach ($cities as $qc): ?>
                <a href="?city=<?= urlencode($qc) ?><?= $checkin ? '&checkin='.urlencode($checkin) : '' ?><?= $checkout ? '&checkout='.urlencode($checkout) : '' ?>"
                   class="city-pill <?= ($city === $qc) ? 'active' : '' ?>"
                   style="font-size:12px; padding:5px 14px;">
                    <?= $qc ?>
                </a>
            <?php endforeach; endforeach; ?>
        </div>
    </div>

    <?php if ($hotels->num_rows === 0): ?>
        <!-- Empty state -->
        <div style="
            text-align: center; padding: 80px 20px;
            background: #fff; border-radius: 16px;
            border: 1px solid var(--rd-border);
        ">
            <div style="
                width: 72px; height: 72px; margin: 0 auto 20px;
                background: var(--rd-red-pale); border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                font-size: 30px; color: var(--rd-red);
            "><i class="bi bi-building-x"></i></div>
            <h5 style="font-size:18px; font-weight:700; margin:0 0 8px; color:#333;">No hotels found</h5>
            <p style="color:var(--rd-muted); font-size:14px; max-width:340px; margin:0 auto 24px;">
                No properties matched your search. Try a different city or browse all hotels.
            </p>
            <a href="/hotels/search.php" class="btn-rd" style="padding:10px 28px;">
                View All Hotels
            </a>
        </div>

    <?php else: ?>
        <div class="row g-4">
        <?php
        $idx = 0;
        while ($h = $hotels->fetch_assoc()):
            $stars   = round($h['Hotel_Rating']);
            $imgSeed = 'reddoorz' . $h['Hotel_Id'];
            $delay   = ($idx % 3) * 80;
            $idx++;
        ?>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
            <div class="card-rd h-100">

                <!-- Hotel image -->
                <div style="height:190px; position:relative; overflow:hidden;">
                    <img src="https://picsum.photos/seed/<?= $imgSeed ?>/640/400"
                         alt="<?= htmlspecialchars($h['Hotel_Name']) ?>"
                         style="width:100%; height:100%; object-fit:cover; transition:transform 0.4s ease;"
                         onmouseover="this.style.transform='scale(1.05)'"
                         onmouseout="this.style.transform='scale(1)'">
                    <div style="
                        position:absolute; top:10px; left:10px;
                        background:rgba(184,0,32,0.88); backdrop-filter:blur(4px);
                        color:#fff; font-size:11px; font-weight:700;
                        padding:3px 9px; border-radius:20px;
                        display:flex; align-items:center; gap:4px;
                    ">
                        <i class="bi bi-star-fill" style="font-size:9px;"></i>
                        <?= number_format($h['Hotel_Rating'], 1) ?>
                    </div>
                    <div style="
                        position:absolute; top:10px; right:10px;
                        background:rgba(0,0,0,0.52); backdrop-filter:blur(4px);
                        color:#fff; font-size:11px;
                        padding:3px 9px; border-radius:20px;
                    ">
                        <?= htmlspecialchars($h['Hotel_City']) ?>
                    </div>
                </div>

                <div style="padding:16px 18px 20px;">
                    <h5 style="font-size:15px; font-weight:700; margin:0 0 5px; line-height:1.3;">
                        <?= htmlspecialchars($h['Hotel_Name']) ?>
                    </h5>
                    <p style="font-size:12px; color:var(--rd-muted); margin:0 0 12px; display:flex; align-items:center; gap:4px;">
                        <i class="bi bi-geo-alt-fill" style="font-size:11px; color:var(--rd-red);"></i>
                        <?= htmlspecialchars($h['Hotel_Address'] ?? $h['Hotel_City']) ?>
                    </p>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= $stars ? '-fill' : '' ?>"></i>
                                <?php endfor; ?>
                            </span>
                            <span style="font-size:11px; color:#bbb; margin-left:3px;">
                        (<?= $datesGiven ? $h['AvailRooms'] . ' avail.' : $h['RoomCount'] . ' rooms' ?>)
                    </span>
                        </div>
                        <?php if ($h['MinPrice']): ?>
                        <div class="text-end">
                            <div class="price-tag" style="font-size:18px;">&#8369;<?= number_format($h['MinPrice']) ?></div>
                            <div style="font-size:11px; color:#bbb;">per night</div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Amenity chips -->
                    <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:14px;">
                        <?php foreach ([['bi-wifi','WiFi'],['bi-snow','AC'],['bi-cup-hot','Breakfast']] as [$ic,$lbl]): ?>
                        <span style="
                            font-size:11px; color:#555;
                            background:#F5F2F2; border-radius:20px;
                            padding:3px 9px;
                            display:inline-flex; align-items:center; gap:4px;
                        ">
                            <i class="bi <?= $ic ?>" style="font-size:11px; color:var(--rd-red);"></i><?= $lbl ?>
                        </span>
                        <?php endforeach; ?>
                    </div>

                    <a href="/hotels/hotel_detail.php?id=<?= $h['Hotel_Id'] ?><?= $checkin ? '&checkin='.urlencode($checkin) : '' ?><?= $checkout ? '&checkout='.urlencode($checkout) : '' ?>"
                       class="btn-rd d-block text-center"
                       style="border-radius:8px; padding:9px; justify-content:center;">
                        View Rooms
                    </a>
                </div>

            </div>
        </div>
        <?php endwhile; ?>
        </div>
    <?php endif; ?>

</div>


<?php include "../layout/footer.php"; ?>
