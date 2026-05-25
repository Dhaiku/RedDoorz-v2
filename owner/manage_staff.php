<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'hotel_owner') {
    header("Location: /auth/login.php"); exit();
}

$hotelId = (int) ($_SESSION['hotel_id'] ?? 0);
$acctId  = (int) $_SESSION['account_id'];
if (!$hotelId) { header("Location: /auth/logout.php"); exit(); }

$msg = ''; $error = '';

// Add staff
if (isset($_POST['add_staff'])) {
    $fname  = trim($_POST['staff_fname']  ?? '');
    $lname  = trim($_POST['staff_lname']  ?? '');
    $mi     = trim($_POST['staff_mi']     ?? '');
    $role   = trim($_POST['staff_role']   ?? 'front_desk');
    $phone  = trim($_POST['staff_phone']  ?? '');
    $email  = trim($_POST['staff_email']  ?? '');

    $fullName    = trim($fname . ' ' . $lname . ($mi ? ' ' . rtrim($mi, '.') . '.' : ''));
    $phoneDigits = preg_replace('/\D/', '', $phone);

    if (!$fname || !$lname) {
        $error = 'First name and last name are required.';
    } elseif ($email !== '' && (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/\.com$/i', $email))) {
        $error = 'Please enter a valid email address ending in .com (e.g. staff@hotel.com).';
    } elseif ($phone !== '' && (strlen($phoneDigits) !== 11 || substr($phoneDigits, 0, 2) !== '09')) {
        $error = 'Phone number must be a valid 11-digit PH number starting with 09 (e.g. 0917-123-4567).';
    } else {
        if ($phoneDigits) {
            $phone = substr($phoneDigits,0,4).'-'.substr($phoneDigits,4,3).'-'.substr($phoneDigits,7,4);
        }
        fs_insert('hotelstaff', [
            'hotelId' => $hotelId,
            'ownerId' => $acctId,
            'name'    => $fullName,
            'role'    => $role,
            'phone'   => $phone,
            'email'   => $email,
            'status'  => 'active',
        ]);
        $msg = 'Staff member added.';
    }
    if ($msg) { header("Location: manage_staff.php?msg=" . urlencode($msg)); exit(); }
}

// Toggle status
if (isset($_POST['toggle_staff'])) {
    $staffId   = (int) $_POST['staff_id'];
    $newStatus = $_POST['new_status'] === 'active' ? 'active' : 'inactive';
    $check = fs_find('hotelstaff', [['id', '=', $staffId], ['hotelId', '=', $hotelId]]);
    if ($check) {
        fs_update('hotelstaff', $staffId, ['status' => $newStatus]);
    }
    header("Location: manage_staff.php?msg=Staff+updated."); exit();
}

// Delete staff
if (isset($_POST['delete_staff'])) {
    $staffId = (int) $_POST['staff_id'];
    $check = fs_find('hotelstaff', [['id', '=', $staffId], ['hotelId', '=', $hotelId]]);
    if ($check) {
        fs_delete('hotelstaff', $staffId);
    }
    header("Location: manage_staff.php?msg=Staff+removed."); exit();
}

$staff = fs_query('hotelstaff', [['hotelId', '=', $hotelId]], [['name', 'ASC']]);

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

        <?php if (empty($staff)): ?>
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
                    <?php foreach ($staff as $s): ?>
                    <tr>
                        <td style="font-weight:600;"><?= htmlspecialchars($s['name']) ?></td>
                        <td style="color:#555;"><?= htmlspecialchars(ucfirst(str_replace('_',' ',$s['role']))) ?></td>
                        <td style="color:#555;"><?= htmlspecialchars($s['phone'] ?: '—') ?></td>
                        <td style="color:#555;"><?= htmlspecialchars($s['email'] ?: '—') ?></td>
                        <td>
                            <?php if ($s['status'] === 'active'): ?>
                            <span class="badge-confirmed">Active</span>
                            <?php else: ?>
                            <span class="badge-voided">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                <form method="POST" style="margin:0;">
                                    <input type="hidden" name="staff_id" value="<?= $s['id'] ?>">
                                    <input type="hidden" name="new_status" value="<?= $s['status'] === 'active' ? 'inactive' : 'active' ?>">
                                    <button type="submit" name="toggle_staff" class="btn-rd-outline" style="font-size:12px; padding:4px 12px;">
                                        <?= $s['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </form>
                                <form method="POST" style="margin:0;" onsubmit="return confirm('Remove this staff member?');">
                                    <input type="hidden" name="staff_id" value="<?= $s['id'] ?>">
                                    <button type="submit" name="delete_staff" style="font-size:12px; background:#FEF2F2; color:#B91C1C; border:1px solid #FECACA; border-radius:6px; padding:4px 12px; cursor:pointer; font-weight:600; font-family:'DM Sans',sans-serif;">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
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
            <form method="POST" novalidate>
                <div class="modal-body" style="padding:24px;">
                    <div class="row g-3 mb-3">
                        <div class="col-5">
                            <label class="form-label">First Name <span style="color:var(--rd-red)">*</span></label>
                            <input type="text" name="staff_fname" class="form-control" placeholder="Maria" required>
                        </div>
                        <div class="col-5">
                            <label class="form-label">Last Name <span style="color:var(--rd-red)">*</span></label>
                            <input type="text" name="staff_lname" class="form-control" placeholder="Santos" required>
                        </div>
                        <div class="col-2">
                            <label class="form-label">M.I.</label>
                            <input type="text" name="staff_mi" class="form-control" placeholder="A." maxlength="3">
                        </div>
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
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="staff_phone" id="staff_phone" class="form-control"
                               placeholder="0917-123-4567" maxlength="13"
                               oninput="formatStaffPhone(this)">
                        <div style="font-size:11px; color:#aaa; margin-top:4px;">Format: xxxx-xxx-xxxx &bull; Must start with 09</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="staff_email" class="form-control"
                               placeholder="staff@hotel.com"
                               pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.com"
                               title="Email must end in .com (e.g. staff@hotel.com)">
                        <div style="font-size:11px; color:#aaa; margin-top:4px;">Must end in .com if provided</div>
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

<script>
function formatStaffPhone(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 11);
    if (v.length > 7)      v = v.substring(0,4) + '-' + v.substring(4,7) + '-' + v.substring(7);
    else if (v.length > 4) v = v.substring(0,4) + '-' + v.substring(4);
    input.value = v;
}
</script>

<?php include "../layout/footer.php"; ?>
