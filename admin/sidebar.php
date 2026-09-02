<!-- Main Sidebar Container -->
<aside class="app-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <div class="sidebar-brand brand-link">
        <a href="index.php" class="brand-link text-decoration-none d-flex align-items-center gap-2 px-3 py-3">
            <span class="brand-text font-weight-bold text-white fs-4"><i class="bi bi-airplane-engines text-warning me-2"></i>SkyPort Admin</span>
        </a>
    </div>

    <!-- Sidebar Content -->
    <div class="sidebar-wrapper">
        <nav class="mt-3">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?= ($current_page == 'index.php') ? 'active' : ''; ?>">
                        <i class="nav-icon bi bi-speedometer2 text-info"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header text-uppercase text-white-50 small mt-3 px-3">Management</li>

                <li class="nav-item">
                    <a href="flights.php" class="nav-link <?= ($current_page == 'flights.php') ? 'active' : ''; ?>">
                        <i class="nav-icon bi bi-airplane text-warning"></i>
                        <p>Manage Flights</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="bookings.php" class="nav-link <?= ($current_page == 'bookings.php') ? 'active' : ''; ?>">
                        <i class="nav-icon bi bi-ticket-perforated text-success"></i>
                        <p>Manage Bookings</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="users.php" class="nav-link <?= ($current_page == 'users.php') ? 'active' : ''; ?>">
                        <i class="nav-icon bi bi-people text-primary"></i>
                        <p>Manage Users & Staff</p>
                    </a>
                </li>

                <li class="nav-header text-uppercase text-white-50 small mt-3 px-3">System</li>

                <li class="nav-item">
                    <a href="appearance.php" class="nav-link <?= ($current_page == 'appearance.php') ? 'active' : ''; ?>">
                        <i class="nav-icon bi bi-palette text-warning"></i>
                        <p>Appearance & Themes</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="settings.php" class="nav-link <?= ($current_page == 'settings.php') ? 'active' : ''; ?>">
                        <i class="nav-icon bi bi-gear text-secondary"></i>
                        <p>System & SMTP Settings</p>
                    </a>
                </li>

                <li class="nav-item mt-4 border-top border-secondary pt-3">
                    <a href="../index.php" class="nav-link" target="_blank">
                        <i class="nav-icon bi bi-box-arrow-up-right text-light"></i>
                        <p>Open Main Website</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="login.php?logout=1" class="nav-link text-danger">
                        <i class="nav-icon bi bi-power text-danger"></i>
                        <p>Logout</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
