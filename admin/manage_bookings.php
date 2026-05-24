<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth/login.php"); exit();
}

$message = "";

// ADMIN OVERRIDE: cancel-only
if (isset($_POST['update_status'])) {
    $bookId = (int) $_POST['book_id'];
    // Admin can only force-cancel; other transitions are handled by owner/customer/system
    if (isset($_POST['status']) && $_POST['status'] === 'cancelled') {
        $conn->query("UPDATE Bookings SET Book_Status='cancelled' WHERE Book_Id=$bookId");
        // Void earnings
        $hasEarningsTable = $conn->query("SHOW TABLES LIKE 'Earnings'")->num_rows > 0;
        if ($hasEarningsTable) {
            $conn->query("UPDATE Earnings SET Earn_Status='voided' WHERE Earn_BookId=$bookId");
        }
        $message = "Booking #" . str_pad($bookId,4,'0',STR_PAD_LEFT) . " has been cancelled and earnings voided.";
    }
}

// Filters
$filterStatus = $conn->real_escape_string($_GET['status'] ?? '');
$filterHotel  = (int) ($_GET['hotel'] ?? 0);
$search       = $conn->real_escape_string(trim($_GET['search'] ?? ''));

$where = "WHERE 1=1";
if ($filterStatus) $where .= " AND b.Book_Status='$filterStatus'";
if ($filterHotel)  $where .= " AND b.Book_HotelId=$filterHotel";
if ($search)       $where .= " AND (c.Cust_FName LIKE '%$search%' OR c.Cust_LName LIKE '%$search%' OR h.Hotel_Name LIKE '%$search%')";

$hasPaymentsTable = $conn->query("SHOW TABLES LIKE 'Payments'")->num_rows > 0;

if ($hasPaymentsTable) {
    $bookings = $conn->query("
        SELECT b.*, h.Hotel_Name, h.Hotel_City, r.Room_Type,
               c.Cust_FName, c.Cust_LName,
               p.Paymt_Method, p.Paymt_Status, p.Paymt_RefCode
        FROM Bookings b
        JOIN Hotels    h ON h.Hotel_Id = b.Book_HotelId
        JOIN Rooms     r ON r.Room_Id  = b.Book_RoomId
        JOIN Customers c ON c.Cust_Id  = b.Book_CustId
        LEFT JOIN Payments p ON p.Paymt_BookId = b.Book_Id
        $where
        ORDER BY b.Book_CreatedAt DESC
    ");
} else {
    $bookings = $conn->query("
        SELECT b.*, h.Hotel_Name, h.Hotel_City, r.Room_Type,
               c.Cust_FName, c.Cust_LName
        FROM Bookings b
        JOIN Hotels    h ON h.Hotel_Id = b.Book_HotelId
        JOIN Rooms     r ON r.Room_Id  = b.Book_RoomId
        JOIN Customers c ON c.Cust_Id  = b.Book_CustId
        $where
        ORDER BY b.Book_CreatedAt DESC
    ");
}

$hotels = $conn->query("SELECT Hotel_Id, Hotel_Name FROM Hotels ORDER BY Hotel_Name");

$title = "Manage Bookings";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:auto;">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <div class="page-header">
            <h1>Bookings</h1>
            <p>View and manage all hotel reservations.</p>
        </div>

        <?php if ($message): ?>
            <div class="alert-rd-success mb-4"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Filters -->
        <form method="GET" class="d-flex flex-wrap gap-2 mb-4 align-items-end">
            <div style="min-width:180px; flex:1;">
                <input type="text" name="search" class="form-control" placeholder="Search guest or hotel..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <select name="status" class="form-select" style="max-width:160px; font-size:14px;">
                <option value="">All Statuses</option>
                <option value="pending"   <?= $filterStatus==='pending'   ? 'selected' : '' ?>>Pending</option>
                <option value="confirmed" <?= $filterStatus==='confirmed' ? 'selected' : '' ?>>Confirmed</option>
                <option value="cancelled" <?= $filterStatus==='cancelled' ? 'selected' : '' ?>>Cancelled</option>
                <option value="completed" <?= $filterStatus==='completed' ? 'selected' : '' ?>>Completed</option>
            </select>
            <select name="hotel" class="form-select" style="max-width:200px; font-size:14px;">
                <option value="">All Hotels</option>
                <?php while ($h = $hotels->fetch_assoc()): ?>
                <option value="<?= $h['Hotel_Id'] ?>" <?= $filterHotel == $h['Hotel_Id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($h['Hotel_Name']) ?>
                </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn-rd" style="padding:10px 20px;">Filter</button>
            <a href="manage_bookings.php" style="font-size:13px; color:#999; align-self:center; text-decoration:none;">Clear</a>
        </form>

        <!-- Admin info banner -->
        <div style="background:#EFF6FF; border:1px solid #BFDBFE; border-radius:8px; padding:12px 16px; font-size:13px; color:#1E40AF; margin-bottom:20px; display:flex; align-items:center; gap:9px;">
            <i class="bi bi-info-circle-fill" style="flex-shrink:0;"></i>
            Admin override is limited to <strong>cancellation only</strong>. Room, check-in, and pricing changes are managed by hotel owners.
        </div>

        <p style="font-size:13px; color:#999; margin-bottom:16px;"><?= $bookings->num_rows ?> booking<?= $bookings->num_rows != 1 ? 's' : '' ?> found</p>

        <!-- Table -->
        <div style="background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,0.07); overflow:hidden;">
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:14px;">
                    <thead>
                        <tr style="background:#FAFAFA; border-bottom:1px solid #EBEBEB;">
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">#</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Guest</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Hotel / Room</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Check-in</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Nights</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Total</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Status</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Payment</th>
                            <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;">Update</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($bookings->num_rows === 0): ?>
                        <tr><td colspan="9" style="padding:40px; text-align:center; color:#999;">No bookings found.</td></tr>
                    <?php else: while ($b = $bookings->fetch_assoc()):
                        $nights = max(1, (new DateTime($b['Book_CheckIn']))->diff(new DateTime($b['Book_CheckOut']))->days);
                        $badge  = match($b['Book_Status']) {
                            'confirmed' => '<span class="badge-confirmed">Confirmed</span>',
                            'cancelled' => '<span class="badge-cancelled">Cancelled</span>',
                            'completed' => '<span class="badge-completed">Completed</span>',
                            default     => '<span class="badge-pending">Pending</span>',
                        };
                    ?>
                    <tr style="border-bottom:1px solid #F8F8F8;">
                        <td style="padding:14px 16px; color:#999; white-space:nowrap;">#<?= str_pad($b['Book_Id'],4,'0',STR_PAD_LEFT) ?></td>
                        <td style="padding:14px 16px; font-weight:600; white-space:nowrap;"><?= htmlspecialchars($b['Cust_FName'].' '.$b['Cust_LName']) ?></td>
                        <td style="padding:14px 16px; color:#555;">
                            <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($b['Hotel_Name']) ?></div>
                            <div style="font-size:12px; color:#999;"><?= htmlspecialchars($b['Room_Type']) ?></div>
                        </td>
                        <td style="padding:14px 16px; color:#555; white-space:nowrap;"><?= date('M d, Y', strtotime($b['Book_CheckIn'])) ?></td>
                        <td style="padding:14px 16px; color:#555; text-align:center;"><?= $nights ?></td>
                        <td style="padding:14px 16px; font-weight:700; color:var(--rd-red); white-space:nowrap;">₱<?= number_format($b['Book_TotalPrice']) ?></td>
                        <td style="padding:14px 16px;"><?= $badge ?></td>
                        <td style="padding:14px 16px;">
                            <?php if ($hasPaymentsTable && !empty($b['Paymt_Method'])): ?>
                                <?php
                                $methodShort = match($b['Paymt_Method']) {
                                    'gcash'        => 'GCash',
                                    'maya'         => 'Maya',
                                    'credit_card'  => 'Card',
                                    'pay_at_hotel' => 'At Hotel',
                                    default        => ucfirst($b['Paymt_Method']),
                                };
                                $paidBadge = $b['Paymt_Status'] === 'paid'
                                    ? '<span style="background:#D1E7DD;color:#0A3622;border-radius:4px;padding:2px 8px;font-size:11px;font-weight:700;">Paid</span>'
                                    : '<span style="background:#FFF3CD;color:#856404;border-radius:4px;padding:2px 8px;font-size:11px;font-weight:700;">Pending</span>';
                                ?>
                                <div style="font-size:12px; font-weight:600; color:#444;"><?= $methodShort ?></div>
                                <div style="margin-top:3px;"><?= $paidBadge ?></div>
                            <?php elseif ($b['Book_Status'] === 'pending'): ?>
                                <span style="font-size:12px; color:#999;">No payment</span>
                            <?php else: ?>
                                <span style="font-size:12px; color:#bbb;">—</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            <?php if (!in_array($b['Book_Status'], ['cancelled','completed'])): ?>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Force-cancel booking #<?= $b['Book_Id'] ?>? This will void earnings.');">
                                <input type="hidden" name="book_id" value="<?= $b['Book_Id'] ?>">
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" name="update_status"
                                        style="background:#FEF2F2; color:#B91C1C; border:1px solid #FECACA; padding:5px 14px; font-size:12px; border-radius:6px; cursor:pointer; font-weight:600; font-family:'DM Sans',sans-serif;">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </button>
                            </form>
                            <?php else: ?>
                            <span style="font-size:12px; color:#aaa;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
