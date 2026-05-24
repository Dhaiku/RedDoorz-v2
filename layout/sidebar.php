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

        <a href="/admin/manage_customers.php"
           class="sidebar-link <?= $currentPage === 'manage_customers.php' ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Customers
        </a>

        <a href="/admin/manage_owners.php"
           class="sidebar-link <?= $currentPage === 'manage_owners.php' ? 'active' : '' ?>">
            <i class="bi bi-person-badge"></i> Hotel Owners
        </a>

        <a href="/admin/manage_payouts.php"
           class="sidebar-link <?= $currentPage === 'manage_payouts.php' ? 'active' : '' ?>">
            <i class="bi bi-cash-stack"></i> Payouts
        </a>

        <hr class="divider">

        <a href="/hotels/search.php" class="sidebar-link">
            <i class="bi bi-search"></i> View Site
        </a>

    <?php elseif ($role === 'hotel_owner'): ?>

        <div class="sidebar-section-title">Owner Panel</div>

        <a href="/owner/dashboard.php"
           class="sidebar-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a href="/owner/hotel_profile.php"
           class="sidebar-link <?= $currentPage === 'hotel_profile.php' ? 'active' : '' ?>">
            <i class="bi bi-building-gear"></i> Hotel Profile
        </a>

        <a href="/owner/manage_bookings.php"
           class="sidebar-link <?= $currentPage === 'manage_bookings.php' ? 'active' : '' ?>">
            <i class="bi bi-calendar-check"></i> Bookings
        </a>

        <a href="/owner/manage_rooms.php"
           class="sidebar-link <?= $currentPage === 'manage_rooms.php' ? 'active' : '' ?>">
            <i class="bi bi-door-closed"></i> Rooms
        </a>

        <a href="/owner/manage_staff.php"
           class="sidebar-link <?= $currentPage === 'manage_staff.php' ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Staff
        </a>

        <a href="/owner/earnings.php"
           class="sidebar-link <?= $currentPage === 'earnings.php' ? 'active' : '' ?>">
            <i class="bi bi-cash-coin"></i> Earnings
        </a>

        <hr class="divider">

        <a href="/auth/logout.php" class="sidebar-link" style="color:var(--rd-red);">
            <i class="bi bi-box-arrow-right"></i> Logout
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
