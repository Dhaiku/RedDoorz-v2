<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth/login.php"); exit();
}

$message   = "";
$filterHotel = (int) ($_GET['hotel'] ?? 0);

// ADD
if (isset($_POST['add_room'])) {
    $hotelId = (int) $_POST['hotel_id'];
    $type    = $conn->real_escape_string(trim($_POST['type']));
    $price   = (float) $_POST['price'];
    $cap     = (int) $_POST['capacity'];
    $desc    = $conn->real_escape_string(trim($_POST['description']));
    $conn->query("INSERT INTO Rooms (Room_HotelId,Room_Type,Room_Price,Room_Capacity,Room_Description) VALUES ($hotelId,'$type',$price,$cap,'$desc')");
    $message = "Room added.";
}

// EDIT
if (isset($_POST['edit_room'])) {
    $id    = (int) $_POST['room_id'];
    $type  = $conn->real_escape_string(trim($_POST['type']));
    $price = (float) $_POST['price'];
    $cap   = (int) $_POST['capacity'];
    $desc  = $conn->real_escape_string(trim($_POST['description']));
    $stat  = $_POST['status'] === 'available' ? 'available' : 'unavailable';
    $conn->query("UPDATE Rooms SET Room_Type='$type',Room_Price=$price,Room_Capacity=$cap,Room_Description='$desc',Room_Status='$stat' WHERE Room_Id=$id");
    $message = "Room updated.";
}

// DELETE
if (isset($_POST['delete_room'])) {
    $id = (int) $_POST['room_id'];
    $conn->query("UPDATE Rooms SET Room_Status='unavailable' WHERE Room_Id=$id");
    $message = "Room marked unavailable.";
}

$hotels = $conn->query("SELECT Hotel_Id, Hotel_Name, Hotel_City FROM Hotels WHERE Hotel_Status='active' ORDER BY Hotel_Name");

$where  = $filterHotel ? "WHERE r.Room_HotelId=$filterHotel" : "";
$rooms  = $conn->query("SELECT r.*, h.Hotel_Name, h.Hotel_City FROM Rooms r JOIN Hotels h ON h.Hotel_Id=r.Room_HotelId $where ORDER BY h.Hotel_Name, r.Room_Price");

$title = "Manage Rooms";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:calc(100vh - 64px);">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:12px;">
            <div class="page-header" style="margin:0;">
                <h1>Rooms</h1>
                <p>Manage room types and pricing.</p>
            </div>
            <button class="btn-rd" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-lg me-1"></i>Add Room
            </button>
        </div>

        <?php if ($message): ?>
            <div class="alert-rd-success mb-4"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Filter -->
        <form method="GET" class="d-flex align-items-center gap-3 mb-4" style="max-width:400px;">
            <select name="hotel" class="form-select" onchange="this.form.submit()" style="font-size:14px;">
                <option value="">All Hotels</option>
                <?php
                $hotels->data_seek(0);
                while ($h = $hotels->fetch_assoc()):
                ?>
                <option value="<?= $h['Hotel_Id'] ?>" <?= $filterHotel == $h['Hotel_Id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($h['Hotel_Name']) ?>
                </option>
                <?php endwhile; ?>
            </select>
        </form>

        <!-- Table -->
        <div style="background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,0.07); overflow:hidden;">
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:14px;">
                    <thead>
                        <tr style="background:#FAFAFA; border-bottom:1px solid #EBEBEB;">
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px;">Hotel</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px;">Room Type</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px;">Price/Night</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px;">Capacity</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px;">Status</th>
                            <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($rooms->num_rows === 0): ?>
                        <tr><td colspan="6" style="padding:40px; text-align:center; color:#999;">No rooms found.</td></tr>
                    <?php else: while ($r = $rooms->fetch_assoc()): ?>
                    <tr style="border-bottom:1px solid #F8F8F8;">
                        <td style="padding:14px 16px; color:#555; font-size:13px;"><?= htmlspecialchars($r['Hotel_Name']) ?></td>
                        <td style="padding:14px 16px; font-weight:600;"><?= htmlspecialchars($r['Room_Type']) ?></td>
                        <td style="padding:14px 16px; color:#E8002D; font-weight:700;">₱<?= number_format($r['Room_Price']) ?></td>
                        <td style="padding:14px 16px; color:#555;"><?= $r['Room_Capacity'] ?> guest<?= $r['Room_Capacity'] > 1 ? 's' : '' ?></td>
                        <td style="padding:14px 16px;">
                            <span style="background:<?= $r['Room_Status']==='available' ? '#D1E7DD' : '#F8D7DA' ?>; color:<?= $r['Room_Status']==='available' ? '#0A3622' : '#842029' ?>; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">
                                <?= ucfirst($r['Room_Status']) ?>
                            </span>
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            <div style="display:flex; gap:6px; justify-content:center;">
                                <button class="btn-rd-outline" style="padding:5px 14px; font-size:12px;"
                                        onclick="openEdit(<?= htmlspecialchars(json_encode($r)) ?>)">
                                    Edit
                                </button>
                                <form method="POST" onsubmit="return confirm('Mark unavailable?');" style="margin:0;">
                                    <input type="hidden" name="room_id" value="<?= $r['Room_Id'] ?>">
                                    <button type="submit" name="delete_room"
                                            style="background:none; border:1px solid #DDD; color:#999; padding:5px 14px; font-size:12px; border-radius:8px; cursor:pointer; font-weight:600;">
                                        Disable
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Add Room</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            <?php $hotels->data_seek(0); while ($h = $hotels->fetch_assoc()): ?>
                            <option value="<?= $h['Hotel_Id'] ?>"><?= htmlspecialchars($h['Hotel_Name']) ?> — <?= $h['Hotel_City'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Room Type <span class="text-danger">*</span></label>
                            <input type="text" name="type" class="form-control" placeholder="e.g. Standard Room" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Price/Night (₱) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" min="1" step="0.01" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Capacity</label>
                            <input type="number" name="capacity" class="form-control" value="2" min="1" max="10">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn-rd-outline" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_room" class="btn-rd">Add Room</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Room</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <form method="POST">
                    <input type="hidden" name="room_id" id="edit_id">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Room Type <span class="text-danger">*</span></label>
                            <input type="text" name="type" id="edit_type" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Price/Night (₱) <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="edit_price" class="form-control" min="1" step="0.01" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Capacity</label>
                            <input type="number" name="capacity" id="edit_cap" class="form-control" min="1" max="10">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_desc" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn-rd-outline" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_room" class="btn-rd">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openEdit(r) {
    document.getElementById('edit_id').value     = r.Room_Id;
    document.getElementById('edit_type').value   = r.Room_Type;
    document.getElementById('edit_price').value  = r.Room_Price;
    document.getElementById('edit_cap').value    = r.Room_Capacity;
    document.getElementById('edit_desc').value   = r.Room_Description || '';
    document.getElementById('edit_status').value = r.Room_Status;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

<?php include "../layout/footer.php"; ?>
