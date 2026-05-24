<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'hotel_owner') {
    header("Location: /auth/login.php"); exit();
}

$hotelId = (int) ($_SESSION['hotel_id'] ?? 0);
if (!$hotelId) { header("Location: /auth/logout.php"); exit(); }

// Handle room edit
$msg = '';
if (isset($_POST['edit_room'])) {
    $roomId  = (int) $_POST['room_id'];
    $type    = $conn->real_escape_string(trim($_POST['room_type']        ?? ''));
    $price   = max(0, (float) $_POST['room_price']);
    $cap     = max(1, (int) $_POST['room_capacity']);
    $desc    = $conn->real_escape_string(trim($_POST['room_description'] ?? ''));
    $check   = $conn->query("SELECT Room_Id FROM Rooms WHERE Room_Id=$roomId AND Room_HotelId=$hotelId LIMIT 1");
    if ($check->num_rows > 0 && $type !== '') {
        $conn->query("UPDATE Rooms SET Room_Type='$type', Room_Price=$price, Room_Capacity=$cap, Room_Description='$desc' WHERE Room_Id=$roomId");
        $msg = 'Room updated successfully.';
    } else {
        $msg = 'Invalid room or missing name.';
    }
    header("Location: manage_rooms.php?msg=" . urlencode($msg)); exit();
}

// Handle room status toggle
if (isset($_POST['toggle_status'])) {
    $roomId    = (int) $_POST['room_id'];
    $newStatus = $_POST['new_status'] ?? '';
    $allowed   = ['available', 'maintenance', 'occupied'];
    if (in_array($newStatus, $allowed)) {
        $check = $conn->query("SELECT Room_Id FROM Rooms WHERE Room_Id=$roomId AND Room_HotelId=$hotelId LIMIT 1");
        if ($check->num_rows > 0) {
            $conn->query("UPDATE Rooms SET Room_Status='$newStatus' WHERE Room_Id=$roomId");
            $msg = 'Room status updated.';
        }
    }
    header("Location: manage_rooms.php?msg=" . urlencode($msg)); exit();
}

// Handle block date add
if (isset($_POST['add_block'])) {
    $roomId    = (int) $_POST['block_room_id'];
    $dateFrom  = $conn->real_escape_string($_POST['block_from'] ?? '');
    $dateTo    = $conn->real_escape_string($_POST['block_to']   ?? '');
    $reason    = $conn->real_escape_string($_POST['block_reason'] ?? 'maintenance');
    $hasTable  = $conn->query("SHOW TABLES LIKE 'BlockedDates'")->num_rows > 0;
    if ($hasTable && $dateFrom && $dateTo && $dateTo > $dateFrom) {
        $check = $conn->query("SELECT Room_Id FROM Rooms WHERE Room_Id=$roomId AND Room_HotelId=$hotelId LIMIT 1");
        if ($check->num_rows > 0) {
            $conn->query("INSERT INTO BlockedDates (Block_RoomId,Block_HotelId,Block_DateFrom,Block_DateTo,Block_Reason) VALUES ($roomId,$hotelId,'$dateFrom','$dateTo','$reason')");
            $msg = 'Dates blocked successfully.';
        }
    } else {
        $msg = 'Invalid date range.';
    }
    header("Location: manage_rooms.php?msg=" . urlencode($msg)); exit();
}

// Handle block delete
if (isset($_POST['delete_block'])) {
    $blockId = (int) $_POST['block_id'];
    $conn->query("DELETE FROM BlockedDates WHERE Block_Id=$blockId AND Block_HotelId=$hotelId");
    header("Location: manage_rooms.php?msg=Block+removed."); exit();
}

$rooms = $conn->query("SELECT * FROM Rooms WHERE Room_HotelId=$hotelId ORDER BY Room_Type");

// Load blocked dates
$hasBlockTable = $conn->query("SHOW TABLES LIKE 'BlockedDates'")->num_rows > 0;
$blocks = [];
if ($hasBlockTable) {
    $bRes = $conn->query("SELECT * FROM BlockedDates WHERE Block_HotelId=$hotelId ORDER BY Block_DateFrom");
    while ($bl = $bRes->fetch_assoc()) {
        $blocks[$bl['Block_RoomId']][] = $bl;
    }
}

$title = "Manage Rooms";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:auto;">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <div class="page-header">
            <h1>Rooms</h1>
            <p>View room availability and block dates for your hotel.</p>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg']): ?>
        <div class="alert-rd-success mb-4" style="display:flex; align-items:center; gap:9px;">
            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($_GET['msg']) ?>
        </div>
        <?php endif; ?>

        <!-- Info notice -->
        <div style="background:#EFF6FF; border:1px solid #BFDBFE; border-radius:8px; padding:12px 16px; font-size:13px; color:#1E40AF; margin-bottom:24px; display:flex; align-items:center; gap:9px;">
            <i class="bi bi-info-circle-fill" style="flex-shrink:0;"></i>
            You can edit room details, update availability status, and block specific dates for your rooms below.
        </div>

        <?php if ($rooms->num_rows === 0): ?>
        <div style="padding:48px; text-align:center; background:#fff; border-radius:14px; color:#999; font-size:14px; box-shadow:var(--rd-shadow);">
            No rooms found for your hotel.
        </div>
        <?php else: ?>
        <?php while ($room = $rooms->fetch_assoc()):
            $statusBadge = match($room['Room_Status']) {
                'available'   => '<span class="badge-available">Available</span>',
                'maintenance' => '<span class="badge-voided">Maintenance</span>',
                'occupied'    => '<span class="badge-checked-in">Occupied</span>',
                default       => '<span class="badge-pending">' . htmlspecialchars($room['Room_Status']) . '</span>',
            };
            $roomBlocks = $blocks[$room['Room_Id']] ?? [];
        ?>
        <div style="background:#fff; border-radius:14px; box-shadow:var(--rd-shadow); margin-bottom:20px; overflow:hidden; border:1px solid rgba(228,223,223,0.5);">
            <!-- Room header -->
            <div style="padding:18px 22px; border-bottom:1px solid var(--rd-border); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                <div>
                    <div style="font-size:15px; font-weight:700;"><?= htmlspecialchars($room['Room_Type']) ?></div>
                    <div style="font-size:13px; color:var(--rd-muted); margin-top:2px;">
                        &#8369;<?= number_format($room['Room_Price']) ?>/night &bull; Max <?= $room['Room_Capacity'] ?> guests
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <?= $statusBadge ?>
                    <button type="button" class="btn-rd-outline" style="font-size:12px; padding:5px 14px;"
                            data-bs-toggle="modal" data-bs-target="#editRoomModal"
                            data-room-id="<?= $room['Room_Id'] ?>"
                            data-room-type="<?= htmlspecialchars($room['Room_Type'], ENT_QUOTES) ?>"
                            data-room-price="<?= $room['Room_Price'] ?>"
                            data-room-capacity="<?= $room['Room_Capacity'] ?>"
                            data-room-description="<?= htmlspecialchars($room['Room_Description'] ?? '', ENT_QUOTES) ?>">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </button>
                    <button type="button" class="btn-rd-outline" style="font-size:12px; padding:5px 14px;"
                            data-bs-toggle="modal" data-bs-target="#toggleModal"
                            data-room-id="<?= $room['Room_Id'] ?>"
                            data-room-type="<?= htmlspecialchars($room['Room_Type']) ?>"
                            data-room-status="<?= htmlspecialchars($room['Room_Status']) ?>">
                        <i class="bi bi-toggle-on me-1"></i>Change Status
                    </button>
                    <button type="button" class="btn-rd" style="font-size:12px; padding:5px 14px;"
                            data-bs-toggle="modal" data-bs-target="#blockModal"
                            data-room-id="<?= $room['Room_Id'] ?>"
                            data-room-type="<?= htmlspecialchars($room['Room_Type']) ?>">
                        <i class="bi bi-calendar-x me-1"></i>Block Dates
                    </button>
                </div>
            </div>

            <!-- Blocked dates list -->
            <?php if ($roomBlocks): ?>
            <div style="padding:14px 22px; background:var(--rd-bg); border-top:1px solid var(--rd-border);">
                <div style="font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:var(--rd-muted); margin-bottom:10px;">Blocked Periods</div>
                <div style="display:flex; flex-wrap:wrap; gap:8px;">
                    <?php foreach ($roomBlocks as $bl): ?>
                    <div style="display:inline-flex; align-items:center; gap:8px; background:#fff; border:1px solid var(--rd-border); border-radius:8px; padding:6px 12px; font-size:12px;">
                        <i class="bi bi-calendar-x" style="color:var(--rd-red);"></i>
                        <?= date('M d', strtotime($bl['Block_DateFrom'])) ?> &ndash; <?= date('M d, Y', strtotime($bl['Block_DateTo'])) ?>
                        <span style="color:#999;">(<?= htmlspecialchars($bl['Block_Reason']) ?>)</span>
                        <form method="POST" style="margin:0;" onsubmit="return confirm('Remove this block?');">
                            <input type="hidden" name="block_id" value="<?= $bl['Block_Id'] ?>">
                            <button type="submit" name="delete_block" style="background:none; border:none; color:#ccc; cursor:pointer; padding:0; font-size:13px; line-height:1;">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
        <?php endwhile; ?>
        <?php endif; ?>

    </div>
</div>

<!-- Edit Room Modal -->
<div class="modal fade" id="editRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none;">
            <div class="modal-header" style="border-bottom:1px solid var(--rd-border); padding:20px 24px;">
                <h5 class="modal-title" style="font-size:16px; font-weight:700;">Edit Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body" style="padding:24px;">
                    <input type="hidden" name="room_id" id="edit_room_id">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Room Type / Name <span style="color:var(--rd-red)">*</span></label>
                            <input type="text" name="room_type" id="edit_room_type" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Price per Night (₱) <span style="color:var(--rd-red)">*</span></label>
                            <input type="number" name="room_price" id="edit_room_price" class="form-control" min="0" step="0.01" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Max Guests <span style="color:var(--rd-red)">*</span></label>
                            <input type="number" name="room_capacity" id="edit_room_capacity" class="form-control" min="1" max="20" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="room_description" id="edit_room_description" class="form-control" rows="3" placeholder="Brief description of the room..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--rd-border); padding:16px 24px;">
                    <button type="button" class="btn-rd-outline" data-bs-dismiss="modal" style="padding:9px 22px;">Cancel</button>
                    <button type="submit" name="edit_room" class="btn-rd" style="padding:9px 22px;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toggle Status Modal -->
<div class="modal fade" id="toggleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none;">
            <div class="modal-header" style="border-bottom:1px solid var(--rd-border); padding:20px 24px;">
                <h5 class="modal-title" style="font-size:16px; font-weight:700;">Change Room Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body" style="padding:24px;">
                    <input type="hidden" name="room_id" id="toggle_room_id">
                    <p style="font-size:14px; color:#555; margin-bottom:16px;">
                        Updating status for: <strong id="toggle_room_type"></strong>
                    </p>
                    <label class="form-label">New Status</label>
                    <select name="new_status" class="form-select">
                        <option value="available">Available</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="occupied">Occupied</option>
                    </select>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--rd-border); padding:16px 24px;">
                    <button type="button" class="btn-rd-outline" data-bs-dismiss="modal" style="padding:9px 22px;">Close</button>
                    <button type="submit" name="toggle_status" class="btn-rd" style="padding:9px 22px;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Block Dates Modal -->
<div class="modal fade" id="blockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none;">
            <div class="modal-header" style="border-bottom:1px solid var(--rd-border); padding:20px 24px;">
                <h5 class="modal-title" style="font-size:16px; font-weight:700;">Block Dates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body" style="padding:24px;">
                    <input type="hidden" name="block_room_id" id="block_room_id">
                    <p style="font-size:14px; color:#555; margin-bottom:16px;">
                        Blocking dates for: <strong id="block_room_type"></strong>
                    </p>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">From <span style="color:var(--rd-red)">*</span></label>
                            <input type="date" name="block_from" class="form-control" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">To <span style="color:var(--rd-red)">*</span></label>
                            <input type="date" name="block_to" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Reason</label>
                            <select name="block_reason" class="form-select">
                                <option value="maintenance">Maintenance</option>
                                <option value="walk_in">Walk-in / Reserved</option>
                                <option value="renovation">Renovation</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--rd-border); padding:16px 24px;">
                    <button type="button" class="btn-rd-outline" data-bs-dismiss="modal" style="padding:9px 22px;">Close</button>
                    <button type="submit" name="add_block" class="btn-rd" style="padding:9px 22px;">Block Dates</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('editRoomModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('edit_room_id').value          = btn.dataset.roomId;
    document.getElementById('edit_room_type').value        = btn.dataset.roomType;
    document.getElementById('edit_room_price').value       = btn.dataset.roomPrice;
    document.getElementById('edit_room_capacity').value    = btn.dataset.roomCapacity;
    document.getElementById('edit_room_description').value = btn.dataset.roomDescription;
});
document.getElementById('toggleModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('toggle_room_id').value   = btn.dataset.roomId;
    document.getElementById('toggle_room_type').textContent = btn.dataset.roomType;
    const sel = this.querySelector('select[name="new_status"]');
    sel.value = btn.dataset.roomStatus;
});
document.getElementById('blockModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('block_room_id').value    = btn.dataset.roomId;
    document.getElementById('block_room_type').textContent = btn.dataset.roomType;
});
</script>

<?php include "../layout/footer.php"; ?>
