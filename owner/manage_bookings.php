<?php
session_start();
require_once "../config/db.php";
require_once "../config/notify.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'hotel_owner') {
    header("Location: /auth/login.php"); exit();
}

$hotelId = (int) ($_SESSION['hotel_id'] ?? 0);
if (!$hotelId) { header("Location: /auth/logout.php"); exit(); }

// Handle status update
$updateMsg = '';
if (isset($_POST['update_status'])) {
    $bookId    = (int) $_POST['book_id'];
    $newStatus = $_POST['new_status'] ?? '';
    $allowed   = ['confirmed', 'checked_in', 'completed', 'cancelled'];

    if (in_array($newStatus, $allowed)) {
        // Verify this booking belongs to owner's hotel
        $bRow = fs_find('bookings', [['id', '=', $bookId], ['hotelId', '=', $hotelId]]);
        if ($bRow) {
            $oldStatus = $bRow['status'];
            fs_update('bookings', $bookId, ['status' => $newStatus]);

            if ($newStatus === 'confirmed' && $oldStatus === 'pending') {
                // Walk-in payment collected — create Earnings record if not already existing
                $alreadyExists = fs_find('earnings', [['bookId', '=', $bookId]]);
                if (!$alreadyExists) {
                    $hotel = fs_get('hotels', $hotelId);
                    $total       = (float) $bRow['totalPrice'];
                    $ownerShare  = round($total * 0.85, 2);
                    $platformFee = round($total * 0.15, 2);
                    $earnOwnerId = (int)($hotel['ownerId'] ?? 0);
                    fs_insert('earnings', [
                        'bookId'      => $bookId,
                        'hotelId'     => $hotelId,
                        'ownerId'     => $earnOwnerId,
                        'totalAmount' => $total,
                        'ownerShare'  => $ownerShare,
                        'platformFee' => $platformFee,
                        'status'      => 'pending',
                    ]);
                }
            }

            if ($newStatus === 'cancelled') {
                $earnRow = fs_find('earnings', [['bookId', '=', $bookId]]);
                if ($earnRow) {
                    fs_update('earnings', (int)$earnRow['id'], ['status' => 'voided']);
                }
            }

            // Push notification on key status changes
            $custRow    = fs_get('customers', (int)$bRow['custId']);
            $custAcctId = (int)($custRow['acctId'] ?? 0);
            $bookRef    = 'RD-' . str_pad($bookId, 4, '0', STR_PAD_LEFT);
            $hotelRow   = fs_get('hotels', $hotelId);
            $hotelName  = $hotelRow['name'] ?? '';
            $notifMap   = [
                'confirmed'  => ['Booking Confirmed! 🎉', "Your booking {$bookRef} at {$hotelName} has been confirmed."],
                'checked_in' => ['Checked In ✅',         "Welcome to {$hotelName}! Enjoy your stay."],
                'completed'  => ['Stay Completed',        "Thank you for staying at {$hotelName}. We hope to see you again!"],
                'cancelled'  => ['Booking Cancelled',     "Your booking {$bookRef} has been cancelled."],
            ];
            if (isset($notifMap[$newStatus])) {
                [$nTitle, $nBody] = $notifMap[$newStatus];
                sendPushNotification(null, $custAcctId, $nTitle, $nBody, ['booking_id' => (string)$bookId]);
            }
            syncBookingToFirestore(null, $bookId);

            $updateMsg = 'Booking #' . str_pad($bookId,4,'0',STR_PAD_LEFT) . ' updated.';
        }
    }
    header("Location: manage_bookings.php?msg=" . urlencode($updateMsg)); exit();
}

// Filter
$statusFilter = $_GET['status'] ?? '';

$wheres = [['hotelId', '=', $hotelId]];
if ($statusFilter) {
    $wheres[] = ['status', '=', $statusFilter];
}

$bookings = fs_query('bookings', $wheres, [['createdAt', 'DESC']]);

// Enrich with room type, customer name, and email
foreach ($bookings as &$b) {
    $room = fs_get('rooms', (int)$b['roomId']);
    $b['roomType'] = $room['type'] ?? '';

    $cust = fs_get('customers', (int)$b['custId']);
    $b['custFirstName'] = $cust['firstName'] ?? '';
    $b['custLastName']  = $cust['lastName']  ?? '';

    $acct = $cust ? fs_get('accounts', (int)$cust['acctId']) : null;
    $b['acctEmail'] = $acct['email'] ?? '';
}
unset($b);

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
                    <?php if (empty($bookings)): ?>
                    <tr><td colspan="8" style="text-align:center; padding:40px; color:#999;">No bookings found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($bookings as $b):
                        $badge = match($b['status']) {
                            'confirmed'  => '<span class="badge-confirmed">Confirmed</span>',
                            'cancelled'  => '<span class="badge-cancelled">Cancelled</span>',
                            'completed'  => '<span class="badge-completed">Completed</span>',
                            'checked_in' => '<span class="badge-checked-in">Checked In</span>',
                            default      => '<span class="badge-pending">Pending</span>',
                        };
                    ?>
                    <tr>
                        <td style="color:#999;">#<?= str_pad($b['id'],4,'0',STR_PAD_LEFT) ?></td>
                        <td>
                            <div style="font-weight:600;"><?= htmlspecialchars($b['custFirstName'].' '.$b['custLastName']) ?></div>
                            <div style="font-size:12px; color:#999;"><?= htmlspecialchars($b['acctEmail']) ?></div>
                        </td>
                        <td><?= htmlspecialchars($b['roomType']) ?></td>
                        <td><?= date('M d, Y', strtotime($b['checkIn'])) ?></td>
                        <td><?= date('M d, Y', strtotime($b['checkOut'])) ?></td>
                        <td style="font-weight:700; color:var(--rd-red);">&#8369;<?= number_format($b['totalPrice']) ?></td>
                        <td><?= $badge ?></td>
                        <td>
                            <?php if (in_array($b['status'], ['confirmed','pending'])): ?>
                            <button type="button" class="btn-rd" style="font-size:12px; padding:5px 12px;"
                                    data-bs-toggle="modal" data-bs-target="#statusModal"
                                    data-id="<?= $b['id'] ?>"
                                    data-status="<?= htmlspecialchars($b['status']) ?>">
                                Update
                            </button>
                            <?php else: ?>
                            <span style="font-size:12px; color:#aaa;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
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
