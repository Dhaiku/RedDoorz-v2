<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth/login.php"); exit();
}

// ── Stats ─────────────────────────────────────────────────────────────────────
$totalHotels     = fs_count('hotels',   [['status', '=', 'active']]);
$totalRooms      = fs_count('rooms',    [['status', '=', 'available']]);
$totalBookings   = fs_count('bookings', []);
$totalCustomers  = fs_count('accounts', [['role', '=', 'customer']]);
$pendingPayments = fs_count('bookings', [['status', '=', 'pending']]);

$confirmedBkgs   = fs_query('bookings', [['status', 'in', ['confirmed','completed']]]);
$confirmedCount  = count($confirmedBkgs);
$totalRevenue    = array_sum(array_column($confirmedBkgs, 'totalPrice'));

$cancelledCount  = fs_count('bookings', [['status', '=', 'cancelled']]);
$cancelRate      = $totalBookings > 0 ? round(($cancelledCount / $totalBookings) * 100, 1) : 0;

$pendingPayouts  = fs_count('payoutrequests', [['status', '=', 'pending']]);
$totalCommission = fs_sum('earnings', 'platformFee', []);

// Avg rating across all hotels
$allHotels   = fs_query('hotels', [['status', '=', 'active']]);
$ratings     = array_filter(array_column($allHotels, 'rating'));
$avgRating   = count($ratings) ? round(array_sum($ratings) / count($ratings), 1) : 0;

// Recent bookings (last 8)
$recentBookings = fs_query('bookings', [], [['createdAt', 'DESC']], 8);
foreach ($recentBookings as &$b) {
    $hotel = fs_get('hotels',    (int)$b['hotelId']);
    $b['hotelName']    = $hotel['name'] ?? '—';
    $cust = fs_get('customers', (int)$b['custId']);
    $b['custName'] = trim(($cust['firstName'] ?? '') . ' ' . ($cust['lastName'] ?? '')) ?: '—';
}
unset($b);

$title = "Admin Dashboard";
include "../layout/layout.php";
?>

<style>
/* ── Admin shell override ───────────────────────────────────────────────────── */
body { background: #F4F6FA !important; padding-top: 0 !important; }

/* Hide the public navbar for the admin portal */
.navbar-rd { display: none !important; }

/* ── Layout ─────────────────────────────────────────────────────────────────── */
.admin-shell {
    display: flex;
    min-height: 100vh;
}

/* ── Dark Sidebar ────────────────────────────────────────────────────────────── */
.adm-sidebar {
    width: 220px;
    flex-shrink: 0;
    background: #14151A;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    position: sticky;
    top: 0;
    align-self: flex-start;
    height: 100vh;
    overflow-y: auto;
}

.adm-sidebar-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 22px 20px 18px;
    border-bottom: 1px solid rgba(255,255,255,0.07);
}

.adm-sidebar-logo .logo-icon {
    width: 34px; height: 34px;
    background: var(--rd-red);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px; color: #fff; flex-shrink: 0;
}

.adm-sidebar-logo .logo-text {
    line-height: 1.15;
}
.adm-sidebar-logo .logo-text strong {
    display: block; font-size: 15px; font-weight: 700; color: #fff; letter-spacing: -0.2px;
}
.adm-sidebar-logo .logo-text span {
    font-size: 10px; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.9px; color: rgba(255,255,255,0.38);
}

.adm-nav-section {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1px; color: rgba(255,255,255,0.28);
    padding: 22px 20px 7px;
}

.adm-nav-link {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 20px;
    font-size: 13.5px; font-weight: 500;
    color: rgba(255,255,255,0.55);
    text-decoration: none;
    transition: all 0.15s;
    position: relative;
    border-radius: 0;
}

.adm-nav-link i { font-size: 16px; width: 18px; text-align: center; flex-shrink: 0; }

.adm-nav-link:hover {
    color: #fff;
    background: rgba(255,255,255,0.06);
}

.adm-nav-link.active {
    color: #fff;
    background: rgba(184,0,32,0.22);
    font-weight: 600;
}

.adm-nav-link.active::before {
    content: '';
    position: absolute; left: 0; top: 6px; bottom: 6px;
    width: 3px; background: var(--rd-red);
    border-radius: 0 3px 3px 0;
}

.adm-nav-badge {
    margin-left: auto;
    background: var(--rd-red);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 20px;
    min-width: 20px;
    text-align: center;
}

.adm-nav-divider {
    border: none; border-top: 1px solid rgba(255,255,255,0.07);
    margin: 10px 20px;
}

/* ── Main content area ───────────────────────────────────────────────────────── */
.adm-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
}

/* ── Top bar ─────────────────────────────────────────────────────────────────── */
.adm-topbar {
    background: #fff;
    border-bottom: 1px solid #E8EAED;
    padding: 0 28px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    position: sticky;
    top: 0;
    z-index: 100;
}

.adm-topbar-title {
    font-size: 20px;
    font-weight: 700;
    color: #1A1A1A;
    letter-spacing: -0.2px;
}

.adm-topbar-title span { color: var(--rd-red); }

.adm-topbar-right {
    display: flex;
    align-items: center;
    gap: 18px;
}

.adm-topbar-date {
    display: flex; align-items: center; gap: 6px;
    font-size: 13px; color: #888; font-weight: 500;
}

.adm-bell {
    position: relative;
    width: 34px; height: 34px;
    border-radius: 8px;
    background: #F4F6FA;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px; color: #555;
    cursor: pointer;
    transition: background 0.15s;
}
.adm-bell:hover { background: #EBEEF3; }
.adm-bell-dot {
    position: absolute; top: 6px; right: 7px;
    width: 7px; height: 7px;
    background: var(--rd-red); border-radius: 50%;
    border: 1.5px solid #fff;
}

/* ── Content padding ─────────────────────────────────────────────────────────── */
.adm-content {
    padding: 26px 28px 40px;
    flex: 1;
}

/* ── Alert banner ────────────────────────────────────────────────────────────── */
.adm-alert {
    display: flex; align-items: center; gap: 10px;
    background: #FFF5F5;
    border: 1px solid #FECACA;
    border-radius: 10px;
    padding: 13px 18px;
    font-size: 13.5px;
    color: #7F1D1D;
    margin-bottom: 24px;
}
.adm-alert i { font-size: 16px; color: #DC2626; flex-shrink: 0; }
.adm-alert a { color: var(--rd-red); font-weight: 700; text-decoration: none; margin-left: 4px; }
.adm-alert a:hover { text-decoration: underline; }

/* ── Stat cards ──────────────────────────────────────────────────────────────── */
.adm-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

@media (max-width: 1100px) { .adm-stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px)  { .adm-stats-grid { grid-template-columns: 1fr; } }

.adm-stat {
    background: #fff;
    border-radius: 12px;
    padding: 20px 22px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    border: 1px solid #ECEEF2;
    border-left: 4px solid transparent;
    display: flex;
    flex-direction: column;
    gap: 4px;
    position: relative;
    overflow: hidden;
}

.adm-stat-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.7px;
    color: #9CA3AF;
    margin-bottom: 2px;
}

.adm-stat-value {
    font-size: 30px;
    font-weight: 700;
    line-height: 1.1;
    color: #111827;
    letter-spacing: -0.5px;
}

.adm-stat-sub {
    font-size: 12px;
    color: #9CA3AF;
    margin-top: 2px;
}

.adm-stat-sub.green  { color: #16A34A; font-weight: 600; }
.adm-stat-sub.red    { color: #DC2626; font-weight: 600; }
.adm-stat-sub.orange { color: #D97706; font-weight: 600; }

.adm-stat-icon {
    position: absolute;
    top: 18px; right: 18px;
    width: 42px; height: 42px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
}

/* Color accents — matching screenshot */
.adm-stat.green  { border-left-color: #16A34A; }
.adm-stat.green  .adm-stat-icon { background: #F0FDF4; color: #16A34A; }

.adm-stat.blue   { border-left-color: #2563EB; }
.adm-stat.blue   .adm-stat-icon { background: #EFF6FF; color: #2563EB; }

.adm-stat.red    { border-left-color: #DC2626; }
.adm-stat.red    .adm-stat-icon { background: #FEF2F2; color: #DC2626; }

.adm-stat.purple { border-left-color: #7C3AED; }
.adm-stat.purple .adm-stat-icon { background: #F5F3FF; color: #7C3AED; }

.adm-stat.yellow { border-left-color: #D97706; }
.adm-stat.yellow .adm-stat-icon { background: #FFFBEB; color: #D97706; }

.adm-stat.orange { border-left-color: #EA580C; }
.adm-stat.orange .adm-stat-icon { background: #FFF7ED; color: #EA580C; }

.adm-stat.teal   { border-left-color: #0D9488; }
.adm-stat.teal   .adm-stat-icon { background: #F0FDFA; color: #0D9488; }

.adm-stat.rose   { border-left-color: #E11D48; }
.adm-stat.rose   .adm-stat-icon { background: #FFF1F2; color: #E11D48; }

/* ── Table card ──────────────────────────────────────────────────────────────── */
.adm-table-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    border: 1px solid #ECEEF2;
    overflow: hidden;
}

.adm-table-header {
    padding: 16px 22px;
    border-bottom: 1px solid #F0F2F5;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.adm-table-header h5 {
    font-size: 15px; font-weight: 700; margin: 0; color: #111827;
}

.adm-table-header a {
    font-size: 13px; color: var(--rd-red); font-weight: 600;
    text-decoration: none;
}
.adm-table-header a:hover { text-decoration: underline; }
</style>

<div class="admin-shell">

    <!-- ── Dark Sidebar ─────────────────────────────────────────────────── -->
    <aside class="adm-sidebar">

        <div class="adm-sidebar-logo">
            <div class="logo-icon"><i class="bi bi-door-open-fill"></i></div>
            <div class="logo-text">
                <strong>RedDoorz</strong>
                <span>Admin Portal</span>
            </div>
        </div>

        <div class="adm-nav-section">Overview</div>

        <a href="/admin/dashboard.php" class="adm-nav-link active">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div class="adm-nav-section">Management</div>

        <a href="/admin/manage_hotels.php" class="adm-nav-link">
            <i class="bi bi-building"></i> Hotels
        </a>

        <a href="/admin/manage_rooms.php" class="adm-nav-link">
            <i class="bi bi-door-closed"></i> Rooms
        </a>

        <a href="/admin/manage_bookings.php" class="adm-nav-link">
            <i class="bi bi-calendar-check"></i> Bookings
            <?php if ($pendingPayments > 0): ?>
                <span class="adm-nav-badge"><?= $pendingPayments ?></span>
            <?php endif; ?>
        </a>

        <a href="/admin/manage_payouts.php" class="adm-nav-link">
            <i class="bi bi-credit-card"></i> Payments
        </a>

        <div class="adm-nav-section">Content</div>

        <a href="/admin/manage_hotels.php?tab=reviews" class="adm-nav-link">
            <i class="bi bi-star"></i> Reviews
        </a>

        <a href="/admin/manage_customers.php" class="adm-nav-link">
            <i class="bi bi-people"></i> Users
        </a>

        <a href="/admin/manage_owners.php" class="adm-nav-link">
            <i class="bi bi-person-badge"></i> Hotel Owners
        </a>

        <hr class="adm-nav-divider">

        <a href="/hotels/search.php" class="adm-nav-link">
            <i class="bi bi-box-arrow-up-right"></i> View Site
        </a>

        <a href="/auth/logout.php" class="adm-nav-link" style="color:rgba(255,100,100,0.7);">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>

    </aside>

    <!-- ── Main ─────────────────────────────────────────────────────────── -->
    <div class="adm-main">

        <!-- Top bar -->
        <div class="adm-topbar">
            <div class="adm-topbar-title">Dashboard <span>Overview</span></div>
            <div class="adm-topbar-right">
                <div class="adm-topbar-date">
                    <i class="bi bi-calendar3" style="font-size:14px;"></i>
                    <?= date('D, M j, Y') ?>
                </div>
                <div class="adm-bell" title="Notifications">
                    <i class="bi bi-bell"></i>
                    <?php if ($pendingPayments > 0): ?>
                        <span class="adm-bell-dot"></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="adm-content">

            <!-- Alert banner -->
            <?php if ($pendingPayments > 0): ?>
            <div class="adm-alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>
                    <strong><?= $pendingPayments ?> pending payment<?= $pendingPayments > 1 ? 's' : '' ?></strong>
                    require your attention.
                    <a href="/admin/manage_bookings.php?status=pending">Review now &rarr;</a>
                </span>
            </div>
            <?php endif; ?>

            <!-- Row 1: 4 stat cards -->
            <div class="adm-stats-grid">

                <div class="adm-stat green">
                    <div class="adm-stat-icon"><i class="bi bi-calendar2-check"></i></div>
                    <div class="adm-stat-label">Total Bookings</div>
                    <div class="adm-stat-value"><?= $totalBookings ?></div>
                    <div class="adm-stat-sub green">
                        <i class="bi bi-arrow-up"></i> <?= $confirmedCount ?> confirmed
                    </div>
                </div>

                <div class="adm-stat blue">
                    <div class="adm-stat-icon"><i class="bi bi-currency-dollar"></i></div>
                    <div class="adm-stat-label">Total Revenue</div>
                    <div class="adm-stat-value" style="font-size:22px;">&#8369;<?= number_format($totalRevenue) ?></div>
                    <div class="adm-stat-sub">From paid transactions</div>
                </div>

                <div class="adm-stat purple">
                    <div class="adm-stat-icon"><i class="bi bi-building"></i></div>
                    <div class="adm-stat-label">Partner Hotels</div>
                    <div class="adm-stat-value"><?= $totalHotels ?></div>
                    <div class="adm-stat-sub">Active properties</div>
                </div>

                <div class="adm-stat rose">
                    <div class="adm-stat-icon"><i class="bi bi-people"></i></div>
                    <div class="adm-stat-label">Registered Guests</div>
                    <div class="adm-stat-value"><?= $totalCustomers ?></div>
                    <div class="adm-stat-sub">Platform accounts</div>
                </div>

            </div>

            <!-- Row 2: 4 stat cards -->
            <div class="adm-stats-grid">

                <div class="adm-stat yellow">
                    <div class="adm-stat-icon"><i class="bi bi-hourglass-split"></i></div>
                    <div class="adm-stat-label">Pending Payments</div>
                    <div class="adm-stat-value"><?= $pendingPayments ?></div>
                    <div class="adm-stat-sub red">
                        <?= $pendingPayments > 0 ? 'Action required' : 'All clear' ?>
                    </div>
                </div>

                <div class="adm-stat red">
                    <div class="adm-stat-icon"><i class="bi bi-x-circle"></i></div>
                    <div class="adm-stat-label">Cancellation Rate</div>
                    <div class="adm-stat-value"><?= $cancelRate ?>%</div>
                    <div class="adm-stat-sub"><?= $cancelledCount ?> cancelled bookings</div>
                </div>

                <div class="adm-stat orange">
                    <div class="adm-stat-icon"><i class="bi bi-star-fill"></i></div>
                    <div class="adm-stat-label">Avg. Rating</div>
                    <div class="adm-stat-value"><?= $avgRating ?></div>
                    <div class="adm-stat-sub">Platform-wide guest score</div>
                </div>

                <div class="adm-stat teal">
                    <div class="adm-stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
                    <div class="adm-stat-label">Confirmed Bookings</div>
                    <div class="adm-stat-value"><?= $confirmedCount ?></div>
                    <div class="adm-stat-sub">
                        <?= $totalBookings > 0 ? round(($confirmedCount / $totalBookings) * 100) : 0 ?>% of total
                    </div>
                </div>

            </div>

            <!-- Recent Bookings table -->
            <div class="adm-table-card">
                <div class="adm-table-header">
                    <h5>Recent Bookings</h5>
                    <a href="/admin/manage_bookings.php">View all</a>
                </div>

                <?php if (empty($recentBookings)): ?>
                    <div style="padding:40px; text-align:center; color:#9CA3AF; font-size:14px;">No bookings yet.</div>
                <?php else: ?>
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
                        <thead>
                            <tr style="background:#FAFBFC;">
                                <th style="padding:12px 18px; text-align:left; font-size:11px; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap;">#</th>
                                <th style="padding:12px 18px; text-align:left; font-size:11px; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap;">Guest</th>
                                <th style="padding:12px 18px; text-align:left; font-size:11px; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap;">Hotel</th>
                                <th style="padding:12px 18px; text-align:left; font-size:11px; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap;">Check-in</th>
                                <th style="padding:12px 18px; text-align:left; font-size:11px; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap;">Total</th>
                                <th style="padding:12px 18px; text-align:left; font-size:11px; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap;">Status</th>
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
                        <tr style="border-bottom:1px solid #F3F4F6; transition:background 0.12s;"
                            onmouseover="this.style.background='#FAFBFC'"
                            onmouseout="this.style.background=''">
                            <td style="padding:14px 18px; color:#9CA3AF; font-weight:600;">#<?= str_pad($b['id'],4,'0',STR_PAD_LEFT) ?></td>
                            <td style="padding:14px 18px; font-weight:600; color:#111827;"><?= htmlspecialchars($b['custName']) ?></td>
                            <td style="padding:14px 18px; color:#6B7280;"><?= htmlspecialchars($b['hotelName']) ?></td>
                            <td style="padding:14px 18px; color:#6B7280;"><?= date('M d, Y', strtotime($b['checkIn'])) ?></td>
                            <td style="padding:14px 18px; font-weight:700; color:var(--rd-red);">&#8369;<?= number_format($b['totalPrice']) ?></td>
                            <td style="padding:14px 18px;"><?= $badge ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

        </div><!-- /adm-content -->
    </div><!-- /adm-main -->
</div><!-- /admin-shell -->

<?php include "../layout/footer.php"; ?>
