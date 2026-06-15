<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit; }
require_once '../config/config.php';
$page_title = 'Pengaturan';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $conn->real_escape_string($_POST['nama']);
    $email = $conn->real_escape_string($_POST['email']);
    $id = $_SESSION['user_id'];
    $sql = "UPDATE pengguna SET nama='$nama', email='$email'";
    if (!empty($_POST['password_baru'])) {
        if (password_verify($_POST['password_lama'], $conn->query("SELECT password FROM pengguna WHERE id=$id")->fetch_assoc()['password'])) {
            $pass = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);
            $sql .= ", password='$pass'";
        } else {
            $msg = '<div class="alert alert-danger">Password lama tidak sesuai.</div>';
        }
    }
    if (!$msg) {
        $conn->query("$sql WHERE id=$id");
        $_SESSION['user_nama'] = $_POST['nama'];
        $msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Pengaturan berhasil disimpan.</div>';
    }
}

$user = $conn->query("SELECT * FROM pengguna WHERE id=" . $_SESSION['user_id'])->fetch_assoc();
include '../includes/header.php'; include '../includes/sidebar.php';
?>
<div class="main-wrapper">
    <div class="topbar">
        <div class="topbar-left"><button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button></div>
        <div class="topbar-right">
            <div class="topbar-notif"><i class="fas fa-bell"></i></div>
            <div class="topbar-user">
                <div class="topbar-avatar"><?php echo strtoupper(substr($_SESSION['user_nama'],0,1)); ?></div>
                <span class="topbar-username"><?php echo htmlspecialchars($_SESSION['user_nama']); ?></span>
            </div>
        </div>
    </div>
    <div class="main-content">
        <div class="page-header">
            <div class="breadcrumb"><a href="dashboard.php"><i class="fas fa-home"></i></a><span class="breadcrumb-sep">/</span><span>Pengaturan</span></div>
            <h1>Pengaturan Akun</h1>
            <p>Ubah informasi profil dan password</p>
        </div>
        <?php echo $msg; ?>
        <div class="card" style="max-width:560px;">
            <div class="card-header"><span class="card-title">Informasi Profil</span></div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group"><label>Nama</label><input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($user['nama']); ?>" required></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required></div>
                    <hr style="border-color:var(--border); margin:20px 0;">
                    <p style="font-size:14px; font-weight:600; margin-bottom:16px; color:var(--text-medium);">Ganti Password (opsional)</p>
                    <div class="form-group"><label>Password Lama</label><input type="password" name="password_lama" class="form-control"></div>
                    <div class="form-group"><label>Password Baru</label><input type="password" name="password_baru" class="form-control"></div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>
