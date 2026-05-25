<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'RedDoorz') ?> | RedDoorz</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DM Sans -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <!-- AOS scroll animations -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js" defer></script>

    <style>
        /* =============================================
           REDDOORZ DESIGN SYSTEM — DARKER RED EDITION
        ============================================= */
        :root {
            --rd-red:            #B80020;
            --rd-red-dark:       #880016;
            --rd-red-light:      #D4001F;
            --rd-red-pale:       #FFF0F0;
            --rd-white:          #FFFFFF;
            --rd-bg:             #F5F2F2;
            --rd-text:           #1A1A1A;
            --rd-muted:          #6B6868;
            --rd-border:         #E4DFDF;
            --rd-shadow:         0 2px 16px rgba(0,0,0,0.09);
            --rd-shadow-hover:   0 12px 32px rgba(184,0,32,0.14);
        }

        *, *::before, *::after { box-sizing: border-box; }

        html {
            overflow-x: hidden;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--rd-bg);
            color: var(--rd-text);
            margin: 0;
            padding-top: 64px;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100dvh;
        }

        a { text-decoration: none; }

        /* =============================================
           NAVBAR
        ============================================= */
        .navbar-rd {
            background: var(--rd-red);
            height: 64px;
            padding: 0;
        }

        .navbar-rd .navbar-brand {
            color: #fff !important;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.3px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .navbar-rd .navbar-brand .brand-icon {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 8px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .navbar-rd .nav-link {
            color: rgba(255,255,255,0.82) !important;
            font-size: 14px;
            font-weight: 500;
            padding: 7px 13px !important;
            border-radius: 6px;
            transition: all 0.18s;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .navbar-rd .nav-link:hover {
            color: #fff !important;
            background: rgba(255,255,255,0.13);
        }

        .btn-nav-outline {
            color: #fff;
            border: 1.5px solid rgba(255,255,255,0.52);
            background: transparent;
            padding: 6px 18px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.18s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-nav-outline:hover {
            border-color: #fff;
            background: rgba(255,255,255,0.11);
            color: #fff;
        }

        .btn-nav-solid {
            color: var(--rd-red);
            background: #fff;
            border: none;
            padding: 6px 18px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.18s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-nav-solid:hover {
            background: #f2ecec;
            color: var(--rd-red);
        }

        /* =============================================
           PRIMARY BUTTONS
        ============================================= */
        .btn-rd {
            background: var(--rd-red);
            color: #fff;
            border: none;
            padding: 10px 28px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            transition: background 0.18s, transform 0.12s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        .btn-rd:hover {
            background: var(--rd-red-dark);
            color: #fff;
        }

        .btn-rd:active { transform: scale(0.98); }

        .btn-rd-outline {
            background: transparent;
            color: var(--rd-red);
            border: 1.5px solid var(--rd-red);
            padding: 9px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.18s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        .btn-rd-outline:hover {
            background: var(--rd-red);
            color: #fff;
        }

        /* =============================================
           CARDS
        ============================================= */
        .card-rd {
            background: #fff;
            border: 1px solid rgba(228,223,223,0.6);
            border-radius: 14px;
            box-shadow: var(--rd-shadow);
            overflow: hidden;
            transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .card-rd:hover {
            transform: translateY(-4px);
            box-shadow: var(--rd-shadow-hover);
        }

        .card-form {
            background: #fff;
            border: 1px solid rgba(228,223,223,0.6);
            border-radius: 14px;
            box-shadow: var(--rd-shadow);
            padding: 36px 40px;
        }

        /* =============================================
           SIDEBAR
        ============================================= */
        .sidebar-rd {
            background: #fff;
            border-right: 1px solid var(--rd-border);
            position: sticky;
            top: 64px;
            height: calc(100vh - 64px);
            overflow-y: auto;
            padding: 28px 12px;
            width: 220px;
            flex-shrink: 0;
            align-self: flex-start;
        }

        .sidebar-section-title {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--rd-muted);
            padding: 0 12px;
            margin-bottom: 8px;
            margin-top: 20px;
        }

        .sidebar-section-title:first-child { margin-top: 0; }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: #444;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.15s;
            text-decoration: none;
        }

        .sidebar-link i { font-size: 16px; width: 20px; text-align: center; }

        .sidebar-link:hover {
            background: var(--rd-red-pale);
            color: var(--rd-red);
        }

        .sidebar-link.active {
            background: var(--rd-red-pale);
            color: var(--rd-red);
            font-weight: 600;
        }

        /* =============================================
           BADGES
        ============================================= */
        .badge-pending   { background:#FFF3CD; color:#856404; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .badge-confirmed { background:#D1E7DD; color:#0A3622; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .badge-cancelled { background:#F8D7DA; color:#842029; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .badge-completed { background:#CFE2FF; color:#084298; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .badge-checked-in { background:#D1FAE5; color:#065F46; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .badge-available  { background:#ECFDF5; color:#047857; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .badge-voided     { background:#F3F4F6; color:#6B7280; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }

        /* =============================================
           RATING STARS
        ============================================= */
        .stars { color: #C98A00; font-size: 13px; }

        /* =============================================
           FORMS
        ============================================= */
        .form-control, .form-select {
            border: 1.5px solid var(--rd-border);
            border-radius: 8px;
            font-size: 14px;
            padding: 10px 14px;
            font-family: 'DM Sans', sans-serif;
            background: #fff;
            transition: border-color 0.18s, box-shadow 0.18s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--rd-red);
            box-shadow: 0 0 0 3px rgba(184,0,32,0.1);
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #3a3a3a;
            margin-bottom: 6px;
        }

        /* =============================================
           TABLES
        ============================================= */
        .table-rd {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--rd-shadow);
        }

        .table-rd thead th {
            background: #FAFAFA;
            border-bottom: 1px solid var(--rd-border);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--rd-muted);
            padding: 14px 16px;
        }

        .table-rd tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #F5F3F3;
            font-size: 14px;
            vertical-align: middle;
        }

        .table-rd tbody tr:last-child td { border-bottom: none; }

        /* =============================================
           STAT CARDS
        ============================================= */
        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 24px;
            box-shadow: var(--rd-shadow);
            border: 1px solid rgba(228,223,223,0.5);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 700;
            line-height: 1;
        }

        .stat-card .stat-label {
            font-size: 13px;
            color: var(--rd-muted);
            margin-top: 4px;
        }

        /* =============================================
           ALERTS
        ============================================= */
        .alert-rd-danger {
            background: #FFF0F0;
            border: 1px solid #F5C6CB;
            color: #860018;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
        }

        .alert-rd-success {
            background: #F0FFF4;
            border: 1px solid #BBF7D0;
            color: #166534;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
        }

        /* =============================================
           PAGE HEADER
        ============================================= */
        .page-header { margin-bottom: 28px; }

        .page-header h1 {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 4px;
        }

        .page-header p {
            color: var(--rd-muted);
            font-size: 14px;
            margin: 0;
        }

        /* =============================================
           DIVIDER / MISC
        ============================================= */
        .divider { border: none; border-top: 1px solid var(--rd-border); margin: 20px 0; }

        .text-rd { color: var(--rd-red); }

        .hotel-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .price-tag {
            font-size: 20px;
            font-weight: 700;
            color: var(--rd-red);
        }

        .price-tag small {
            font-size: 13px;
            color: var(--rd-muted);
            font-weight: 400;
        }

        /* =============================================
           SECTION HEADINGS
        ============================================= */
        .section-label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--rd-red);
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.4px;
            line-height: 1.2;
            margin: 0 0 10px;
        }

        .section-subtitle {
            font-size: 15px;
            color: var(--rd-muted);
            margin: 0;
            line-height: 1.65;
        }

        /* =============================================
           CITY PILLS
        ============================================= */
        .city-pill {
            background: var(--rd-red-pale);
            color: var(--rd-red);
            border-radius: 20px;
            padding: 7px 18px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            border: 1px solid rgba(184,0,32,0.14);
            transition: all 0.18s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .city-pill:hover,
        .city-pill.active {
            background: var(--rd-red);
            color: #fff;
            border-color: var(--rd-red);
        }

        /* =============================================
           BREADCRUMB
        ============================================= */
        .breadcrumb-rd {
            font-size: 13px;
            color: var(--rd-muted);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .breadcrumb-rd a {
            color: var(--rd-muted);
            transition: color 0.15s;
        }

        .breadcrumb-rd a:hover { color: var(--rd-red); }

        /* =============================================
           FOOTER
        ============================================= */
        .footer-rd {
            background: #0E0C0C;
            color: #aaa;
            width: 100%;
            display: block;
            margin-top: auto;
        }

        /* =============================================
           RESPONSIVE
        ============================================= */
        @media (max-width: 768px) {
            .card-form { padding: 24px 20px; }
        }
    </style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar navbar-expand-lg navbar-rd fixed-top shadow-sm">
    <div class="container">

        <a class="navbar-brand" href="/index.php">
            <span class="brand-icon"><i class="bi bi-door-open-fill"></i></span>
            RedDoorz
        </a>

        <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#rdNav">
            <i class="bi bi-list fs-4 text-white"></i>
        </button>

        <div class="collapse navbar-collapse" id="rdNav">
            <ul class="navbar-nav me-auto ps-2">
                <?php if (($_SESSION['role'] ?? '') !== 'hotel_owner' && ($_SESSION['role'] ?? '') !== 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/hotels/search.php">
                        <i class="bi bi-search"></i> Find Hotels
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/index.php#featured">
                        <i class="bi bi-star"></i> Featured
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <?php if (isset($_SESSION['account_id'])): ?>
                    <div class="dropdown">
                        <button class="btn-nav-solid dropdown-toggle" data-bs-toggle="dropdown" style="cursor:pointer;">
                            <i class="bi bi-person-circle"></i>
                            <?= htmlspecialchars($_SESSION['display_name'] ?? 'User') ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2" style="min-width:200px;">
                            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                                <li>
                                    <a class="dropdown-item py-2" href="/admin/dashboard.php">
                                        <i class="bi bi-speedometer2 me-2" style="color:var(--rd-red)"></i>Admin Panel
                                    </a>
                                </li>
                            <?php elseif (($_SESSION['role'] ?? '') === 'hotel_owner'): ?>
                                <li>
                                    <a class="dropdown-item py-2" href="/owner/dashboard.php">
                                        <i class="bi bi-building me-2" style="color:var(--rd-red)"></i>Owner Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="/owner/manage_bookings.php">
                                        <i class="bi bi-calendar-check me-2" style="color:var(--rd-red)"></i>Bookings
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="/owner/earnings.php">
                                        <i class="bi bi-cash-coin me-2" style="color:var(--rd-red)"></i>Earnings
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <a class="dropdown-item py-2" href="/customer/dashboard.php">
                                        <i class="bi bi-calendar-check me-2" style="color:var(--rd-red)"></i>My Bookings
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="/customer/profile.php">
                                        <i class="bi bi-person me-2" style="color:var(--rd-red)"></i>My Profile
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item py-2 text-danger" href="/auth/logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="/auth/login.php" class="btn-nav-outline">Login</a>
                    <a href="/auth/register.php" class="btn-nav-solid">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</nav>
<!-- ===== /NAVBAR ===== -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof AOS !== 'undefined') {
        AOS.init({ once: true, duration: 660, easing: 'ease-out-cubic', offset: 60 });
    }
});
</script>
