<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth/login.php"); exit();
}

$message = "";

// ADD
if (isset($_POST['add_hotel'])) {
    $name  = $conn->real_escape_string(trim($_POST['name']));
    $city  = $conn->real_escape_string(trim($_POST['city']));
    $addr  = $conn->real_escape_string(trim($_POST['address']));
    $desc  = $conn->real_escape_string(trim($_POST['description']));
    $rat   = (float) $_POST['rating'];
    $rat   = max(1.0, min(5.0, $rat));
    $conn->query("INSERT INTO Hotels (Hotel_Name,Hotel_City,Hotel_Address,Hotel_Description,Hotel_Rating) VALUES ('$name','$city','$addr','$desc',$rat)");
    $message = "Hotel added successfully.";
}

// EDIT
if (isset($_POST['edit_hotel'])) {
    $id    = (int) $_POST['hotel_id'];
    $name  = $conn->real_escape_string(trim($_POST['name']));
    $city  = $conn->real_escape_string(trim($_POST['city']));
    $addr  = $conn->real_escape_string(trim($_POST['address']));
    $desc  = $conn->real_escape_string(trim($_POST['description']));
    $rat   = max(1.0, min(5.0, (float) $_POST['rating']));
    $stat  = $_POST['status'] === 'active' ? 'active' : 'inactive';
    $conn->query("UPDATE Hotels SET Hotel_Name='$name',Hotel_City='$city',Hotel_Address='$addr',Hotel_Description='$desc',Hotel_Rating=$rat,Hotel_Status='$stat' WHERE Hotel_Id=$id");
    $message = "Hotel updated successfully.";
}

// DELETE
if (isset($_POST['delete_hotel'])) {
    $id = (int) $_POST['hotel_id'];
    $conn->query("UPDATE Hotels SET Hotel_Status='inactive' WHERE Hotel_Id=$id");
    $message = "Hotel deactivated.";
}

$hotels = $conn->query("
    SELECT h.*, COUNT(r.Room_Id) AS RoomCount,
           a.Acct_Email AS Owner_Email
    FROM Hotels h
    LEFT JOIN Rooms    r ON r.Room_HotelId   = h.Hotel_Id
    LEFT JOIN Accounts a ON a.Acct_Id        = h.Hotel_OwnerId AND a.Acct_Role = 'hotel_owner'
    GROUP BY h.Hotel_Id
    ORDER BY h.Hotel_CreatedAt DESC
");

$title = "Manage Hotels";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:auto;">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:12px;">
            <div class="page-header" style="margin:0;">
                <h1>Hotels</h1>
                <p>Manage all hotel listings.</p>
            </div>
            <button class="btn-rd" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-lg me-1"></i>Add Hotel
            </button>
        </div>

        <?php if ($message): ?>
            <div class="alert-rd-success mb-4"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Table -->
        <div style="background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,0.07); overflow:hidden;">
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:14px;">
                    <thead>
                        <tr style="background:#FAFAFA; border-bottom:1px solid #EBEBEB;">
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px;">Hotel Name</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px;">City</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px;">Owner</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px;">Rating</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px;">Rooms</th>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px;">Status</th>
                            <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($h = $hotels->fetch_assoc()): ?>
                    <tr style="border-bottom:1px solid #F8F8F8;">
                        <td style="padding:14px 16px; font-weight:600;"><?= htmlspecialchars($h['Hotel_Name']) ?></td>
                        <td style="padding:14px 16px; color:#555;"><?= htmlspecialchars($h['Hotel_City']) ?></td>
                        <td style="padding:14px 16px; color:#555; font-size:13px;">
                            <?php if ($h['Owner_Email']): ?>
                            <span style="display:inline-flex; align-items:center; gap:5px;">
                                <i class="bi bi-person-badge" style="color:var(--rd-red);"></i>
                                <?= htmlspecialchars($h['Owner_Email']) ?>
                            </span>
                            <?php else: ?>
                            <span style="color:#ccc; font-size:12px;">Platform</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:14px 16px;">
                            <span class="stars" style="font-size:12px;">
                                <?php for ($i=1;$i<=5;$i++) echo '<i class="bi bi-star' . ($i <= round($h['Hotel_Rating']) ? '-fill' : '') . '"></i>'; ?>
                            </span>
                            <span style="font-size:12px; color:#999; margin-left:3px;"><?= $h['Hotel_Rating'] ?></span>
                        </td>
                        <td style="padding:14px 16px; color:#555;"><?= $h['RoomCount'] ?></td>
                        <td style="padding:14px 16px;">
                            <span style="background:<?= $h['Hotel_Status']==='active' ? '#D1E7DD' : '#F8D7DA' ?>; color:<?= $h['Hotel_Status']==='active' ? '#0A3622' : '#842029' ?>; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">
                                <?= ucfirst($h['Hotel_Status']) ?>
                            </span>
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            <div style="display:flex; gap:6px; justify-content:center;">
                                <button class="btn-rd-outline" style="padding:5px 14px; font-size:12px;"
                                        onclick="openEdit(<?= htmlspecialchars(json_encode($h)) ?>)">
                                    Edit
                                </button>
                                <form method="POST" onsubmit="return confirm('Deactivate this hotel?');" style="margin:0;">
                                    <input type="hidden" name="hotel_id" value="<?= $h['Hotel_Id'] ?>">
                                    <button type="submit" name="delete_hotel"
                                            style="background:none; border:1px solid #DDD; color:#999; padding:5px 14px; font-size:12px; border-radius:8px; cursor:pointer; font-weight:600;">
                                        Deactivate
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Add New Hotel</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Hotel Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Rating (1–5)</label>
                            <input type="number" name="rating" class="form-control" min="1" max="5" step="0.1" value="4.0" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn-rd-outline" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_hotel" class="btn-rd">Add Hotel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Hotel</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <form method="POST" id="editForm">
                    <input type="hidden" name="hotel_id" id="edit_id">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Hotel Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" id="edit_city" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" id="edit_address" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_desc" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Rating</label>
                            <input type="number" name="rating" id="edit_rating" class="form-control" min="1" max="5" step="0.1" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn-rd-outline" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_hotel" class="btn-rd">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openEdit(h) {
    document.getElementById('edit_id').value      = h.Hotel_Id;
    document.getElementById('edit_name').value    = h.Hotel_Name;
    document.getElementById('edit_city').value    = h.Hotel_City;
    document.getElementById('edit_address').value = h.Hotel_Address || '';
    document.getElementById('edit_desc').value    = h.Hotel_Description || '';
    document.getElementById('edit_rating').value  = h.Hotel_Rating;
    document.getElementById('edit_status').value  = h.Hotel_Status;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

<?php include "../layout/footer.php"; ?>
