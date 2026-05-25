<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'hotel_owner') {
    header("Location: /auth/login.php"); exit();
}

$acctId  = (int) $_SESSION['account_id'];
$hotelId = (int) ($_SESSION['hotel_id'] ?? 0);

if (!$hotelId) { header("Location: /auth/logout.php"); exit(); }

// Stats
$hotel       = fs_get('hotels', $hotelId);
$totalRooms  = fs_count('rooms', [['hotelId', '=', $hotelId]]);
$availRooms  = fs_count('rooms', [['hotelId', '=', $hotelId], ['status', '=', 'available']]);
$totalBook   = fs_count('bookings', [['hotelId', '=', $hotelId]]);
$pendingBook = fs_count('bookings', [['hotelId', '=', $hotelId], ['status', '=', 'pending']]);

// Earnings
$totalEarnings   = fs_sum('earnings', 'ownerShare', [['hotelId', '=', $hotelId], ['status', '!=', 'voided']]);
$pendingEarnings = fs_sum('earnings', 'ownerShare', [['hotelId', '=', $hotelId], ['status', '=', 'pending']]);

// Recent bookings (last 8, ordered by createdAt desc)
$recentBookings = fs_query('bookings', [['hotelId', '=', $hotelId]], [['createdAt', 'DESC']], 8);

// Enrich with room type and customer name
foreach ($recentBookings as &$b) {
    $room = fs_get('rooms', (int)$b['roomId']);
    $b['roomType'] = $room['type'] ?? '';

    $cust = fs_get('customers', (int)$b['custId']);
    $b['custFirstName'] = $cust['firstName'] ?? '';
    $b['custLastName']  = $cust['lastName']  ?? '';
}
unset($b);

$title = "Owner Dashboard";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:auto;">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <div class="page-header">
            <h1><?= htmlspecialchars($hotel['name'] ?? 'My Hotel') ?></h1>
            <p><?= htmlspecialchars($hotel['city'] ?? '') ?> &mdash; Owner Dashboard</p>
        </div>

        <!-- Stats -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value"><?= $totalRooms ?></div>
                            <div class="stat-label">Total Rooms</div>
                        </div>
                        <div class="stat-icon" style="background:var(--rd-red-pale); color:var(--rd-red);">
                            <i class="bi bi-door-closed"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value"><?= $availRooms ?></div>
                            <div class="stat-label">Available Rooms</div>
                        </div>
                        <div class="stat-icon" style="background:#ECFDF5; color:#047857;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value"><?= $totalBook ?></div>
                            <div class="stat-label">Total Bookings</div>
                        </div>
                        <div class="stat-icon" style="background:#EFF6FF; color:#2563EB;">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="stat-value" style="font-size:20px;">&#8369;<?= number_format($totalEarnings) ?></div>
                            <div class="stat-label">Total Earnings (85%)</div>
                        </div>
                        <div class="stat-icon" style="background:#FFFBEB; color:#D97706;">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($pendingBook > 0): ?>
        <div class="alert-rd-danger mb-4" style="display:flex; align-items:center; gap:9px;">
            <i class="bi bi-clock-history"></i>
            <span>You have <strong><?= $pendingBook ?></strong> pending booking<?= $pendingBook > 1 ? 's' : '' ?> awaiting payment.
            <a href="/owner/manage_bookings.php" style="color:inherit; font-weight:700; text-decoration:underline;">View bookings</a></span>
        </div>
        <?php endif; ?>

        <!-- Recent Bookings -->
        <div class="table-rd">
            <div style="padding:18px 22px; border-bottom:1px solid #F0F0F0; display:flex; justify-content:space-between; align-items:center;">
                <h5 style="font-size:15px; font-weight:700; margin:0;">Recent Bookings</h5>
                <a href="/owner/manage_bookings.php" style="font-size:13px; color:var(--rd-red); font-weight:600; text-decoration:none;">View all</a>
            </div>

            <?php if (empty($recentBookings)): ?>
            <div style="padding:40px; text-align:center; color:#999; font-size:14px;">No bookings yet.</div>
            <?php else: ?>
            <div style="overflow-x:auto;">
                <table class="table mb-0" style="font-size:14px;">
                    <thead class="table-rd">
                        <tr>
                            <th>#</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Check-in</th>
                            <th>Total</th>
                            <th>Status</th>
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
                    <tr>
                        <td style="color:#999;">#<?= str_pad($b['id'],4,'0',STR_PAD_LEFT) ?></td>
                        <td style="font-weight:600;"><?= htmlspecialchars($b['custFirstName'].' '.$b['custLastName']) ?></td>
                        <td style="color:#555;"><?= htmlspecialchars($b['roomType']) ?></td>
                        <td style="color:#555;"><?= date('M d, Y', strtotime($b['checkIn'])) ?></td>
                        <td style="font-weight:700; color:var(--rd-red);">&#8369;<?= number_format($b['totalPrice']) ?></td>
                        <td><?= $badge ?></td>
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
