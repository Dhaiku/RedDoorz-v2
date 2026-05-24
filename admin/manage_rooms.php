<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth/login.php"); exit();
}

$message   = "";
$filterHotel = (int) ($_GET['hotel'] ?? 0);

// ADMIN: status toggle override only
if (isset($_POST['toggle_status'])) {
    $id     = (int) $_POST['room_id'];
    $status = in_array($_POST['new_status'], ['available','unavailable','maintenance']) ? $_POST['new_status'] : 'available';
    $conn->query("UPDATE Rooms SET Room_Status='$status' WHERE Room_Id=$id");
    $message = "Room status updated.";
}

$hotels = $conn->query("SELECT Hotel_Id, Hotel_Name, Hotel_City FROM Hotels WHERE Hotel_Status='active' ORDER BY Hotel_Name");

$where  = $filterHotel ? "WHERE r.Room_HotelId=$filterHotel" : "";
$rooms  = $conn->query("SELECT r.*, h.Hotel_Name, h.Hotel_City FROM Rooms r JOIN Hotels h ON h.Hotel_Id=r.Room_HotelId $where ORDER BY h.Hotel_Name, r.Room_Price");

$title = "Manage Rooms";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:auto;">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <div class="page-header">
            <h1>Rooms</h1>
            <p>Read-only view of all rooms. Hotel owners manage room details; you can override status only.</p>
        </div>

        <?php if ($message): ?>
            <div class="alert-rd-success mb-4"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Info banner -->
        <div style="background:#EFF6FF; border:1px solid #BFDBFE; border-radius:8px; padding:12px 16px; font-size:13px; color:#1E40AF; margin-bottom:20px; display:flex; align-items:center; gap:9px;">
            <i class="bi bi-info-circle-fill" style="flex-shrink:0;"></i>
            Room types, pricing, and capacity are managed by hotel owners. Admin can override room status (e.g. disable a room platform-wide).
        </div>

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
                            <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:0.4px;">Override Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($rooms->num_rows === 0): ?>
                        <tr><td colspan="6" style="padding:40px; text-align:center; color:#999;">No rooms found.</td></tr>
                    <?php else: while ($r = $rooms->fetch_assoc()): ?>
                    <tr style="border-bottom:1px solid #F8F8F8;">
                        <td style="padding:14px 16px; color:#555; font-size:13px;"><?= htmlspecialchars($r['Hotel_Name']) ?></td>
                        <td style="padding:14px 16px; font-weight:600;"><?= htmlspecialchars($r['Room_Type']) ?></td>
                        <td style="padding:14px 16px; color:var(--rd-red); font-weight:700;">₱<?= number_format($r['Room_Price']) ?></td>
                        <td style="padding:14px 16px; color:#555;"><?= $r['Room_Capacity'] ?> guest<?= $r['Room_Capacity'] > 1 ? 's' : '' ?></td>
                        <td style="padding:14px 16px;">
                            <?php
                            $statusColor = match($r['Room_Status']) {
                                'available'   => ['#D1E7DD','#0A3622'],
                                'maintenance' => ['#FFF3CD','#856404'],
                                default       => ['#F8D7DA','#842029'],
                            };
                            ?>
                            <span style="background:<?= $statusColor[0] ?>; color:<?= $statusColor[1] ?>; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">
                                <?= ucfirst($r['Room_Status']) ?>
                            </span>
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            <form method="POST" style="display:flex; gap:6px; justify-content:center; align-items:center;">
                                <input type="hidden" name="room_id" value="<?= $r['Room_Id'] ?>">
                                <select name="new_status" class="form-select" style="font-size:12px; padding:4px 8px; width:140px;">
                                    <option value="available"   <?= $r['Room_Status']==='available'   ? 'selected' : '' ?>>Available</option>
                                    <option value="unavailable" <?= $r['Room_Status']==='unavailable' ? 'selected' : '' ?>>Unavailable</option>
                                    <option value="maintenance" <?= $r['Room_Status']==='maintenance' ? 'selected' : '' ?>>Maintenance</option>
                                </select>
                                <button type="submit" name="toggle_status"
                                        style="background:var(--rd-red); color:#fff; border:none; padding:5px 12px; font-size:12px; border-radius:6px; cursor:pointer; font-weight:600; white-space:nowrap;">
                                    Set
                                </button>
                            </form>
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
