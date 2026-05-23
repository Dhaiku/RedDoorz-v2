<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? '';
?>
<div class="sidebar-rd d-none d-md-block">

    <?php if ($role === 'admin'): ?>

        <div class="sidebar-section-title">Admin Panel</div>

        <a href="/admin/dashboard.php"
           class="sidebar-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a href="/admin/manage_hotels.php"
           class="sidebar-link <?= $currentPage === 'manage_hotels.php' ? 'active' : '' ?>">
            <i class="bi bi-building"></i> Hotels
        </a>

        <a href="/admin/manage_rooms.php"
           class="sidebar-link <?= $currentPage === 'manage_rooms.php' ? 'active' : '' ?>">
            <i class="bi bi-door-closed"></i> Rooms
        </a>

        <a href="/admin/manage_bookings.php"
           class="sidebar-link <?= $currentPage === 'manage_bookings.php' ? 'active' : '' ?>">
            <i class="bi bi-calendar-check"></i> Bookings
        </a>

        <a href="/admin/manage_customers.php"
           class="sidebar-link <?= $currentPage === 'manage_customers.php' ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Customers
        </a>

        <hr class="divider">

        <a href="/hotels/search.php" class="sidebar-link">
            <i class="bi bi-search"></i> View Site
        </a>

    <?php else: ?>

        <div class="sidebar-section-title">My Account</div>

        <a href="/customer/dashboard.php"
           class="sidebar-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
            <i class="bi bi-calendar-check"></i> My Bookings
        </a>

        <a href="/customer/profile.php"
           class="sidebar-link <?= $currentPage === 'profile.php' ? 'active' : '' ?>">
            <i class="bi bi-person"></i> My Profile
        </a>

        <hr class="divider">

        <a href="/hotels/search.php" class="sidebar-link">
            <i class="bi bi-search"></i> Find Hotels
        </a>

        <a href="/auth/logout.php" class="sidebar-link" style="color:var(--rd-red);">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>

    <?php endif; ?>

</div>
