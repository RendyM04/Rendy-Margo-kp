<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
require_once '../config/config.php';
$page_title = 'Dashboard';

// Stats
$total_proyek = $conn->query("SELECT COUNT(*) as c FROM proyek")->fetch_assoc()['c'];
$jadwal_aktif = $conn->query("SELECT COUNT(*) as c FROM jadwal WHERE status='Aktif'")->fetch_assoc()['c'];
$total_pekerja = $conn->query("SELECT COUNT(*) as c FROM pekerja")->fetch_assoc()['c'];
$total_subkon  = $conn->query("SELECT COUNT(*) as c FROM subkontraktor")->fetch_assoc()['c'];

// Proyek chart data
$berjalan = $conn->query("SELECT COUNT(*) as c FROM proyek WHERE status='Berjalan'")->fetch_assoc()['c'];
$selesai  = $conn->query("SELECT COUNT(*) as c FROM proyek WHERE status='Selesai'")->fetch_assoc()['c'];
$tertunda = $conn->query("SELECT COUNT(*) as c FROM proyek WHERE status='Tertunda'")->fetch_assoc()['c'];

// Proyek terbaru
$proyek_terbaru = $conn->query("SELECT * FROM proyek ORDER BY created_at DESC LIMIT 5");

// Jadwal hari ini
$today = date('Y-m-d');
$jadwal_hari_ini = $conn->query("SELECT * FROM jadwal WHERE tanggal = '$today' ORDER BY waktu ASC LIMIT 5");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-wrapper">
    <div class="topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        </div>
        <div class="topbar-right">
            <div class="topbar-notif"><i class="fas fa-bell"></i></div>
            <div class="topbar-user">
                <div class="topbar-avatar"><?php echo strtoupper(substr($_SESSION['user_nama'], 0, 1)); ?></div>
                <span class="topbar-username"><?php echo htmlspecialchars($_SESSION['user_nama']); ?></span>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="page-header">
            <div class="breadcrumb">
                <i class="fas fa-home"></i>
                <span class="breadcrumb-sep">/</span>
                <span>Dashboard</span>
            </div>
            <h1>Dashboard</h1>
            <p>Selamat datang kembali, <?php echo htmlspecialchars($_SESSION['user_nama']); ?></p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-label">Total Proyek</div>
                        <div class="stat-value"><?php echo $total_proyek; ?></div>
                    </div>
                    <div class="stat-icon blue"><i class="fas fa-folder-open"></i></div>
                </div>
                <a href="proyek.php" class="stat-link">Lihat detail <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="stat-card">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-label">Jadwal Aktif</div>
                        <div class="stat-value"><?php echo $jadwal_aktif; ?></div>
                    </div>
                    <div class="stat-icon green"><i class="fas fa-calendar-check"></i></div>
                </div>
                <a href="jadwal.php" class="stat-link">Lihat detail <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="stat-card">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-label">Total Pekerja</div>
                        <div class="stat-value"><?php echo $total_pekerja; ?></div>
                    </div>
                    <div class="stat-icon purple"><i class="fas fa-users"></i></div>
                </div>
                <a href="pekerja.php" class="stat-link">Lihat detail <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="stat-card">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-label">Subkontraktor</div>
                        <div class="stat-value"><?php echo $total_subkon; ?></div>
                    </div>
                    <div class="stat-icon orange"><i class="fas fa-handshake"></i></div>
                </div>
                <a href="subkontraktor.php" class="stat-link">Lihat detail <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Proyek Terbaru -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Proyek Terbaru</span>
                </div>
                <div class="card-body">
                    <?php while ($p = $proyek_terbaru->fetch_assoc()): ?>
                    <div class="project-item">
                        <div class="project-thumb">
                            <?php if ($p['foto']): ?>
                                <img src="<?php echo APP_URL . '/uploads/proyek/' . htmlspecialchars($p['foto']); ?>" alt="">
                            <?php else: ?>
                                <i class="fas fa-hard-hat"></i>
                            <?php endif; ?>
                        </div>
                        <div class="project-info">
                            <div class="project-name"><?php echo htmlspecialchars($p['nama_proyek']); ?></div>
                            <div class="project-date"><?php echo date('d M Y', strtotime($p['tanggal_mulai'])); ?></div>
                        </div>
                        <?php
                        $badge = ['Berjalan' => 'primary', 'Selesai' => 'success', 'Tertunda' => 'warning'];
                        $b = $badge[$p['status']] ?? 'secondary';
                        ?>
                        <span class="badge badge-<?php echo $b; ?>"><?php echo $p['status']; ?></span>
                    </div>
                    <?php endwhile; ?>
                    <a href="proyek.php" style="display:block; text-align:center; margin-top:16px; color:var(--primary); font-size:13px; font-weight:600; text-decoration:none;">
                        Lihat semua proyek <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Grafik Proyek -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Grafik Proyek</span>
                    <span style="font-size:12px; color:var(--text-light);">Semua Waktu</span>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="proyekChart" width="200" height="200"
                            data-berjalan="<?php echo $berjalan; ?>"
                            data-selesai="<?php echo $selesai; ?>"
                            data-tertunda="<?php echo $tertunda; ?>">
                        </canvas>
                    </div>
                    <div class="chart-legend">
                        <?php $total_all = $total_proyek ?: 1; ?>
                        <div class="legend-item">
                            <div class="legend-label">
                                <div class="legend-dot" style="background:#2563eb;"></div>
                                Berjalan
                            </div>
                            <span class="legend-pct"><?php echo round($berjalan / $total_all * 100); ?>%</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-label">
                                <div class="legend-dot" style="background:#10b981;"></div>
                                Selesai
                            </div>
                            <span class="legend-pct"><?php echo round($selesai / $total_all * 100); ?>%</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-label">
                                <div class="legend-dot" style="background:#ef4444;"></div>
                                Tertunda
                            </div>
                            <span class="legend-pct"><?php echo round($tertunda / $total_all * 100); ?>%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jadwal Hari Ini -->
            <div class="card" style="grid-column: 1 / -1;">
                <div class="card-header">
                    <span class="card-title">Jadwal Hari Ini</span>
                    <span style="font-size:12px; color:var(--text-light);"><?php echo date('d F Y'); ?></span>
                </div>
                <div class="card-body" style="padding-top:0;">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Kegiatan</th>
                                    <th>Lokasi</th>
                                    <th>Penanggung Jawab</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $rows = $jadwal_hari_ini->fetch_all(MYSQLI_ASSOC);
                            if (count($rows) > 0):
                                foreach ($rows as $j):
                            ?>
                                <tr>
                                    <td><?php echo date('H:i', strtotime($j['waktu'])); ?></td>
                                    <td><?php echo htmlspecialchars($j['kegiatan']); ?></td>
                                    <td><?php echo htmlspecialchars($j['lokasi']); ?></td>
                                    <td><?php echo htmlspecialchars($j['penanggung_jawab']); ?></td>
                                </tr>
                            <?php
                                endforeach;
                            else:
                            ?>
                                <tr><td colspan="4" style="text-align:center; color:var(--text-light); padding:20px;">Tidak ada jadwal hari ini</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="jadwal.php" style="display:block; text-align:center; margin-top:16px; color:var(--primary); font-size:13px; font-weight:600; text-decoration:none;">
                        Lihat semua jadwal <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</div>
