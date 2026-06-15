<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit; }
require_once '../config/config.php';
$page_title = 'Proyek';

$msg = '';

// Tambah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'tambah') {
        $nama = $conn->real_escape_string($_POST['nama_proyek']);
        $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
        $lokasi = $conn->real_escape_string($_POST['lokasi']);
        $tgl_mulai = $_POST['tanggal_mulai'];
        $tgl_selesai = $_POST['tanggal_selesai'];
        $status = $conn->real_escape_string($_POST['status']);
        $foto = '';

        if (!empty($_FILES['foto']['name'])) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], UPLOAD_PATH . 'proyek/' . $foto);
        }

        $conn->query("INSERT INTO proyek (nama_proyek, deskripsi, lokasi, tanggal_mulai, tanggal_selesai, status, foto)
            VALUES ('$nama','$deskripsi','$lokasi','$tgl_mulai','$tgl_selesai','$status','$foto')");
        $msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Proyek berhasil ditambahkan.</div>';

    } elseif ($_POST['action'] === 'edit') {
        $id = (int)$_POST['id'];
        $nama = $conn->real_escape_string($_POST['nama_proyek']);
        $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
        $lokasi = $conn->real_escape_string($_POST['lokasi']);
        $tgl_mulai = $_POST['tanggal_mulai'];
        $tgl_selesai = $_POST['tanggal_selesai'];
        $status = $conn->real_escape_string($_POST['status']);
        $old = $conn->query("SELECT foto FROM proyek WHERE id=$id")->fetch_assoc();
        $foto = $old['foto'];

        if (!empty($_FILES['foto']['name'])) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], UPLOAD_PATH . 'proyek/' . $foto);
        }

        $conn->query("UPDATE proyek SET nama_proyek='$nama', deskripsi='$deskripsi', lokasi='$lokasi',
            tanggal_mulai='$tgl_mulai', tanggal_selesai='$tgl_selesai', status='$status', foto='$foto'
            WHERE id=$id");
        $msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Proyek berhasil diperbarui.</div>';
    }
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $conn->query("DELETE FROM proyek WHERE id=$id");
    $msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Proyek berhasil dihapus.</div>';
}

$proyek_list = $conn->query("SELECT * FROM proyek ORDER BY created_at DESC");

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
                <div class="topbar-avatar"><?php echo strtoupper(substr($_SESSION['user_nama'],0,1)); ?></div>
                <span class="topbar-username"><?php echo htmlspecialchars($_SESSION['user_nama']); ?></span>
            </div>
        </div>
    </div>
    <div class="main-content">
        <div class="page-header">
            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fas fa-home"></i></a>
                <span class="breadcrumb-sep">/</span>
                <span>Proyek</span>
            </div>
            <h1>Manajemen Proyek</h1>
            <p>Kelola semua data proyek</p>
        </div>

        <?php echo $msg; ?>

        <div class="card">
            <div class="card-header" style="padding:20px 24px;">
                <div class="page-actions" style="margin:0; width:100%;">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Cari proyek...">
                    </div>
                    <button class="btn btn-primary" data-modal="tambahModal">
                        <i class="fas fa-plus"></i> Tambah Proyek
                    </button>
                </div>
            </div>
            <div class="card-body" style="padding:0 24px 24px;">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Proyek</th>
                                <th>Lokasi</th>
                                <th>Tgl Mulai</th>
                                <th>Tgl Selesai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $no=1; while ($p = $proyek_list->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo htmlspecialchars($p['nama_proyek']); ?></strong></td>
                                <td><?php echo htmlspecialchars($p['lokasi']); ?></td>
                                <td><?php echo $p['tanggal_mulai'] ? date('d/m/Y', strtotime($p['tanggal_mulai'])) : '-'; ?></td>
                                <td><?php echo $p['tanggal_selesai'] ? date('d/m/Y', strtotime($p['tanggal_selesai'])) : '-'; ?></td>
                                <td>
                                    <?php $badge = ['Berjalan'=>'primary','Selesai'=>'success','Tertunda'=>'warning']; ?>
                                    <span class="badge badge-<?php echo $badge[$p['status']]??'secondary'; ?>"><?php echo $p['status']; ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-secondary btn-sm" onclick='openEditModal(<?php echo json_encode([
                                        "id"=>$p["id"], "nama_proyek"=>$p["nama_proyek"], "deskripsi"=>$p["deskripsi"],
                                        "lokasi"=>$p["lokasi"], "tanggal_mulai"=>$p["tanggal_mulai"],
                                        "tanggal_selesai"=>$p["tanggal_selesai"], "status"=>$p["status"]
                                    ]); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?hapus=<?php echo $p['id']; ?>" class="btn btn-danger btn-sm btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
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

<!-- Modal Tambah -->
<div class="modal-overlay" id="tambahModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Tambah Proyek</span>
            <button class="modal-close">&times;</button>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="tambah">
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Proyek</label>
                    <input type="text" name="nama_proyek" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label>Lokasi</label>
                    <input type="text" name="lokasi" class="form-control">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option>Berjalan</option>
                            <option>Selesai</option>
                            <option>Tertunda</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Foto</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
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

<!-- Modal Edit -->
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Edit Proyek</span>
            <button class="modal-close">&times;</button>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Proyek</label>
                    <input type="text" name="nama_proyek" id="edit_nama_proyek" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" id="edit_deskripsi" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label>Lokasi</label>
                    <input type="text" name="lokasi" id="edit_lokasi" class="form-control">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="edit_tanggal_mulai" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="edit_tanggal_selesai" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit_status" class="form-control">
                            <option>Berjalan</option>
                            <option>Selesai</option>
                            <option>Tertunda</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Foto Baru (opsional)</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
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
