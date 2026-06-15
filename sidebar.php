$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">
            <i class="fas fa-mountain"></i>
        </div>
        <div class="logo-text">
            <span class="logo-title">RINJANI</span>
            <span class="logo-sub">INTI KARYA SOLUSI</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-title">MENU UTAMA</div>

        <a href="<?php echo APP_URL; ?>/admin/dashboard.php"
           class="nav-item <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>

        <a href="<?php echo APP_URL; ?>/admin/proyek.php"
           class="nav-item <?php echo $current_page == 'proyek.php' ? 'active' : ''; ?>">
            <i class="fas fa-folder-open"></i>
            <span>Proyek</span>
        </a>

        <a href="<?php echo APP_URL; ?>/admin/jadwal.php"
           class="nav-item <?php echo $current_page == 'jadwal.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-alt"></i>
            <span>Jadwal</span>
        </a>

        <a href="<?php echo APP_URL; ?>/admin/pekerja.php"
           class="nav-item <?php echo $current_page == 'pekerja.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i>
            <span>Pekerja</span>
        </a>

        <a href="<?php echo APP_URL; ?>/admin/subkontraktor.php"
           class="nav-item <?php echo $current_page == 'subkontraktor.php' ? 'active' : ''; ?>">
            <i class="fas fa-handshake"></i>
            <span>Subkontraktor</span>
        </a>

        <a href="<?php echo APP_URL; ?>/admin/galeri.php"
           class="nav-item <?php echo $current_page == 'galeri.php' ? 'active' : ''; ?>">
            <i class="fas fa-images"></i>
            <span>Galeri</span>
        </a>

        <a href="<?php echo APP_URL; ?>/admin/pengguna.php"
           class="nav-item <?php echo $current_page == 'pengguna.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-cog"></i>
            <span>Pengguna</span>
        </a>

        <a href="<?php echo APP_URL; ?>/admin/pengaturan.php"
           class="nav-item <?php echo $current_page == 'pengaturan.php' ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i>
            <span>Pengaturan</span>
        </a>

        <div class="nav-section-title" style="margin-top:16px;">LAINNYA</div>

        <a href="<?php echo APP_URL; ?>/auth/logout.php" class="nav-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </nav>
</div>
<?php
