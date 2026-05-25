<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth/login.php"); exit();
}

$totalHotels    = fs_count('hotels',   [['status', '=', 'active']]);
$totalRooms     = fs_count('rooms',    [['status', '=', 'available']]);
$totalBookings  = fs_count('bookings', []);
$totalCustomers = fs_count('accounts', [['role', '=', 'customer'], ['status', '=', 'active']]);
$pendingPayments= fs_count('bookings', [['status', '=', 'pending']]);

// Revenue: sum totalPrice for confirmed/completed bookings
$confirmedBookings = fs_query('bookings', [['status', 'in', ['confirmed','completed']]]);
$confirmedCount    = count($confirmedBookings);
$totalRevenue      = array_sum(array_column($confirmedBookings, 'totalPrice'));

$cancelledCount  = fs_count('bookings', [['status', '=', 'cancelled']]);
$cancelRate      = $totalBookings > 0 ? round(($cancelledCount / $totalBookings) * 100, 1) : 0;

$allHotelsRated  = fs_query('hotels', [['status', '=', 'active']]);
$ratings         = array_filter(array_column($allHotelsRated, 'rating'));
$avgRating       = count($ratings) ? round(array_sum($ratings) / count($ratings), 1) : 0;

$pendingPayouts  = fs_count('payoutrequests', [['status', '=', 'pending']]);
$totalCommission = fs_sum('earnings', 'platformFee', [['status', '!=', 'voided']]);

// Recent bookings
$recentBookings = fs_query('bookings', [], [['createdAt', 'DESC']], 8);

foreach ($recentBookings as &$b) {
    $hotel = fs_get('hotels', (int)$b['hotelId']);
    $b['hotelName'] = $hotel['name'] ?? '';
    $b['hotelCity'] = $hotel['city'] ?? '';

    $room = fs_get('rooms', (int)$b['roomId']);
    $b['roomType'] = $room['type'] ?? '';

    $cust = fs_get('customers', (int)$b['custId']);
    $b['custFirstName'] = $cust['firstName'] ?? '';
    $b['custLastName']  = $cust['lastName']  ?? '';
}
unset($b);

$title = "Admin Dashboard";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:auto;">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <!-- Top bar -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
            <h1 style="font-size:22px; font-weight:700; margin:0; letter-spacing:-0.2px;">
                Dashboard <span style="color:var(--rd-red);">Overview</span>
            </h1>
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="display:flex; align-items:center; gap:6px; font-size:13px; color:#888; font-weight:500;">
                    <i class="bi bi-calendar3"></i> <?= date('D, M j, Y') ?>
                </div>
                <div style="position:relative; width:34px; height:34px; background:#F4F6FA; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:17px; color:#555; cursor:pointer;">
                    <i class="bi bi-bell"></i>
                    <?php if ($pendingPayments > 0): ?>
                        <span style="position:absolute; top:6px; right:7px; width:7px; height:7px; background:var(--rd-red); border-radius:50%; border:1.5px solid #fff;"></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Pending payments alert -->
        <?php if ($pendingPayments > 0): ?>
        <div style="display:flex; align-items:center; gap:10px; background:#FFF5F5; border:1px solid #FECACA; border-radius:10px; padding:13px 18px; font-size:13.5px; color:#7F1D1D; margin-bottom:24px;">
            <i class="bi bi-exclamation-circle-fill" style="font-size:16px; color:#DC2626; flex-shrink:0;"></i>
            <span>
                <strong><?= $pendingPayments ?> pending payment<?= $pendingPayments > 1 ? 's' : '' ?></strong>
                require your attention.
                <a href="/admin/manage_bookings.php?status=pending" style="color:var(--rd-red); font-weight:700; text-decoration:none; margin-left:4px;">Review now &rarr;</a>
            </span>
        </div>
        <?php endif; ?>

        <!-- Stats — Row 1: 4 cards -->
        <div class="row g-3 mb-3">
            <!-- Total Bookings -->
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card" style="border-left:4px solid #16A34A;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-label" style="text-transform:uppercase; font-size:11px; font-weight:700; letter-spacing:0.6px; margin-bottom:6px;">Total Bookings</div>
                            <div class="stat-value"><?= $totalBookings ?></div>
                            <div style="font-size:12px; color:#16A34A; font-weight:600; margin-top:4px;">
                                <i class="bi bi-arrow-up"></i> <?= $confirmedCount ?> confirmed
                            </div>
                        </div>
                        <div class="stat-icon" style="background:#F0FDF4; color:#16A34A;">
                            <i class="bi bi-calendar2-check"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Revenue -->
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card" style="border-left:4px solid #2563EB;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-label" style="text-transform:uppercase; font-size:11px; font-weight:700; letter-spacing:0.6px; margin-bottom:6px;">Total Revenue</div>
                            <div class="stat-value" style="font-size:20px;">&#8369;<?= number_format($totalRevenue) ?></div>
                            <div style="font-size:12px; color:#9CA3AF; margin-top:4px;">From paid transactions</div>
                        </div>
                        <div class="stat-icon" style="background:#EFF6FF; color:#2563EB;">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Partner Hotels -->
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card" style="border-left:4px solid #7C3AED;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-label" style="text-transform:uppercase; font-size:11px; font-weight:700; letter-spacing:0.6px; margin-bottom:6px;">Partner Hotels</div>
                            <div class="stat-value"><?= $totalHotels ?></div>
                            <div style="font-size:12px; color:#9CA3AF; margin-top:4px;">Active properties</div>
                        </div>
                        <div class="stat-icon" style="background:#F5F3FF; color:#7C3AED;">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Registered Guests -->
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card" style="border-left:4px solid #E11D48;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-label" style="text-transform:uppercase; font-size:11px; font-weight:700; letter-spacing:0.6px; margin-bottom:6px;">Registered Guests</div>
                            <div class="stat-value"><?= $totalCustomers ?></div>
                            <div style="font-size:12px; color:#9CA3AF; margin-top:4px;">Platform accounts</div>
                        </div>
                        <div class="stat-icon" style="background:#FFF1F2; color:#E11D48;">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats — Row 2: 4 cards -->
        <div class="row g-3 mb-4">
            <!-- Pending Payments -->
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card" style="border-left:4px solid #D97706;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-label" style="text-transform:uppercase; font-size:11px; font-weight:700; letter-spacing:0.6px; margin-bottom:6px;">Pending Payments</div>
                            <div class="stat-value"><?= $pendingPayments ?></div>
                            <div style="font-size:12px; color:<?= $pendingPayments > 0 ? '#DC2626' : '#16A34A' ?>; font-weight:600; margin-top:4px;">
                                <?= $pendingPayments > 0 ? 'Action required' : 'All clear' ?>
                            </div>
                        </div>
                        <div class="stat-icon" style="background:#FFFBEB; color:#D97706;">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Cancellation Rate -->
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card" style="border-left:4px solid #DC2626;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-label" style="text-transform:uppercase; font-size:11px; font-weight:700; letter-spacing:0.6px; margin-bottom:6px;">Cancellation Rate</div>
                            <div class="stat-value"><?= $cancelRate ?>%</div>
                            <div style="font-size:12px; color:#9CA3AF; margin-top:4px;"><?= $cancelledCount ?> cancelled bookings</div>
                        </div>
                        <div class="stat-icon" style="background:#FEF2F2; color:#DC2626;">
                            <i class="bi bi-x-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Avg. Rating -->
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card" style="border-left:4px solid #EA580C;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-label" style="text-transform:uppercase; font-size:11px; font-weight:700; letter-spacing:0.6px; margin-bottom:6px;">Avg. Rating</div>
                            <div class="stat-value"><?= $avgRating ?></div>
                            <div style="font-size:12px; color:#9CA3AF; margin-top:4px;">Platform-wide guest score</div>
                        </div>
                        <div class="stat-icon" style="background:#FFF7ED; color:#EA580C;">
                            <i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Confirmed Bookings -->
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card" style="border-left:4px solid #0D9488;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-label" style="text-transform:uppercase; font-size:11px; font-weight:700; letter-spacing:0.6px; margin-bottom:6px;">Confirmed Bookings</div>
                            <div class="stat-value"><?= $confirmedCount ?></div>
                            <div style="font-size:12px; color:#9CA3AF; margin-top:4px;">
                                <?= $totalBookings > 0 ? round(($confirmedCount / $totalBookings) * 100) : 0 ?>% of total
                            </div>
                        </div>
                        <div class="stat-icon" style="background:#F0FDFA; color:#0D9488;">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div style="background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,0.07); overflow:hidden;">
            <div style="padding:18px 22px; border-bottom:1px solid #F0F0F0; display:flex; justify-content:space-between; align-items:center;">
                <h5 style="font-size:15px; font-weight:700; margin:0;">Recent Bookings</h5>
                <a href="/admin/manage_bookings.php" style="font-size:13px; color:var(--rd-red); font-weight:600; text-decoration:none;">View all</a>
            </div>

            <?php if (empty($recentBookings)): ?>
                <div style="padding:40px; text-align:center; color:#999; font-size:14px;">No bookings yet.</div>
            <?php else: ?>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:14px;">
                    <thead>
                        <tr style="background:#FAFAFA;">
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">#</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Guest</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Hotel</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Check-in</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Total</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentBookings as $b):
                        $badge = match($b['status']) {
                            'confirmed' => '<span class="badge-confirmed">Confirmed</span>',
                            'cancelled' => '<span class="badge-cancelled">Cancelled</span>',
                            'completed' => '<span class="badge-completed">Completed</span>',
                            default     => '<span class="badge-pending">Pending</span>',
                        };
                    ?>
                    <tr style="border-bottom:1px solid #F8F8F8;">
                        <td style="padding:14px 16px; color:#999;">#<?= str_pad($b['id'],4,'0',STR_PAD_LEFT) ?></td>
                        <td style="padding:14px 16px; font-weight:600;"><?= htmlspecialchars($b['custFirstName'].' '.$b['custLastName']) ?></td>
                        <td style="padding:14px 16px; color:#555;"><?= htmlspecialchars($b['hotelName']) ?></td>
                        <td style="padding:14px 16px; color:#555;"><?= date('M d, Y', strtotime($b['checkIn'])) ?></td>
                        <td style="padding:14px 16px; font-weight:700; color:var(--rd-red);">&#8369;<?= number_format($b['totalPrice']) ?></td>
                        <td style="padding:14px 16px;"><?= $badge ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
