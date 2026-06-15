<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit; }
require_once '../config/config.php';
$page_title = 'Jadwal';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'tambah') {
        $kegiatan = $conn->real_escape_string($_POST['kegiatan']);
        $tanggal = $_POST['tanggal'];
        $waktu = $_POST['waktu'];
        $lokasi = $conn->real_escape_string($_POST['lokasi']);
        $pj = $conn->real_escape_string($_POST['penanggung_jawab']);
        $status = $conn->real_escape_string($_POST['status']);
        $proyek_id = $_POST['proyek_id'] ?: 'NULL';
        $conn->query("INSERT INTO jadwal (kegiatan, tanggal, waktu, lokasi, penanggung_jawab, status, proyek_id) VALUES ('$kegiatan','$tanggal','$waktu','$lokasi','$pj','$status',$proyek_id)");
        $msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Jadwal berhasil ditambahkan.</div>';
    } elseif ($_POST['action'] === 'edit') {
        $id = (int)$_POST['id'];
        $kegiatan = $conn->real_escape_string($_POST['kegiatan']);
        $tanggal = $_POST['tanggal'];
        $waktu = $_POST['waktu'];
        $lokasi = $conn->real_escape_string($_POST['lokasi']);
        $pj = $conn->real_escape_string($_POST['penanggung_jawab']);
        $status = $conn->real_escape_string($_POST['status']);
        $proyek_id = $_POST['proyek_id'] ?: 'NULL';
        $conn->query("UPDATE jadwal SET kegiatan='$kegiatan', tanggal='$tanggal', waktu='$waktu', lokasi='$lokasi', penanggung_jawab='$pj', status='$status', proyek_id=$proyek_id WHERE id=$id");
        $msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Jadwal berhasil diperbarui.</div>';
    }
}
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $conn->query("DELETE FROM jadwal WHERE id=$id");
    $msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Jadwal berhasil dihapus.</div>';
}

$list = $conn->query("SELECT j.*, p.nama_proyek FROM jadwal j LEFT JOIN proyek p ON j.proyek_id = p.id ORDER BY j.tanggal DESC, j.waktu ASC");
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
            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fas fa-home"></i></a>
                <span class="breadcrumb-sep">/</span><span>Jadwal</span>
            </div>
            <h1>Manajemen Jadwal</h1>
            <p>Kelola jadwal kegiatan</p>
        </div>
        <?php echo $msg; ?>
        <div class="card">
            <div class="card-header" style="padding:20px 24px;">
                <div class="page-actions" style="margin:0; width:100%;">
                    <div class="search-box"><i class="fas fa-search"></i><input type="text" id="searchInput" placeholder="Cari jadwal..."></div>
                    <button class="btn btn-primary" data-modal="tambahModal"><i class="fas fa-plus"></i> Tambah Jadwal</button>
                </div>
            </div>
            <div class="card-body" style="padding:0 24px 24px;">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr><th>#</th><th>Tanggal</th><th>Waktu</th><th>Kegiatan</th><th>Lokasi</th><th>Penanggung Jawab</th><th>Proyek</th><th>Status</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                        <?php $no=1; while ($r = $list->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($r['tanggal'])); ?></td>
                                <td><?php echo date('H:i', strtotime($r['waktu'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($r['kegiatan']); ?></strong></td>
                                <td><?php echo htmlspecialchars($r['lokasi']); ?></td>
                                <td><?php echo htmlspecialchars($r['penanggung_jawab']); ?></td>
                                <td><?php echo $r['nama_proyek'] ? htmlspecialchars($r['nama_proyek']) : '<span style="color:var(--text-light)">-</span>'; ?></td>
                                <td>
                                    <?php $b=['Aktif'=>'primary','Selesai'=>'success','Dibatalkan'=>'danger']; ?>
                                    <span class="badge badge-<?php echo $b[$r['status']]??'secondary'; ?>"><?php echo $r['status']; ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-secondary btn-sm" onclick='openEditModal(<?php echo json_encode(["id"=>$r["id"],"kegiatan"=>$r["kegiatan"],"tanggal"=>$r["tanggal"],"waktu"=>substr($r["waktu"],0,5),"lokasi"=>$r["lokasi"],"penanggung_jawab"=>$r["penanggung_jawab"],"status"=>$r["status"],"proyek_id"=>$r["proyek_id"]]); ?>)'>
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
        <div class="modal-header"><span class="modal-title">Tambah Jadwal</span><button class="modal-close">&times;</button></div>
        <form method="POST"><input type="hidden" name="action" value="tambah">
            <div class="modal-body">
                <div class="form-group"><label>Kegiatan</label><input type="text" name="kegiatan" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group"><label>Tanggal</label><input type="date" name="tanggal" class="form-control" required></div>
                    <div class="form-group"><label>Waktu</label><input type="time" name="waktu" class="form-control"></div>
                </div>
                <div class="form-group"><label>Lokasi</label><input type="text" name="lokasi" class="form-control"></div>
                <div class="form-group"><label>Penanggung Jawab</label><input type="text" name="penanggung_jawab" class="form-control"></div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option>Aktif</option><option>Selesai</option><option>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Proyek (opsional)</label>
                        <select name="proyek_id" class="form-control">
                            <option value="">-- Pilih Proyek --</option>
                            <?php foreach ($proyek_arr as $pr): ?>
                            <option value="<?php echo $pr['id']; ?>"><?php echo htmlspecialchars($pr['nama_proyek']); ?></option>
                            <?php endforeach; ?>
                        </select>
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
        <div class="modal-header"><span class="modal-title">Edit Jadwal</span><button class="modal-close">&times;</button></div>
        <form method="POST"><input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="edit_id">
            <div class="modal-body">
                <div class="form-group"><label>Kegiatan</label><input type="text" name="kegiatan" id="edit_kegiatan" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group"><label>Tanggal</label><input type="date" name="tanggal" id="edit_tanggal" class="form-control" required></div>
                    <div class="form-group"><label>Waktu</label><input type="time" name="waktu" id="edit_waktu" class="form-control"></div>
                </div>
                <div class="form-group"><label>Lokasi</label><input type="text" name="lokasi" id="edit_lokasi" class="form-control"></div>
                <div class="form-group"><label>Penanggung Jawab</label><input type="text" name="penanggung_jawab" id="edit_penanggung_jawab" class="form-control"></div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit_status" class="form-control">
                            <option>Aktif</option><option>Selesai</option><option>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Proyek</label>
                        <select name="proyek_id" id="edit_proyek_id" class="form-control">
                            <option value="">-- Pilih Proyek --</option>
                            <?php foreach ($proyek_arr as $pr): ?>
                            <option value="<?php echo $pr['id']; ?>"><?php echo htmlspecialchars($pr['nama_proyek']); ?></option>
                            <?php endforeach; ?>
                        </select>
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
