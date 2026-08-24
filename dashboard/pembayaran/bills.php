<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}
$_SESSION['navigation'] = "8";
include_once("../navbar.php");
include '../koneks.php';

// Proses update status tagihan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $bill_id = (int)$_POST['bill_id'];
    $status = mysqli_real_escape_string($con, $_POST['status']);
    
    // Ambil borrowing_id dan bukti pembayaran untuk update
    $bill_data = mysqli_fetch_assoc(mysqli_query($con, "SELECT borrowing_id, payment_proof FROM bills WHERE id=$bill_id"));
    $borrowing_id = $bill_data['borrowing_id'] ?? null;
    $payment_proof = $bill_data['payment_proof'] ?? null;
    
    // Jika status Dibatalkan → hapus bukti pembayaran
    if ($status === 'Dibatalkan' && !empty($payment_proof)) {
        $file_path = "../assets/images/payments/" . $payment_proof;
        if (file_exists($file_path)) {
            unlink($file_path); // hapus file di server
        }
        // kosongkan kolom payment_proof di database
        mysqli_query($con, "UPDATE bills SET payment_proof=NULL WHERE id=$bill_id");
    }

    // Update status bill
    $update_bill = mysqli_query($con, "UPDATE bills SET status='$status' WHERE id=$bill_id");
    
    if ($update_bill) {
        // Jika status pembayaran lunas, update juga status peminjaman
        if ($status === 'Sudah Dibayar' && $borrowing_id) {
            mysqli_query($con, "UPDATE borrowing SET status='Hilang - Lunas' WHERE id=$borrowing_id");
        }
        echo "<script>alert('Status tagihan berhasil diperbarui!'); window.location='bills.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui status tagihan!');</script>";
    }
}


// Proses penghapusan data peminjaman
if (isset($_POST['delete'])) {
    $id_borrowing = (int)$_POST['id_borrowing'];
    
    // Hapus referensi di tabel bills terlebih dahulu
    $delete_bills_query = "DELETE FROM bills WHERE borrowing_id = $id_borrowing";
    mysqli_query($con, $delete_bills_query);
    
    // Sekarang hapus data di tabel borrowing
    $delete_query = "DELETE FROM borrowing WHERE id = $id_borrowing";
    if (mysqli_query($con, $delete_query)) {
        echo "Data berhasil dihapus";
    } else {
        echo "Error deleting record: " . mysqli_error($con);
    }
}

// Ambil semua data tagihan
$query = mysqli_query($con, "SELECT b.*, 
           br.loan_date, 
           br.actual_return_date, 
           bk.title, 
           s.fullname AS student_name
    FROM bills b
    JOIN borrowing br ON b.borrowing_id = br.id
    JOIN book bk ON br.book_id = bk.id
    JOIN siswa s ON br.borrower_id = s.id
    ORDER BY b.created_at DESC
");

$bills = [];
while ($row = mysqli_fetch_assoc($query)) {
    $bills[] = $row;
}
?>

<div class="container mt-5">
    <h3 class="mb-4 text-primary">📊 Manajemen Tagihan</h3>
    
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Tagihan</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="billsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Siswa</th>
                            <th>Judul Buku</th>
                            <th>Deskripsi</th>
                            <th>Jumlah</th>
                            <th>Tanggal Tagihan</th>
                            <th>Bukti Pembayaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bills as $bill) { ?>
                            <tr>
                                <td><?= $bill['id'] ?></td>
                                <td><?= htmlspecialchars($bill['student_name']) ?></td>
                                <td><?= htmlspecialchars($bill['title']) ?></td>
                                <td><?= htmlspecialchars($bill['description']) ?></td>
                                <td>Rp <?= number_format($bill['amount'], 0, ',', '.') ?></td>
                                <td><?= date('d-m-Y', strtotime($bill['created_at'])) ?></td>
                                <td>
                                    <?php if (!empty($bill['payment_proof'])) { ?>
                                        <a href="/projecteperpus/assets/images/payments/<?= $bill['payment_proof'] ?>" target="_blank" class="btn btn-sm btn-info">Lihat</a>
                                    <?php } else { ?>
                                        <span class="text-muted">Belum ada</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $bill['status'] == 'Belum Dibayar' ? 'danger' : 
                                        ($bill['status'] == 'Sudah Dibayar' ? 'success' : 
                                        ($bill['status'] == 'Menunggu Konfirmasi' ? 'warning' : 'secondary')) 
                                    ?>">
                                        <?= $bill['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $bill['id'] ?>">
                                        Ubah Status
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Modal Edit Status -->
                            <div class="modal fade" id="editModal<?= $bill['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $bill['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel<?= $bill['id'] ?>">Ubah Status Tagihan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="bill_id" value="<?= $bill['id'] ?>">
                                                
                                                <div class="mb-3">
                                                    <p><strong>Nama Siswa:</strong> <?= htmlspecialchars($bill['student_name']) ?></p>
                                                    <p><strong>Judul Buku:</strong> <?= htmlspecialchars($bill['title']) ?></p>
                                                    <p><strong>Deskripsi:</strong> <?= htmlspecialchars($bill['description']) ?></p>
                                                    <p><strong>Jumlah:</strong> Rp <?= number_format($bill['amount'], 0, ',', '.') ?></p>
                                                    
                                                    <?php if (!empty($bill['payment_proof'])) { ?>
                                                        <p><strong>Bukti Pembayaran:</strong> <br>
                                                            <a href="/projecteperpus/assets/images/payments/<?= $bill['payment_proof'] ?>" target="_blank" class="btn btn-sm btn-info">Lihat Bukti</a>
                                                        </p>
                                                    <?php } else { ?>
                                                        <p><strong>Bukti Pembayaran:</strong> <br>
                                                            <span class="text-muted">Belum ada</span>
                                                        </p>
                                                    <?php } ?>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="status<?= $bill['id'] ?>" class="form-label">Status:</label>
                                                    <select class="form-select" id="status<?= $bill['id'] ?>" name="status" required>
                                                        <option value="Belum Dibayar" <?= $bill['status'] == 'Belum Dibayar' ? 'selected' : '' ?>>Belum Dibayar</option>
                                                        <option value="Sudah Dibayar" <?= $bill['status'] == 'Sudah Dibayar' ? 'selected' : '' ?>>Sudah Dibayar</option>
                                                        <option value="Dibatalkan" <?= $bill['status'] == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JS & DataTables -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#billsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.12.1/i18n/id.json'
            }
        });
    });
</script>
<style>
    body {
        background-color: #f9f9fa;
    }
    h3 {
        font-weight: bold;
    }
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .table th {
        background-color: #f8f9fa;
    }
</style>