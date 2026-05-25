<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'hotel_owner') {
    header("Location: /auth/login.php"); exit();
}

$hotelId = (int) ($_SESSION['hotel_id'] ?? 0);
if (!$hotelId) { header("Location: /auth/logout.php"); exit(); }

// Handle add room
$msg = '';
if (isset($_POST['add_room'])) {
    $type  = trim($_POST['room_type']        ?? '');
    $price = max(0, (float) $_POST['room_price']);
    $cap   = max(1, (int) $_POST['room_capacity']);
    $desc  = trim($_POST['room_description'] ?? '');
    if ($type !== '') {
        fs_insert('rooms', [
            'hotelId'     => $hotelId,
            'type'        => $type,
            'price'       => $price,
            'capacity'    => $cap,
            'description' => $desc,
            'status'      => 'available',
        ]);
        $msg = 'Room added successfully.';
    } else {
        $msg = 'Room type/name is required.';
    }
    header("Location: manage_rooms.php?msg=" . urlencode($msg)); exit();
}

// Handle delete room
if (isset($_POST['delete_room'])) {
    $roomId = (int) $_POST['room_id'];
    $check  = fs_find('rooms', [['id', '=', $roomId], ['hotelId', '=', $hotelId]]);
    if ($check) {
        fs_delete('rooms', $roomId);
        $msg = 'Room deleted.';
    }
    header("Location: manage_rooms.php?msg=" . urlencode($msg)); exit();
}

// Handle room edit
if (isset($_POST['edit_room'])) {
    $roomId = (int) $_POST['room_id'];
    $type   = trim($_POST['room_type']        ?? '');
    $price  = max(0, (float) $_POST['room_price']);
    $cap    = max(1, (int) $_POST['room_capacity']);
    $desc   = trim($_POST['room_description'] ?? '');
    $check  = fs_find('rooms', [['id', '=', $roomId], ['hotelId', '=', $hotelId]]);
    if ($check && $type !== '') {
        fs_update('rooms', $roomId, [
            'type'        => $type,
            'price'       => $price,
            'capacity'    => $cap,
            'description' => $desc,
        ]);
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
        $check = fs_find('rooms', [['id', '=', $roomId], ['hotelId', '=', $hotelId]]);
        if ($check) {
            fs_update('rooms', $roomId, ['status' => $newStatus]);
            $msg = 'Room status updated.';
        }
    }
    header("Location: manage_rooms.php?msg=" . urlencode($msg)); exit();
}

// Handle block date add
if (isset($_POST['add_block'])) {
    $roomId   = (int) $_POST['block_room_id'];
    $dateFrom = $_POST['block_from']   ?? '';
    $dateTo   = $_POST['block_to']     ?? '';
    $reason   = $_POST['block_reason'] ?? 'maintenance';
    if ($dateFrom && $dateTo && $dateTo > $dateFrom) {
        $check = fs_find('rooms', [['id', '=', $roomId], ['hotelId', '=', $hotelId]]);
        if ($check) {
            fs_insert('blockeddates', [
                'roomId'   => $roomId,
                'hotelId'  => $hotelId,
                'dateFrom' => $dateFrom,
                'dateTo'   => $dateTo,
                'reason'   => $reason,
            ]);
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
    $check   = fs_find('blockeddates', [['id', '=', $blockId], ['hotelId', '=', $hotelId]]);
    if ($check) {
        fs_delete('blockeddates', $blockId);
    }
    header("Location: manage_rooms.php?msg=Block+removed."); exit();
}

$rooms = fs_query('rooms', [['hotelId', '=', $hotelId]], [['type', 'ASC']]);

// Load blocked dates
$allBlocks = fs_query('blockeddates', [['hotelId', '=', $hotelId]], [['dateFrom', 'ASC']]);
$blocks = [];
foreach ($allBlocks as $bl) {
    $blocks[$bl['roomId']][] = $bl;
}

$title = "Manage Rooms";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:auto;">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:12px;">
            <div class="page-header" style="margin:0;">
                <h1>Rooms</h1>
                <p>Manage rooms, availability, and blocked dates for your hotel.</p>
            </div>
            <button type="button" class="btn-rd" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                <i class="bi bi-plus-lg me-1"></i>Add Room
            </button>
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

        <?php if (empty($rooms)): ?>
        <div style="padding:48px; text-align:center; background:#fff; border-radius:14px; color:#999; font-size:14px; box-shadow:var(--rd-shadow);">
            No rooms found for your hotel.
        </div>
        <?php else: ?>
        <?php foreach ($rooms as $room):
            $statusBadge = match($room['status']) {
                'available'   => '<span class="badge-available">Available</span>',
                'maintenance' => '<span class="badge-voided">Maintenance</span>',
                'occupied'    => '<span class="badge-checked-in">Occupied</span>',
                default       => '<span class="badge-pending">' . htmlspecialchars($room['status']) . '</span>',
            };
            $roomBlocks = $blocks[$room['id']] ?? [];
        ?>
        <div style="background:#fff; border-radius:14px; box-shadow:var(--rd-shadow); margin-bottom:20px; overflow:hidden; border:1px solid rgba(228,223,223,0.5);">
            <!-- Room header -->
            <div style="padding:18px 22px; border-bottom:1px solid var(--rd-border); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                <div>
                    <div style="font-size:15px; font-weight:700;"><?= htmlspecialchars($room['type']) ?></div>
                    <div style="font-size:13px; color:var(--rd-muted); margin-top:2px;">
                        &#8369;<?= number_format($room['price']) ?>/night &bull; Max <?= $room['capacity'] ?> guests
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <?= $statusBadge ?>
                    <button type="button" class="btn-rd-outline" style="font-size:12px; padding:5px 14px;"
                            data-bs-toggle="modal" data-bs-target="#editRoomModal"
                            data-room-id="<?= $room['id'] ?>"
                            data-room-type="<?= htmlspecialchars($room['type'], ENT_QUOTES) ?>"
                            data-room-price="<?= $room['price'] ?>"
                            data-room-capacity="<?= $room['capacity'] ?>"
                            data-room-description="<?= htmlspecialchars($room['description'] ?? '', ENT_QUOTES) ?>">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </button>
                    <button type="button" class="btn-rd-outline" style="font-size:12px; padding:5px 14px;"
                            data-bs-toggle="modal" data-bs-target="#toggleModal"
                            data-room-id="<?= $room['id'] ?>"
                            data-room-type="<?= htmlspecialchars($room['type']) ?>"
                            data-room-status="<?= htmlspecialchars($room['status']) ?>">
                        <i class="bi bi-toggle-on me-1"></i>Change Status
                    </button>
                    <button type="button" class="btn-rd" style="font-size:12px; padding:5px 14px;"
                            data-bs-toggle="modal" data-bs-target="#blockModal"
                            data-room-id="<?= $room['id'] ?>"
                            data-room-type="<?= htmlspecialchars($room['type']) ?>">
                        <i class="bi bi-calendar-x me-1"></i>Block Dates
                    </button>
                    <form method="POST" style="margin:0;" onsubmit="return confirm('Delete this room? This cannot be undone.');">
                        <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                        <button type="submit" name="delete_room"
                                style="background:none; border:1px solid #FECACA; color:#B91C1C; padding:5px 14px; font-size:12px; border-radius:8px; cursor:pointer; font-weight:600; font-family:'DM Sans',sans-serif;">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>
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
                        <?= date('M d', strtotime($bl['dateFrom'])) ?> &ndash; <?= date('M d, Y', strtotime($bl['dateTo'])) ?>
                        <span style="color:#999;">(<?= htmlspecialchars($bl['reason']) ?>)</span>
                        <form method="POST" style="margin:0;" onsubmit="return confirm('Remove this block?');">
                            <input type="hidden" name="block_id" value="<?= $bl['id'] ?>">
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
        <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none;">
            <div class="modal-header" style="border-bottom:1px solid var(--rd-border); padding:20px 24px;">
                <h5 class="modal-title" style="font-size:16px; font-weight:700;">Add New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body" style="padding:24px;">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Room Type / Name <span style="color:var(--rd-red)">*</span></label>
                            <input type="text" name="room_type" class="form-control" placeholder="e.g. Deluxe Room, Suite" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Price per Night (₱) <span style="color:var(--rd-red)">*</span></label>
                            <input type="number" name="room_price" class="form-control" min="0" step="0.01" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Max Guests <span style="color:var(--rd-red)">*</span></label>
                            <input type="number" name="room_capacity" class="form-control" min="1" max="20" value="2" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="room_description" class="form-control" rows="3" placeholder="Brief description of the room, bed type, view, etc..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--rd-border); padding:16px 24px;">
                    <button type="button" class="btn-rd-outline" data-bs-dismiss="modal" style="padding:9px 22px;">Cancel</button>
                    <button type="submit" name="add_room" class="btn-rd" style="padding:9px 22px;">Add Room</button>
                </div>
            </form>
        </div>
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
