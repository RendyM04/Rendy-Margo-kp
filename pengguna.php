<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit; }
require_once '../config/config.php';
$page_title = 'Pengguna';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'tambah') {
        $nama  = $conn->real_escape_string($_POST['nama']);
        $email = $conn->real_escape_string($_POST['email']);
        $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role  = $conn->real_escape_string($_POST['role']);
        $conn->query("INSERT INTO pengguna (nama, email, password, role) VALUES ('$nama','$email','$pass','$role')");
        $msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Pengguna berhasil ditambahkan.</div>';
    } elseif ($_POST['action'] === 'edit') {
        $id   = (int)$_POST['id'];
        $nama = $conn->real_escape_string($_POST['nama']);
        $email= $conn->real_escape_string($_POST['email']);
        $role = $conn->real_escape_string($_POST['role']);
        $sql  = "UPDATE pengguna SET nama='$nama', email='$email', role='$role'";
        if (!empty($_POST['password'])) {
            $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql .= ", password='$pass'";
        }
        $conn->query("$sql WHERE id=$id");
        $msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Data pengguna berhasil diperbarui.</div>';
    }
}
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id != $_SESSION['user_id']) {
        $conn->query("DELETE FROM pengguna WHERE id=$id");
        $msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Pengguna berhasil dihapus.</div>';
    } else {
        $msg = '<div class="alert alert-danger">Tidak dapat menghapus akun sendiri.</div>';
    }
}

$list = $conn->query("SELECT * FROM pengguna ORDER BY created_at DESC");
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
            <div class="breadcrumb"><a href="dashboard.php"><i class="fas fa-home"></i></a><span class="breadcrumb-sep">/</span><span>Pengguna</span></div>
            <h1>Manajemen Pengguna</h1>
            <p>Kelola akun pengguna sistem</p>
        </div>
        <?php echo $msg; ?>
        <div class="card">
            <div class="card-header" style="padding:20px 24px;">
                <div class="page-actions" style="margin:0; width:100%;">
                    <div class="search-box"><i class="fas fa-search"></i><input type="text" id="searchInput" placeholder="Cari pengguna..."></div>
                    <button class="btn btn-primary" data-modal="tambahModal"><i class="fas fa-plus"></i> Tambah Pengguna</button>
                </div>
            </div>
            <div class="card-body" style="padding:0 24px 24px;">
                <div class="table-responsive">
                    <table>
                        <thead><tr><th>#</th><th>Nama</th><th>Email</th><th>Role</th><th>Terdaftar</th><th>Aksi</th></tr></thead>
                        <tbody>
                        <?php $no=1; while ($r = $list->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo htmlspecialchars($r['nama']); ?></strong></td>
                                <td><?php echo htmlspecialchars($r['email']); ?></td>
                                <td><span class="badge badge-<?php echo $r['role']==='admin'?'primary':'success'; ?>"><?php echo ucfirst($r['role']); ?></span></td>
                                <td><?php echo date('d/m/Y', strtotime($r['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-secondary btn-sm" onclick='openEditModal(<?php echo json_encode(["id"=>$r["id"],"nama"=>$r["nama"],"email"=>$r["email"],"role"=>$r["role"]]); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($r['id'] != $_SESSION['user_id']): ?>
                                    <a href="?hapus=<?php echo $r['id']; ?>" class="btn btn-danger btn-sm btn-delete"><i class="fas fa-trash"></i></a>
                                    <?php endif; ?>
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
        <div class="modal-header"><span class="modal-title">Tambah Pengguna</span><button class="modal-close">&times;</button></div>
        <form method="POST"><input type="hidden" name="action" value="tambah">
            <div class="modal-body">
                <div class="form-group"><label>Nama</label><input type="text" name="nama" class="form-control" required></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control"><option value="admin">Admin</option><option value="user">User</option></select>
                    </div>
                </div>
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
        <div class="modal-header"><span class="modal-title">Edit Pengguna</span><button class="modal-close">&times;</button></div>
        <form method="POST"><input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="edit_id">
            <div class="modal-body">
                <div class="form-group"><label>Nama</label><input type="text" name="nama" id="edit_nama" class="form-control" required></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" id="edit_email" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group"><label>Password Baru (kosongkan jika tidak diubah)</label><input type="password" name="password" class="form-control"></div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" id="edit_role" class="form-control"><option value="admin">Admin</option><option value="user">User</option></select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
            </div>
        </form>
    </div>
</div>
