<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$_SESSION['navigation'] = "9";

include_once("../navbar.php");
include '../koneks.php';

// Ambil data untuk laporan keuangan
$monthly_report = mysqli_query($con, "SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        SUM(CASE WHEN status = 'Sudah Dibayar' THEN amount ELSE 0 END) as paid_amount,
        COUNT(CASE WHEN status = 'Sudah Dibayar' THEN 1 END) as paid_count,
        SUM(CASE WHEN status = 'Belum Dibayar' THEN amount ELSE 0 END) as unpaid_amount,
        COUNT(CASE WHEN status = 'Belum Dibayar' THEN 1 END) as unpaid_count
    FROM bills
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
");

$category_report = mysqli_query($con, "SELECT 
        CASE 
            WHEN description LIKE '%rusak%' THEN 'Biaya Perbaikan'
            WHEN description LIKE '%hilang%' THEN 'Biaya Penggantian'
            ELSE 'Lainnya'
        END as category,
        SUM(CASE WHEN status = 'Sudah Dibayar' THEN amount ELSE 0 END) as total_amount,
        COUNT(CASE WHEN status = 'Sudah Dibayar' THEN 1 END) as total_count
    FROM bills
    GROUP BY category
");

// Total pendapatan
$total_income = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT SUM(amount) as total FROM bills WHERE status = 'Sudah Dibayar'
"))['total'] ?? 0;

// Total tagihan belum dibayar
$total_unpaid = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT SUM(amount) as total FROM bills WHERE status = 'Belum Dibayar'
"))['total'] ?? 0;
?>

<div class="container mt-5">
    <h3 class="mb-4 text-primary">📈 Laporan Keuangan</h3>
    
    <!-- Ringkasan Keuangan -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Pendapatan</h5>
                    <h2>Rp <?= number_format($total_income, 0, ',', '.') ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Tagihan Belum Dibayar</h5>
                    <h2>Rp <?= number_format($total_unpaid, 0, ',', '.') ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Laporan Bulanan -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Laporan Bulanan</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Jumlah Tagihan Dibayar</th>
                            <th>Pendapatan</th>
                            <th>Jumlah Tagihan Belum Dibayar</th>
                            <th>Total Tertunggak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($monthly_report)) { ?>
                            <tr>
                                <td><?= date('F Y', strtotime($row['month'] . '-01')) ?></td>
                                <td><?= $row['paid_count'] ?></td>
                                <td>Rp <?= number_format($row['paid_amount'], 0, ',', '.') ?></td>
                                <td><?= $row['unpaid_count'] ?></td>
                                <td>Rp <?= number_format($row['unpaid_amount'], 0, ',', '.') ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Laporan Kategori -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Laporan Berdasarkan Kategori</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Jumlah Transaksi</th>
                            <th>Total Pendapatan</th>
                            <th>Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($category_report)) { ?>
                            <tr>
                                <td><?= $row['category'] ?></td>
                                <td><?= $row['total_count'] ?></td>
                                <td>Rp <?= number_format($row['total_amount'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($row['total_count'] > 0 ? $row['total_amount'] / $row['total_count'] : 0, 0, ',', '.') ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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