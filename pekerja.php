<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit; }
require_once '../config/config.php';
$page_title = 'Pekerja';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'tambah') {
        $nama = $conn->real_escape_string($_POST['nama']);
        $jabatan = $conn->real_escape_string($_POST['jabatan']);
        $telepon = $conn->real_escape_string($_POST['telepon']);
        $email = $conn->real_escape_string($_POST['email']);
        $alamat = $conn->real_escape_string($_POST['alamat']);
        $conn->query("INSERT INTO pekerja (nama, jabatan, telepon, email, alamat) VALUES ('$nama','$jabatan','$telepon','$email','$alamat')");
        $msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Pekerja berhasil ditambahkan.</div>';
    } elseif ($_POST['action'] === 'edit') {
        $id = (int)$_POST['id'];
        $nama = $conn->real_escape_string($_POST['nama']);
        $jabatan = $conn->real_escape_string($_POST['jabatan']);
        $telepon = $conn->real_escape_string($_POST['telepon']);
        $email = $conn->real_escape_string($_POST['email']);
        $alamat = $conn->real_escape_string($_POST['alamat']);
        $conn->query("UPDATE pekerja SET nama='$nama', jabatan='$jabatan', telepon='$telepon', email='$email', alamat='$alamat' WHERE id=$id");
        $msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Data pekerja berhasil diperbarui.</div>';
    }
}
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $conn->query("DELETE FROM pekerja WHERE id=$id");
    $msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Pekerja berhasil dihapus.</div>';
}

$list = $conn->query("SELECT * FROM pekerja ORDER BY nama ASC");
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
            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fas fa-home"></i></a>
                <span class="breadcrumb-sep">/</span><span>Pekerja</span>
            </div>
            <h1>Manajemen Pekerja</h1>
            <p>Kelola data pekerja</p>
        </div>
        <?php echo $msg; ?>
        <div class="card">
            <div class="card-header" style="padding:20px 24px;">
                <div class="page-actions" style="margin:0; width:100%;">
                    <div class="search-box"><i class="fas fa-search"></i><input type="text" id="searchInput" placeholder="Cari pekerja..."></div>
                    <button class="btn btn-primary" data-modal="tambahModal"><i class="fas fa-plus"></i> Tambah Pekerja</button>
                </div>
            </div>
            <div class="card-body" style="padding:0 24px 24px;">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr><th>#</th><th>Nama</th><th>Jabatan</th><th>Telepon</th><th>Email</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                        <?php $no=1; while ($r = $list->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo htmlspecialchars($r['nama']); ?></strong></td>
                                <td><?php echo htmlspecialchars($r['jabatan']); ?></td>
                                <td><?php echo htmlspecialchars($r['telepon']); ?></td>
                                <td><?php echo htmlspecialchars($r['email']); ?></td>
                                <td>
                                    <button class="btn btn-secondary btn-sm" onclick='openEditModal(<?php echo json_encode(["id"=>$r["id"],"nama"=>$r["nama"],"jabatan"=>$r["jabatan"],"telepon"=>$r["telepon"],"email"=>$r["email"],"alamat"=>$r["alamat"]]); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?hapus=<?php echo $r['id']; ?>" class="btn btn-danger btn-sm btn-delete"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>

<div class="modal-overlay" id="tambahModal">
    <div class="modal">
        <div class="modal-header"><span class="modal-title">Tambah Pekerja</span><button class="modal-close">&times;</button></div>
        <form method="POST"><input type="hidden" name="action" value="tambah">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group"><label>Nama</label><input type="text" name="nama" class="form-control" required></div>
                    <div class="form-group"><label>Jabatan</label><input type="text" name="jabatan" class="form-control"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Telepon</label><input type="text" name="telepon" class="form-control"></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control"></div>
                </div>
                <div class="form-group"><label>Alamat</label><textarea name="alamat" class="form-control"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="editModal">
    <div class="modal">
        <div class="modal-header"><span class="modal-title">Edit Pekerja</span><button class="modal-close">&times;</button></div>
        <form method="POST"><input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="edit_id">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group"><label>Nama</label><input type="text" name="nama" id="edit_nama" class="form-control" required></div>
                    <div class="form-group"><label>Jabatan</label><input type="text" name="jabatan" id="edit_jabatan" class="form-control"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Telepon</label><input type="text" name="telepon" id="edit_telepon" class="form-control"></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" id="edit_email" class="form-control"></div>
                </div>
                <div class="form-group"><label>Alamat</label><textarea name="alamat" id="edit_alamat" class="form-control"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
            </div>
        </form>
    </div>
</div>
