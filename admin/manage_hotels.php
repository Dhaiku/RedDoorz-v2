<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth/login.php"); exit();
}

$message = "";

// DEACTIVATE
if (isset($_POST['delete_hotel'])) {
    $id = (int) $_POST['hotel_id'];
    fs_update('hotels', $id, ['status' => 'inactive']);
    $message = "Hotel deactivated.";
}

$hotels = fs_all('hotels', [['createdAt', 'DESC']]);

// Enrich with room count and owner email
foreach ($hotels as &$h) {
    $h['roomCount']  = fs_count('rooms', [['hotelId', '=', $h['id']]]);
    $ownerAcct = !empty($h['ownerId']) ? fs_get('accounts', (int)$h['ownerId']) : null;
    $h['ownerEmail'] = $ownerAcct['email'] ?? '';
}
unset($h);

$title = "Manage Hotels";
include "../layout/layout.php";
?>

<div style="display:flex; min-height:auto;">
    <?php include "../layout/sidebar.php"; ?>

    <div style="flex:1; padding:36px 32px; overflow:visible;">

        <div class="page-header">
            <h1>Hotels</h1>
            <p>Manage all hotel listings.</p>
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
                    <?php foreach ($hotels as $h): ?>
                    <tr style="border-bottom:1px solid #F8F8F8;">
                        <td style="padding:14px 16px; font-weight:600;"><?= htmlspecialchars($h['name']) ?></td>
                        <td style="padding:14px 16px; color:#555;"><?= htmlspecialchars($h['city']) ?></td>
                        <td style="padding:14px 16px; color:#555; font-size:13px;">
                            <?php if ($h['ownerEmail']): ?>
                            <span style="display:inline-flex; align-items:center; gap:5px;">
                                <i class="bi bi-person-badge" style="color:var(--rd-red);"></i>
                                <?= htmlspecialchars($h['ownerEmail']) ?>
                            </span>
                            <?php else: ?>
                            <span style="color:#ccc; font-size:12px;">Platform</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:14px 16px;">
                            <span class="stars" style="font-size:12px;">
                                <?php for ($i=1;$i<=5;$i++) echo '<i class="bi bi-star' . ($i <= round($h['rating'] ?? 0) ? '-fill' : '') . '"></i>'; ?>
                            </span>
                            <span style="font-size:12px; color:#999; margin-left:3px;"><?= $h['rating'] ?? 0 ?></span>
                        </td>
                        <td style="padding:14px 16px; color:#555;"><?= $h['roomCount'] ?></td>
                        <td style="padding:14px 16px;">
                            <span style="background:<?= ($h['status'] ?? '') === 'active' ? '#D1E7DD' : '#F8D7DA' ?>; color:<?= ($h['status'] ?? '') === 'active' ? '#0A3622' : '#842029' ?>; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">
                                <?= ucfirst($h['status'] ?? '') ?>
                            </span>
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            <form method="POST" onsubmit="return confirm('Deactivate this hotel?');" style="margin:0;">
                                <input type="hidden" name="hotel_id" value="<?= $h['id'] ?>">
                                <button type="submit" name="delete_hotel"
                                        style="background:none; border:1px solid #DDD; color:#999; padding:5px 14px; font-size:12px; border-radius:8px; cursor:pointer; font-weight:600;">
                                    Deactivate
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
