<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit; }
require_once '../config/config.php';
$page_title = 'Galeri';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'tambah') {
        $judul = $conn->real_escape_string($_POST['judul']);
        $proyek_id = $_POST['proyek_id'] ?: 'NULL';
        $foto = '';
        if (!empty($_FILES['foto']['name'])) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], UPLOAD_PATH . 'galeri/' . $foto);
            $conn->query("INSERT INTO galeri (judul, foto, proyek_id) VALUES ('$judul','$foto',$proyek_id)");
            $msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Foto berhasil ditambahkan.</div>';
        } else {
            $msg = '<div class="alert alert-danger">Harap pilih foto.</div>';
        }
    }
}
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $r = $conn->query("SELECT foto FROM galeri WHERE id=$id")->fetch_assoc();
    if ($r && $r['foto']) @unlink(UPLOAD_PATH . 'galeri/' . $r['foto']);
    $conn->query("DELETE FROM galeri WHERE id=$id");
    $msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Foto berhasil dihapus.</div>';
}

$list = $conn->query("SELECT g.*, p.nama_proyek FROM galeri g LEFT JOIN proyek p ON g.proyek_id=p.id ORDER BY g.created_at DESC");
$proyek_opt = $conn->query("SELECT id, nama_proyek FROM proyek ORDER BY nama_proyek ASC");
$proyek_arr = $proyek_opt->fetch_all(MYSQLI_ASSOC);

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
            <div class="breadcrumb"><a href="dashboard.php"><i class="fas fa-home"></i></a><span class="breadcrumb-sep">/</span><span>Galeri</span></div>
            <h1>Galeri Foto</h1>
            <p>Dokumentasi foto proyek</p>
        </div>
        <?php echo $msg; ?>

        <div style="display:flex; justify-content:flex-end; margin-bottom:20px;">
            <button class="btn btn-primary" data-modal="tambahModal"><i class="fas fa-plus"></i> Upload Foto</button>
        </div>

        <div class="galeri-grid">
        <?php while ($r = $list->fetch_assoc()): ?>
            <div class="galeri-item">
                <?php if ($r['foto']): ?>
                    <img src="<?php echo APP_URL . '/uploads/galeri/' . htmlspecialchars($r['foto']); ?>" alt="" style="width:100%; height:160px; object-fit:cover; display:block;">
                <?php else: ?>
                    <div class="galeri-img"><i class="fas fa-image"></i></div>
                <?php endif; ?>
                <div class="galeri-info">
                    <div class="galeri-title"><?php echo htmlspecialchars($r['judul'] ?: 'Tanpa Judul'); ?></div>
                    <div class="galeri-meta"><?php echo $r['nama_proyek'] ? htmlspecialchars($r['nama_proyek']) : 'Umum'; ?> &bull; <?php echo date('d/m/Y', strtotime($r['created_at'])); ?></div>
                    <a href="?hapus=<?php echo $r['id']; ?>" class="btn btn-danger btn-sm btn-delete" style="margin-top:8px; width:100%; justify-content:center;">
                        <i class="fas fa-trash"></i> Hapus
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>

<div class="modal-overlay" id="tambahModal">
    <div class="modal">
        <div class="modal-header"><span class="modal-title">Upload Foto</span><button class="modal-close">&times;</button></div>
        <form method="POST" enctype="multipart/form-data"><input type="hidden" name="action" value="tambah">
            <div class="modal-body">
                <div class="form-group"><label>Judul Foto</label><input type="text" name="judul" class="form-control"></div>
                <div class="form-group">
                    <label>Proyek</label>
                    <select name="proyek_id" class="form-control">
                        <option value="">-- Umum --</option>
                        <?php foreach ($proyek_arr as $pr): ?>
                        <option value="<?php echo $pr['id']; ?>"><?php echo htmlspecialchars($pr['nama_proyek']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Foto <span style="color:red">*</span></label><input type="file" name="foto" class="form-control" accept="image/*" required></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload</button>
            </div>
        </form>
    </div>
</div>
