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
$totalRevenue = array_sum(array_column($confirmedBookings, 'totalPrice'));

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

        <div class="page-header">
            <h1>Dashboard</h1>
            <p>Overview of your hotel booking platform.</p>
        </div>

        <!-- Stats -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-2" style="flex:0 0 calc(16.66% - 12px); min-width:140px;">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value"><?= $totalHotels ?></div>
                            <div class="stat-label">Active Hotels</div>
                        </div>
                        <div class="stat-icon" style="background:var(--rd-red-pale); color:var(--rd-red);">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2" style="flex:0 0 calc(16.66% - 12px); min-width:140px;">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value"><?= $totalRooms ?></div>
                            <div class="stat-label">Available Rooms</div>
                        </div>
                        <div class="stat-icon" style="background:#EFF6FF; color:#2563EB;">
                            <i class="bi bi-door-closed"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2" style="flex:0 0 calc(16.66% - 12px); min-width:140px;">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value"><?= $totalCustomers ?></div>
                            <div class="stat-label">Customers</div>
                        </div>
                        <div class="stat-icon" style="background:#F5F3FF; color:#7C3AED;">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2" style="flex:0 0 calc(16.66% - 12px); min-width:140px;">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value"><?= $totalBookings ?></div>
                            <div class="stat-label">Total Bookings</div>
                        </div>
                        <div class="stat-icon" style="background:#F0FFF4; color:#16A34A;">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2" style="flex:0 0 calc(16.66% - 12px); min-width:140px;">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value"><?= $pendingPayments ?></div>
                            <div class="stat-label">Pending Payments</div>
                        </div>
                        <div class="stat-icon" style="background:#FFF8E1; color:#92400E;">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2" style="flex:0 0 calc(16.66% - 12px); min-width:140px;">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value" style="font-size:18px;">₱<?= number_format($totalRevenue) ?></div>
                            <div class="stat-label">Revenue</div>
                        </div>
                        <div class="stat-icon" style="background:#FFFBEB; color:#D97706;">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2" style="flex:0 0 calc(16.66% - 12px); min-width:140px;">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value"><?= $pendingPayouts ?></div>
                            <div class="stat-label">Pending Payouts</div>
                        </div>
                        <div class="stat-icon" style="background:#FEF9C3; color:#A16207;">
                            <i class="bi bi-send"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-2" style="flex:0 0 calc(16.66% - 12px); min-width:140px;">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value" style="font-size:18px;">₱<?= number_format($totalCommission) ?></div>
                            <div class="stat-label">Platform Commission</div>
                        </div>
                        <div class="stat-icon" style="background:var(--rd-red-pale); color:var(--rd-red);">
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
