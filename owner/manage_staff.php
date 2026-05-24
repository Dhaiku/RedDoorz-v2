<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'hotel_owner') {
    header("Location: /auth/login.php"); exit();
}

$hotelId = (int) ($_SESSION['hotel_id'] ?? 0);
$acctId  = (int) $_SESSION['account_id'];
if (!$hotelId) { header("Location: /auth/logout.php"); exit(); }

$hasTable = $conn->query("SHOW TABLES LIKE 'HotelStaff'")->num_rows > 0;
$msg = ''; $error = '';

// Add staff
if (isset($_POST['add_staff']) && $hasTable) {
    $name   = trim($conn->real_escape_string($_POST['staff_name']  ?? ''));
    $role   = trim($conn->real_escape_string($_POST['staff_role']  ?? 'front_desk'));
    $phone  = trim($conn->real_escape_string($_POST['staff_phone'] ?? ''));
    $email  = trim($conn->real_escape_string($_POST['staff_email'] ?? ''));
    if (!$name) {
        $error = 'Staff name is required.';
    } else {
        $conn->query("INSERT INTO HotelStaff (Staff_HotelId,Staff_OwnerId,Staff_Name,Staff_Role,Staff_Phone,Staff_Email) VALUES ($hotelId,$acctId,'$name','$role','$phone','$email')");
        $msg = 'Staff member added.';
    }
    if ($msg) { header("Location: manage_staff.php?msg=" . urlencode($msg)); exit(); }
}

// Toggle status
if (isset($_POST['toggle_staff']) && $hasTable) {
    $staffId   = (int) $_POST['staff_id'];
    $newStatus = $_POST['new_status'] === 'active' ? 'active' : 'inactive';
    $conn->query("UPDATE HotelStaff SET Staff_Status='$newStatus' WHERE Staff_Id=$staffId AND Staff_HotelId=$hotelId");
    header("Location: manage_staff.php?msg=Staff+updated."); exit();
}

// Delete staff
if (isset($_POST['delete_staff']) && $hasTable) {
    $staffId = (int) $_POST['staff_id'];
    $conn->query("DELETE FROM HotelStaff WHERE Staff_Id=$staffId AND Staff_HotelId=$hotelId");
    header("Location: manage_staff.php?msg=Staff+removed."); exit();
}

$staff = $hasTable ? $conn->query("SELECT * FROM HotelStaff WHERE Staff_HotelId=$hotelId ORDER BY Staff_Name") : null;

$title = "Manage Staff";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:auto;">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1>Staff</h1>
                <p>Manage front desk and hotel staff members.</p>
            </div>
            <button type="button" class="btn-rd" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                <i class="bi bi-plus-lg me-1"></i>Add Staff
            </button>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg']): ?>
        <div class="alert-rd-success mb-4" style="display:flex; align-items:center; gap:9px;">
            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($_GET['msg']) ?>
        </div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert-rd-danger mb-4" style="display:flex; align-items:center; gap:9px;">
            <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <?php if (!$hasTable): ?>
        <div style="background:#FFF8E1; border:1px solid #FFE082; border-radius:8px; padding:16px; color:#7B5800; font-size:13px;">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Staff management requires the database migration to be run first. Please run <code>config/migration_owner.sql</code>.
        </div>
        <?php elseif ($staff && $staff->num_rows === 0): ?>
        <div style="background:#fff; border-radius:14px; padding:60px 20px; text-align:center; box-shadow:var(--rd-shadow); border:1px solid rgba(228,223,223,0.5);">
            <div style="font-size:40px; color:var(--rd-red); margin-bottom:14px;"><i class="bi bi-people"></i></div>
            <h5 style="font-size:16px; font-weight:700; margin:0 0 8px;">No staff members yet</h5>
            <p style="font-size:13px; color:var(--rd-muted); margin:0 0 20px;">Add your front desk and hotel staff to manage your team.</p>
            <button type="button" class="btn-rd" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                <i class="bi bi-plus-lg me-1"></i>Add First Staff Member
            </button>
        </div>
        <?php else: ?>
        <div class="table-rd">
            <div style="overflow-x:auto;">
                <table class="table mb-0" style="font-size:14px;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($s = $staff->fetch_assoc()): ?>
                    <tr>
                        <td style="font-weight:600;"><?= htmlspecialchars($s['Staff_Name']) ?></td>
                        <td style="color:#555;"><?= htmlspecialchars(ucfirst(str_replace('_',' ',$s['Staff_Role']))) ?></td>
                        <td style="color:#555;"><?= htmlspecialchars($s['Staff_Phone'] ?: '—') ?></td>
                        <td style="color:#555;"><?= htmlspecialchars($s['Staff_Email'] ?: '—') ?></td>
                        <td>
                            <?php if ($s['Staff_Status'] === 'active'): ?>
                            <span class="badge-confirmed">Active</span>
                            <?php else: ?>
                            <span class="badge-voided">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                <form method="POST" style="margin:0;">
                                    <input type="hidden" name="staff_id" value="<?= $s['Staff_Id'] ?>">
                                    <input type="hidden" name="new_status" value="<?= $s['Staff_Status'] === 'active' ? 'inactive' : 'active' ?>">
                                    <button type="submit" name="toggle_staff" class="btn-rd-outline" style="font-size:12px; padding:4px 12px;">
                                        <?= $s['Staff_Status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </form>
                                <form method="POST" style="margin:0;" onsubmit="return confirm('Remove this staff member?');">
                                    <input type="hidden" name="staff_id" value="<?= $s['Staff_Id'] ?>">
                                    <button type="submit" name="delete_staff" style="font-size:12px; background:#FEF2F2; color:#B91C1C; border:1px solid #FECACA; border-radius:6px; padding:4px 12px; cursor:pointer; font-weight:600; font-family:'DM Sans',sans-serif;">
                                        Remove
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
        <?php endif; ?>

    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none;">
            <div class="modal-header" style="border-bottom:1px solid var(--rd-border); padding:20px 24px;">
                <h5 class="modal-title" style="font-size:16px; font-weight:700;">Add Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body" style="padding:24px;">
                    <div class="mb-3">
                        <label class="form-label">Full Name <span style="color:var(--rd-red)">*</span></label>
                        <input type="text" name="staff_name" class="form-control" placeholder="e.g. Maria Santos" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="staff_role" class="form-select">
                            <option value="front_desk">Front Desk</option>
                            <option value="housekeeping">Housekeeping</option>
                            <option value="security">Security</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="manager">Manager</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="staff_phone" class="form-control" placeholder="0917-xxx-xxxx">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="staff_email" class="form-control" placeholder="staff@hotel.com">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--rd-border); padding:16px 24px;">
                    <button type="button" class="btn-rd-outline" data-bs-dismiss="modal" style="padding:9px 22px;">Cancel</button>
                    <button type="submit" name="add_staff" class="btn-rd" style="padding:9px 22px;">Add Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "../layout/footer.php"; ?>
