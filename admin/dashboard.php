<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth/login.php"); exit();
}

$totalHotels    = $conn->query("SELECT COUNT(*) c FROM Hotels WHERE Hotel_Status='active'")->fetch_assoc()['c'];
$totalRooms     = $conn->query("SELECT COUNT(*) c FROM Rooms WHERE Room_Status='available'")->fetch_assoc()['c'];
$totalBookings  = $conn->query("SELECT COUNT(*) c FROM Bookings")->fetch_assoc()['c'];
$totalRevenue   = $conn->query("SELECT COALESCE(SUM(Book_TotalPrice),0) r FROM Bookings WHERE Book_Status IN ('confirmed','completed')")->fetch_assoc()['r'];
$totalCustomers = $conn->query("SELECT COUNT(*) c FROM Accounts WHERE Acct_Role='customer' AND Acct_Status='active'")->fetch_assoc()['c'];
$pendingPayments= $conn->query("SELECT COUNT(*) c FROM Bookings WHERE Book_Status='pending'")->fetch_assoc()['c'];

$recent = $conn->query("
    SELECT b.*, h.Hotel_Name, h.Hotel_City, r.Room_Type,
           c.Cust_FName, c.Cust_LName
    FROM Bookings b
    JOIN Hotels    h ON h.Hotel_Id = b.Book_HotelId
    JOIN Rooms     r ON r.Room_Id  = b.Book_RoomId
    JOIN Customers c ON c.Cust_Id  = b.Book_CustId
    ORDER BY b.Book_CreatedAt DESC
    LIMIT 8
");

$title = "Admin Dashboard";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:calc(100vh - 64px);">
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
        </div>

        <!-- Recent Bookings -->
        <div style="background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,0.07); overflow:hidden;">
            <div style="padding:18px 22px; border-bottom:1px solid #F0F0F0; display:flex; justify-content:space-between; align-items:center;">
                <h5 style="font-size:15px; font-weight:700; margin:0;">Recent Bookings</h5>
                <a href="/admin/manage_bookings.php" style="font-size:13px; color:var(--rd-red); font-weight:600; text-decoration:none;">View all</a>
            </div>

            <?php if ($recent->num_rows === 0): ?>
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
                    <?php while ($b = $recent->fetch_assoc()):
                        $badge = match($b['Book_Status']) {
                            'confirmed' => '<span class="badge-confirmed">Confirmed</span>',
                            'cancelled' => '<span class="badge-cancelled">Cancelled</span>',
                            'completed' => '<span class="badge-completed">Completed</span>',
                            default     => '<span class="badge-pending">Pending</span>',
                        };
                    ?>
                    <tr style="border-bottom:1px solid #F8F8F8;">
                        <td style="padding:14px 16px; color:#999;">#<?= str_pad($b['Book_Id'],4,'0',STR_PAD_LEFT) ?></td>
                        <td style="padding:14px 16px; font-weight:600;"><?= htmlspecialchars($b['Cust_FName'].' '.$b['Cust_LName']) ?></td>
                        <td style="padding:14px 16px; color:#555;"><?= htmlspecialchars($b['Hotel_Name']) ?></td>
                        <td style="padding:14px 16px; color:#555;"><?= date('M d, Y', strtotime($b['Book_CheckIn'])) ?></td>
                        <td style="padding:14px 16px; font-weight:700; color:var(--rd-red);">&#8369;<?= number_format($b['Book_TotalPrice']) ?></td>
                        <td style="padding:14px 16px;"><?= $badge ?></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
