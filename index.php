<?php
$title = "Affordable Hotels Across the Philippines";
require_once "config/db.php";
include "layout/layout.php";

// Featured hotels
$featured = $conn->query("SELECT * FROM Hotels WHERE Hotel_Status='active' ORDER BY Hotel_Rating DESC LIMIT 6");

// Min price per hotel
$prices = [];
$priceResult = $conn->query("SELECT Room_HotelId, MIN(Room_Price) as MinPrice FROM Rooms WHERE Room_Status='available' GROUP BY Room_HotelId");
while ($row = $priceResult->fetch_assoc()) {
    $prices[$row['Room_HotelId']] = $row['MinPrice'];
}

$isLoggedIn = isset($_SESSION['account_id']);
?>

<!-- ===== HERO ===== -->
<section style="
    min-height: 100dvh;
    position: relative;
    background-image: url('https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?q=80&w=1470&auto=format&fit=crop');
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    margin-top: -64px;
    padding-top: 64px;
">
    <!-- Gradient overlay — darker on left, fades right -->
    <div style="
        position: absolute; inset: 0;
        background: linear-gradient(105deg,
            rgba(10,0,2,0.80) 0%,
            rgba(10,0,2,0.60) 45%,
            rgba(10,0,2,0.22) 100%);
    "></div>

    <div class="container" style="position:relative; z-index:1; padding: 100px 16px 80px;">
        <div style="max-width:580px;">

            <!-- Eyebrow label -->
            <div class="animate-fade-up" style="
                display: inline-flex; align-items: center; gap: 7px;
                background: rgba(184,0,32,0.8); border: 1px solid rgba(184,0,32,0.6);
                border-radius: 20px; padding: 5px 14px;
                font-size: 12px; font-weight: 600; letter-spacing: 0.8px;
                text-transform: uppercase; color: #fff; margin-bottom: 20px;
            ">
                <i class="bi bi-geo-alt-fill"></i> Philippines &mdash; 200+ Properties
            </div>

            <h1 class="animate-fade-up delay-100" style="
                font-size: clamp(34px, 5vw, 58px);
                font-weight: 700;
                line-height: 1.08;
                letter-spacing: -0.8px;
                color: #fff;
                margin-bottom: 18px;
            ">
                Your Comfortable Stay<br>Starts Here
            </h1>

            <p class="animate-fade-up delay-200" style="
                font-size: 17px;
                color: rgba(255,255,255,0.82);
                line-height: 1.65;
                margin-bottom: 36px;
                max-width: 460px;
            ">
                Clean rooms, honest pricing, free WiFi &mdash; across the Philippines, Indonesia, Singapore, and Vietnam.
            </p>

            <?php if ($isLoggedIn): ?>
                <!-- Search form for logged-in users -->
                <div class="animate-fade-up delay-300" style="
                    background: #fff;
                    border-radius: 16px;
                    padding: 22px 26px;
                    box-shadow: 0 16px 48px rgba(0,0,0,0.28);
                    max-width: 780px;
                ">
                    <form method="GET" action="/hotels/search.php" id="heroSearchForm">
                        <div class="row g-3 align-items-end">
                            <!-- Destination — full width on its own row -->
                            <div class="col-12">
                                <label class="form-label" style="font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">
                                    <i class="bi bi-geo-alt-fill me-1" style="color:var(--rd-red)"></i>Destination
                                </label>
                                <input type="text" name="city" class="form-control" id="heroCity"
                                       placeholder="City or hotel name"
                                       value="<?= htmlspecialchars($_GET['city'] ?? '') ?>">
                            </div>
                            <!-- Check-in -->
                            <div class="col-md-5">
                                <label class="form-label" style="font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">
                                    <i class="bi bi-calendar-event me-1" style="color:var(--rd-red)"></i>Check-in
                                </label>
                                <input type="date" name="checkin" class="form-control" id="heroCheckin"
                                       min="<?= date('Y-m-d') ?>"
                                       style="min-width:0;"
                                       value="<?= htmlspecialchars($_GET['checkin'] ?? '') ?>">
                            </div>
                            <!-- Check-out -->
                            <div class="col-md-5">
                                <label class="form-label" style="font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">
                                    <i class="bi bi-calendar-check me-1" style="color:var(--rd-red)"></i>Check-out
                                </label>
                                <input type="date" name="checkout" class="form-control" id="heroCheckout"
                                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                       style="min-width:0;"
                                       value="<?= htmlspecialchars($_GET['checkout'] ?? '') ?>">
                            </div>
                            <!-- Search button -->
                            <div class="col-md-2">
                                <button type="submit" class="btn-rd w-100"
                                        style="padding:11px 8px; font-size:14px; border-radius:8px; justify-content:center;">
                                    <i class="bi bi-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <script>
                // Keep check-out min date always 1 day after check-in
                (function () {
                    var ci = document.getElementById('heroCheckin');
                    var co = document.getElementById('heroCheckout');
                    if (!ci || !co) return;
                    function updateMin() {
                        if (!ci.value) return;
                        var next = new Date(ci.value);
                        next.setDate(next.getDate() + 1);
                        var yyyy = next.getFullYear();
                        var mm   = String(next.getMonth() + 1).padStart(2, '0');
                        var dd   = String(next.getDate()).padStart(2, '0');
                        var minDate = yyyy + '-' + mm + '-' + dd;
                        co.min = minDate;
                        // if checkout is before or equal to checkin, clear it
                        if (co.value && co.value <= ci.value) {
                            co.value = minDate;
                        }
                    }
                    ci.addEventListener('change', updateMin);
                    updateMin(); // run on page load to restore correct min
                })();
                </script>
            <?php else: ?>
                <!-- CTA for guests -->
                <div class="animate-fade-up delay-300" style="display:flex; gap:12px; flex-wrap:wrap;">
                    <a href="/auth/login.php" class="btn-rd" style="padding:13px 32px; font-size:15px;">
                        <i class="bi bi-box-arrow-in-right"></i> Login to Book
                    </a>
                    <a href="/auth/register.php" style="
                        display: inline-flex; align-items: center; gap: 6px;
                        padding: 12px 30px;
                        border: 1.5px solid rgba(255,255,255,0.55);
                        border-radius: 8px;
                        color: #fff;
                        font-size: 15px;
                        font-weight: 600;
                        transition: all 0.18s;
                        font-family: 'DM Sans', sans-serif;
                    "
                    onmouseover="this.style.background='rgba(255,255,255,0.12)'"
                    onmouseout="this.style.background='transparent'">
                        <i class="bi bi-person-plus"></i> Create Account
                    </a>
                </div>
                <p class="animate-fade-up delay-400" style="
                    margin-top: 16px; font-size: 13px;
                    color: rgba(255,255,255,0.52); margin-bottom: 0;
                ">
                    Free to join. No credit card required.
                </p>
            <?php endif; ?>

        </div>
    </div>

    <!-- Scroll indicator -->
    <div style="
        position: absolute; bottom: 32px; left: 50%; transform: translateX(-50%);
        display: flex; flex-direction: column; align-items: center; gap: 6px;
        animation: bounceDown 2s infinite;
    ">
        <span style="font-size:11px; color:rgba(255,255,255,0.45); letter-spacing:1px; text-transform:uppercase;">Scroll</span>
        <i class="bi bi-chevron-down" style="color:rgba(255,255,255,0.4); font-size:14px;"></i>
    </div>
</section>

<style>
@keyframes bounceDown {
    0%, 100% { transform: translateX(-50%) translateY(0); }
    50%       { transform: translateX(-50%) translateY(5px); }
}
.animate-fade-up {
    animation: fadeUp 0.65s cubic-bezier(0.16,1,0.3,1) both;
}
@keyframes fadeUp {
    from { opacity:0; transform:translateY(22px); }
    to   { opacity:1; transform:translateY(0); }
}
.delay-100 { animation-delay: 0.10s; }
.delay-200 { animation-delay: 0.20s; }
.delay-300 { animation-delay: 0.32s; }
.delay-400 { animation-delay: 0.44s; }
</style>

<!-- ===== CITIES STRIP ===== -->
<section style="background:#fff; padding:18px 0; border-bottom:1px solid #EAE6E6;">
    <div class="container">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span style="font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.8px; margin-right:6px;">Popular:</span>
            <?php
            $cities = ['Bali','Manila','Singapore','Da Nang','Boracay','Hanoi','Cebu City','Jakarta'];
            foreach ($cities as $c): ?>
                <a href="/hotels/search.php?city=<?= urlencode($c) ?>" class="city-pill">
                    <i class="bi bi-geo-alt" style="font-size:11px;"></i><?= $c ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== FEATURED HOTELS ===== -->
<section id="featured" style="padding: 80px 0 60px;">
    <div class="container">

        <div class="d-flex justify-content-between align-items-end mb-5 flex-wrap gap-3">
            <div data-aos="fade-right">
                <div class="section-label">Top-Rated Stays</div>
                <h2 class="section-title">Featured Hotels</h2>
                <p class="section-subtitle">Hand-picked properties with the highest guest ratings.</p>
            </div>
            <a href="/hotels/search.php" class="btn-rd-outline" data-aos="fade-left" style="font-size:13px; padding:8px 20px;">
                Browse All Hotels <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php
            $hotelIdx = 0;
            while ($hotel = $featured->fetch_assoc()):
                $minPrice = $prices[$hotel['Hotel_Id']] ?? null;
                $rating   = $hotel['Hotel_Rating'];
                $stars    = round($rating);
                $imgSeed  = 'reddoorz' . $hotel['Hotel_Id'];
                $hotelIdx++;
            ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($hotelIdx - 1) * 80 ?>">
                <div class="card-rd h-100">

                    <!-- Hotel image -->
                    <div style="height:196px; position:relative; overflow:hidden;">
                        <img src="https://picsum.photos/seed/<?= $imgSeed ?>/640/400"
                             alt="<?= htmlspecialchars($hotel['Hotel_Name']) ?>"
                             style="width:100%; height:100%; object-fit:cover; transition:transform 0.4s ease;"
                             onmouseover="this.style.transform='scale(1.04)'"
                             onmouseout="this.style.transform='scale(1)'">
                        <!-- Rating badge -->
                        <div style="
                            position:absolute; top:12px; left:12px;
                            background:rgba(184,0,32,0.88);
                            backdrop-filter:blur(4px);
                            color:#fff; font-size:11px; font-weight:700;
                            padding:4px 10px; border-radius:20px;
                            display:flex; align-items:center; gap:4px;
                        ">
                            <i class="bi bi-star-fill" style="font-size:9px;"></i>
                            <?= number_format($rating, 1) ?>
                        </div>
                        <!-- City badge -->
                        <div style="
                            position:absolute; top:12px; right:12px;
                            background:rgba(0,0,0,0.52);
                            backdrop-filter:blur(4px);
                            color:#fff; font-size:11px;
                            padding:4px 10px; border-radius:20px;
                        ">
                            <?= htmlspecialchars($hotel['Hotel_City']) ?>
                        </div>
                    </div>

                    <div style="padding:18px 20px 20px;">
                        <h5 style="font-size:15px; font-weight:700; margin:0 0 5px; line-height:1.3;">
                            <?= htmlspecialchars($hotel['Hotel_Name']) ?>
                        </h5>
                        <p style="font-size:13px; color:var(--rd-muted); margin:0 0 14px; display:flex; align-items:center; gap:4px;">
                            <i class="bi bi-geo-alt-fill" style="font-size:11px; color:var(--rd-red);"></i>
                            <?= htmlspecialchars($hotel['Hotel_Address'] ?? $hotel['Hotel_City']) ?>
                        </p>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi bi-star<?= $i <= $stars ? '-fill' : '' ?>"></i>
                                    <?php endfor; ?>
                                </span>
                                <span style="font-size:12px; color:#aaa; margin-left:4px;">(<?= $rating ?>)</span>
                            </div>
                            <?php if ($minPrice): ?>
                                <div class="text-end">
                                    <div class="price-tag">&#8369;<?= number_format($minPrice) ?></div>
                                    <div style="font-size:11px; color:#aaa;">per night</div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <a href="/hotels/hotel_detail.php?id=<?= $hotel['Hotel_Id'] ?>"
                           class="btn-rd mt-3 d-block text-center"
                           style="border-radius:8px; padding:10px; justify-content:center;">
                            View &amp; Book
                        </a>
                    </div>

                </div>
            </div>
            <?php endwhile; ?>
        </div>

    </div>
</section>

<!-- ===== WHY REDDOORZ (Asymmetric 2-col) ===== -->
<section style="background:#fff; padding:80px 0;">
    <div class="container">
        <div class="row g-5 align-items-center">

            <!-- Left: text block -->
            <div class="col-lg-5" data-aos="fade-right">
                <div class="section-label">Why RedDoorz</div>
                <h2 class="section-title">A smarter way<br>to book a hotel</h2>
                <p class="section-subtitle" style="margin-bottom:32px;">
                    We standardize every property so you always know what you're getting &mdash; no unpleasant surprises, no inflated prices.
                </p>
                <a href="/auth/register.php" class="btn-rd" style="padding:12px 28px;">
                    <i class="bi bi-person-plus"></i> Join for Free
                </a>
            </div>

            <!-- Right: feature list -->
            <div class="col-lg-7">
                <div class="row g-4">

                    <?php
                    $features = [
                        ['bi-cash-coin',     'Transparent Pricing',   'The price you see is what you pay. Taxes and fees are always included upfront.', '0'],
                        ['bi-shield-check',  'Verified Properties',   'Every hotel on our platform is inspected and scored for cleanliness and comfort.', '80'],
                        ['bi-wifi',          'Free WiFi Everywhere',  'High-speed internet is included at every RedDoorz property, at no extra charge.', '160'],
                        ['bi-headset',       '24/7 Guest Support',    'Our local support team is available around the clock to resolve any issue fast.', '240'],
                    ];
                    foreach ($features as [$icon, $ttl, $desc, $delay]):
                    ?>
                    <div class="col-sm-6" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                        <div style="
                            display: flex; gap: 14px; align-items: flex-start;
                            padding: 20px; border-radius: 12px;
                            background: var(--rd-bg);
                            border: 1px solid var(--rd-border);
                            height: 100%;
                        ">
                            <div style="
                                width: 42px; height: 42px; flex-shrink: 0;
                                background: var(--rd-red-pale);
                                border-radius: 10px;
                                display: flex; align-items: center; justify-content: center;
                                font-size: 20px; color: var(--rd-red);
                            ">
                                <i class="bi <?= $icon ?>"></i>
                            </div>
                            <div>
                                <h6 style="font-size:14px; font-weight:700; margin:0 0 5px;"><?= $ttl ?></h6>
                                <p style="font-size:13px; color:var(--rd-muted); margin:0; line-height:1.55;"><?= $desc ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- ===== TESTIMONIALS ===== -->
<section style="background: var(--rd-bg); padding: 80px 0;">
    <div class="container">

        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-label">Guest Reviews</div>
            <h2 class="section-title">What our guests say</h2>
            <p class="section-subtitle">Real feedback from travelers who booked through RedDoorz.</p>
        </div>

        <div class="row g-4">
            <?php
            $testimonials = [
                [
                    'name'   => 'Maria Santos',
                    'role'   => 'Marketing Professional &mdash; Makati',
                    'seed'   => 'ms-rdz-2024',
                    'rating' => 5,
                    'quote'  => 'Stayed here for a work trip and was genuinely impressed with how clean everything was. The booking was seamless and the final price matched exactly what I saw online.',
                    'delay'  => 0,
                ],
                [
                    'name'   => 'Rico Valdez',
                    'role'   => 'Entrepreneur &mdash; Cebu City',
                    'seed'   => 'rv-rdz-2024',
                    'rating' => 5,
                    'quote'  => 'The Cebu property exceeded my expectations. Great value, WiFi that actually worked, and the staff were genuinely accommodating. Already booked again for next month.',
                    'delay'  => 100,
                ],
                [
                    'name'   => 'Theresa Ocampo',
                    'role'   => 'Teacher &mdash; Quezon City',
                    'seed'   => 'to-rdz-2024',
                    'rating' => 5,
                    'quote'  => 'First time using RedDoorz and I will not go back to other platforms. Simple interface, honest pricing, and zero surprise fees when I checked in.',
                    'delay'  => 200,
                ],
            ];
            foreach ($testimonials as $t):
            ?>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?= $t['delay'] ?>">
                <div style="
                    background: #fff;
                    border: 1px solid var(--rd-border);
                    border-radius: 14px;
                    padding: 28px;
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                    gap: 18px;
                    box-shadow: var(--rd-shadow);
                ">
                    <!-- Stars -->
                    <div>
                        <?php for ($i = 0; $i < $t['rating']; $i++): ?>
                            <i class="bi bi-star-fill" style="color:#C98A00; font-size:13px;"></i>
                        <?php endfor; ?>
                    </div>

                    <!-- Quote -->
                    <p style="font-size:14px; color:#333; line-height:1.68; margin:0; flex:1;">
                        &ldquo;<?= $t['quote'] ?>&rdquo;
                    </p>

                    <!-- Author -->
                    <div style="display:flex; align-items:center; gap:12px; border-top:1px solid var(--rd-border); padding-top:16px;">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($t['name']) ?>&background=B80020&color=fff&size=48&rounded=true&bold=true"
                             alt="<?= htmlspecialchars($t['name']) ?>"
                             style="width:44px; height:44px; border-radius:50%; flex-shrink:0;">
                        <div>
                            <div style="font-size:14px; font-weight:700; color:#1A1A1A;"><?= $t['name'] ?></div>
                            <div style="font-size:12px; color:var(--rd-muted);"><?= $t['role'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- ===== CTA BANNER ===== -->
<section style="background: linear-gradient(135deg, #8A0014 0%, #B80020 55%, #D4001F 100%); padding: 80px 0;">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7" data-aos="fade-right">
                <div style="font-size:12px; font-weight:600; letter-spacing:1.2px; text-transform:uppercase; color:rgba(255,255,255,0.6); margin-bottom:10px;">
                    Ready to Book?
                </div>
                <h2 style="font-size:clamp(26px,4vw,40px); font-weight:700; color:#fff; margin:0 0 12px; letter-spacing:-0.3px; line-height:1.15;">
                    Find your next stay in minutes.
                </h2>
                <p style="font-size:15px; color:rgba(255,255,255,0.78); margin:0; max-width:480px; line-height:1.6;">
                    Over 200 properties across the Philippines. No hidden charges, no booking fees &mdash; just the best price, every time.
                </p>
            </div>
            <div class="col-lg-5 d-flex gap-3 flex-wrap" data-aos="fade-left"
                 style="justify-content: flex-start;">
                <?php if (!$isLoggedIn): ?>
                    <a href="/auth/register.php" style="
                        display: inline-flex; align-items: center; gap: 6px;
                        background: #fff; color: var(--rd-red);
                        padding: 14px 30px; border-radius: 8px;
                        font-size: 15px; font-weight: 700;
                        transition: background 0.18s;
                        font-family: 'DM Sans', sans-serif;
                    "
                    onmouseover="this.style.background='#f5eaea'"
                    onmouseout="this.style.background='#fff'">
                        <i class="bi bi-person-plus"></i> Create Free Account
                    </a>
                <?php endif; ?>
                <a href="/hotels/search.php" style="
                    display: inline-flex; align-items: center; gap: 6px;
                    background: transparent; color: #fff;
                    border: 1.5px solid rgba(255,255,255,0.55);
                    padding: 13px 28px; border-radius: 8px;
                    font-size: 15px; font-weight: 600;
                    transition: all 0.18s;
                    font-family: 'DM Sans', sans-serif;
                "
                onmouseover="this.style.background='rgba(255,255,255,0.12)'"
                onmouseout="this.style.background='transparent'">
                    <i class="bi bi-search"></i> Browse Hotels
                </a>
            </div>
        </div>
    </div>
</section>


<?php include "layout/footer.php"; ?>
