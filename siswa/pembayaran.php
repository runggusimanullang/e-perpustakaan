<?php
session_start();
include_once("header.php");
include '../dashboard/koneks.php';

$borrower_id = $_SESSION['siswa_id'] ?? null;
if (!$borrower_id) {
    echo "Anda harus login sebagai siswa.";
    exit;
}

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'pay_bill') {
    $bill_id = (int)$_POST['bill_id'];
    $payment_method = mysqli_real_escape_string($con, $_POST['payment_method']);
    
    // Update status pembayaran
    $update = mysqli_query($con, "UPDATE bills SET status='Sudah Dibayar', payment_date=NOW(), payment_method='$payment_method' WHERE id=$bill_id AND borrower_id=$borrower_id");
    
    if ($update) {
        echo "<script>alert('Pembayaran berhasil!'); window.location='pembayaran.php';</script>";
    } else {
        echo "<script>alert('Gagal melakukan pembayaran!');</script>";
    }
}

// Ambil data tagihan siswa
$query = mysqli_query($con, "
    SELECT b.*, br.loan_date, br.actual_return_date, bk.title 
    FROM bills b
    JOIN borrowing br ON b.borrowing_id = br.id
    JOIN book bk ON br.book_id = bk.id
    WHERE b.borrower_id = $borrower_id AND b.status = 'Belum Dibayar'
    ORDER BY b.created_at DESC
");

$bills = [];
while ($row = mysqli_fetch_assoc($query)) {
    $bills[] = $row;
}

// Ambil riwayat pembayaran
$history_query = mysqli_query($con, "
    SELECT b.*, br.loan_date, br.actual_return_date, bk.title 
    FROM bills b
    JOIN borrowing br ON b.borrowing_id = br.id
    JOIN book bk ON br.book_id = bk.id
    WHERE b.borrower_id = $borrower_id AND b.status = 'Sudah Dibayar'
    ORDER BY b.payment_date DESC
");

$payment_history = [];
while ($row = mysqli_fetch_assoc($history_query)) {
    $payment_history[] = $row;
}
?>

<div class="container mt-5">
    <h3 class="mb-4 text-primary">💳 Tagihan Pembayaran</h3>
    
    <?php if (empty($bills)) { ?>
        <div class="alert alert-success">
            Anda tidak memiliki tagihan yang harus dibayar.
        </div>
    <?php } else { ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Tagihan Aktif</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Buku</th>
                                <th>Deskripsi</th>
                                <th>Jumlah</th>
                                <th>Tanggal Tagihan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($bills as $bill) { ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($bill['title']) ?></td>
                                    <td><?= htmlspecialchars($bill['description']) ?></td>
                                    <td>Rp <?= number_format($bill['amount'], 0, ',', '.') ?></td>
                                    <td><?= date('d-m-Y', strtotime($bill['created_at'])) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#payModal<?= $bill['id'] ?>">
                                            Bayar
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Modal Pembayaran -->
                                <div class="modal fade" id="payModal<?= $bill['id'] ?>" tabindex="-1" aria-labelledby="payModalLabel<?= $bill['id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="payModalLabel<?= $bill['id'] ?>">Konfirmasi Pembayaran</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="post">
                                                <div class="modal-body">
                                                    <input type="hidden" name="action" value="pay_bill">
                                                    <input type="hidden" name="bill_id" value="<?= $bill['id'] ?>">
                                                    
                                                    <div class="mb-3">
                                                        <p><strong>Judul Buku:</strong> <?= htmlspecialchars($bill['title']) ?></p>
                                                        <p><strong>Deskripsi:</strong> <?= htmlspecialchars($bill['description']) ?></p>
                                                        <p><strong>Jumlah:</strong> Rp <?= number_format($bill['amount'], 0, ',', '.') ?></p>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="paymentMethod<?= $bill['id'] ?>" class="form-label">Metode Pembayaran:</label>
                                                        <select class="form-select" id="paymentMethod<?= $bill['id'] ?>" name="payment_method" required>
                                                            <option value="">Pilih Metode Pembayaran</option>
                                                            <option value="Tunai">Tunai</option>
                                                            <option value="Transfer Bank">Transfer Bank</option>
                                                            <option value="E-Wallet">E-Wallet</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Konfirmasi Pembayaran</button>
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
    <?php } ?>
    
    <h3 class="mb-4 text-primary">📋 Riwayat Pembayaran</h3>
    
    <?php if (empty($payment_history)) { ?>
        <div class="alert alert-info">
            Anda belum memiliki riwayat pembayaran.
        </div>
    <?php } else { ?>
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Riwayat Pembayaran</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Buku</th>
                                <th>Deskripsi</th>
                                <th>Jumlah</th>
                                <th>Tanggal Pembayaran</th>
                                <th>Metode Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($payment_history as $payment) { ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($payment['title']) ?></td>
                                    <td><?= htmlspecialchars($payment['description']) ?></td>
                                    <td>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></td>
                                    <td><?= date('d-m-Y', strtotime($payment['payment_date'])) ?></td>
                                    <td><?= htmlspecialchars($payment['payment_method']) ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<!-- Tambahkan Bootstrap JS jika belum ada -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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