<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'hotel_owner') {
    header("Location: /auth/login.php"); exit();
}

$hotelId = (int) ($_SESSION['hotel_id'] ?? 0);
if (!$hotelId) { header("Location: /auth/logout.php"); exit(); }

// Handle status update (check-in / check-out only)
$updateMsg = '';
if (isset($_POST['update_status'])) {
    $bookId    = (int) $_POST['book_id'];
    $newStatus = $_POST['new_status'] ?? '';
    $allowed   = ['checked_in', 'completed', 'cancelled'];

    $allowed = ['confirmed', 'checked_in', 'completed', 'cancelled'];

    if (in_array($newStatus, $allowed)) {
        // Verify this booking belongs to owner's hotel
        $bRow = $conn->query("SELECT b.*, h.Hotel_OwnerId FROM Bookings b JOIN Hotels h ON h.Hotel_Id=b.Book_HotelId WHERE b.Book_Id=$bookId AND b.Book_HotelId=$hotelId LIMIT 1")->fetch_assoc();
        if ($bRow) {
            $conn->query("UPDATE Bookings SET Book_Status='$newStatus' WHERE Book_Id=$bookId");

            $hasEarningsTable = $conn->query("SHOW TABLES LIKE 'Earnings'")->num_rows > 0;

            if ($newStatus === 'confirmed' && $bRow['Book_Status'] === 'pending') {
                // Walk-in payment collected — create Earnings record now
                if ($hasEarningsTable) {
                    $alreadyExists = $conn->query("SELECT Earn_Id FROM Earnings WHERE Earn_BookId=$bookId LIMIT 1")->num_rows > 0;
                    if (!$alreadyExists) {
                        $total       = (float) $bRow['Book_TotalPrice'];
                        $ownerShare  = round($total * 0.85, 2);
                        $platformFee = round($total * 0.15, 2);
                        $earnOwnerId = $bRow['Hotel_OwnerId'] ? (int)$bRow['Hotel_OwnerId'] : 'NULL';
                        $conn->query("
                            INSERT INTO Earnings
                                (Earn_BookId, Earn_HotelId, Earn_OwnerId, Earn_TotalAmount, Earn_OwnerShare, Earn_PlatformFee, Earn_Status)
                            VALUES
                                ($bookId, $hotelId, $earnOwnerId, $total, $ownerShare, $platformFee, 'pending')
                        ");
                    }
                }
            }

            if ($newStatus === 'cancelled') {
                if ($hasEarningsTable) {
                    $conn->query("UPDATE Earnings SET Earn_Status='voided' WHERE Earn_BookId=$bookId");
                }
            }

            $updateMsg = 'Booking #' . str_pad($bookId,4,'0',STR_PAD_LEFT) . ' updated.';
        }
    }
    header("Location: manage_bookings.php?msg=" . urlencode($updateMsg)); exit();
}

// Filter
$statusFilter = $conn->real_escape_string($_GET['status'] ?? '');
$where = "b.Book_HotelId = $hotelId";
if ($statusFilter) $where .= " AND b.Book_Status = '$statusFilter'";

$bookings = $conn->query("
    SELECT b.*, r.Room_Type, c.Cust_FName, c.Cust_LName, a.Acct_Email
    FROM Bookings b
    JOIN Rooms     r ON r.Room_Id  = b.Book_RoomId
    JOIN Customers c ON c.Cust_Id  = b.Book_CustId
    JOIN Accounts  a ON a.Acct_Id  = c.Cust_AcctId
    WHERE $where
    ORDER BY b.Book_CreatedAt DESC
");

$title = "Manage Bookings";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:auto;">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <div class="page-header">
            <h1>Bookings</h1>
            <p>View and manage all bookings for your hotel.</p>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg']): ?>
        <div class="alert-rd-success mb-4" style="display:flex; align-items:center; gap:9px;">
            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($_GET['msg']) ?>
        </div>
        <?php endif; ?>

        <!-- Filter tabs -->
        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px;">
            <?php
            $statuses = ['' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
            foreach ($statuses as $val => $lbl):
                $active = $statusFilter === $val;
            ?>
            <a href="?status=<?= $val ?>" style="
                padding:7px 16px; border-radius:8px; font-size:13px; font-weight:600;
                text-decoration:none; border:1.5px solid <?= $active ? 'var(--rd-red)' : 'var(--rd-border)' ?>;
                background:<?= $active ? 'var(--rd-red)' : '#fff' ?>;
                color:<?= $active ? '#fff' : '#555' ?>;
                transition:all 0.15s;
            "><?= $lbl ?></a>
            <?php endforeach; ?>
        </div>

        <div class="table-rd">
            <div style="overflow-x:auto;">
                <table class="table mb-0" style="font-size:14px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($bookings->num_rows === 0): ?>
                    <tr><td colspan="8" style="text-align:center; padding:40px; color:#999;">No bookings found.</td></tr>
                    <?php endif; ?>
                    <?php while ($b = $bookings->fetch_assoc()):
                        $badge = match($b['Book_Status']) {
                            'confirmed'  => '<span class="badge-confirmed">Confirmed</span>',
                            'cancelled'  => '<span class="badge-cancelled">Cancelled</span>',
                            'completed'  => '<span class="badge-completed">Completed</span>',
                            'checked_in' => '<span class="badge-checked-in">Checked In</span>',
                            default      => '<span class="badge-pending">Pending</span>',
                        };
                    ?>
                    <tr>
                        <td style="color:#999;">#<?= str_pad($b['Book_Id'],4,'0',STR_PAD_LEFT) ?></td>
                        <td>
                            <div style="font-weight:600;"><?= htmlspecialchars($b['Cust_FName'].' '.$b['Cust_LName']) ?></div>
                            <div style="font-size:12px; color:#999;"><?= htmlspecialchars($b['Acct_Email']) ?></div>
                        </td>
                        <td><?= htmlspecialchars($b['Room_Type']) ?></td>
                        <td><?= date('M d, Y', strtotime($b['Book_CheckIn'])) ?></td>
                        <td><?= date('M d, Y', strtotime($b['Book_CheckOut'])) ?></td>
                        <td style="font-weight:700; color:var(--rd-red);">&#8369;<?= number_format($b['Book_TotalPrice']) ?></td>
                        <td><?= $badge ?></td>
                        <td>
                            <?php if (in_array($b['Book_Status'], ['confirmed','pending'])): ?>
                            <button type="button" class="btn-rd" style="font-size:12px; padding:5px 12px;"
                                    data-bs-toggle="modal" data-bs-target="#statusModal"
                                    data-id="<?= $b['Book_Id'] ?>"
                                    data-status="<?= htmlspecialchars($b['Book_Status']) ?>">
                                Update
                            </button>
                            <?php else: ?>
                            <span style="font-size:12px; color:#aaa;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none;">
            <div class="modal-header" style="border-bottom:1px solid var(--rd-border); padding:20px 24px;">
                <h5 class="modal-title" style="font-size:16px; font-weight:700;">Update Booking Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body" style="padding:24px;">
                    <input type="hidden" name="book_id" id="modal_book_id">
                    <div class="mb-3">
                        <label class="form-label">New Status</label>
                        <select name="new_status" class="form-select">
                            <option value="checked_in">Checked In</option>
                            <option value="completed">Completed (Checked Out)</option>
                            <option value="cancelled">Cancel Booking</option>
                        </select>
                    </div>
                    <p style="font-size:13px; color:var(--rd-muted); margin:0;">
                        <i class="bi bi-info-circle me-1"></i>
                        Cancelling will void the earnings entry for this booking.
                    </p>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--rd-border); padding:16px 24px;">
                    <button type="button" class="btn-rd-outline" data-bs-dismiss="modal" style="padding:9px 22px;">Cancel</button>
                    <button type="submit" name="update_status" class="btn-rd" style="padding:9px 22px;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('statusModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('modal_book_id').value = btn.dataset.id;
});
</script>

<?php include "../layout/footer.php"; ?>
