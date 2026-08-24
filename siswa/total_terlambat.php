<?php
session_start();

$con = mysqli_connect("localhost", "root", "", "projecteperpus");
if (!$con) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if (!isset($_SESSION['siswa_id'])) {
    die("Anda harus login sebagai siswa untuk mengakses data keterlambatan.");
}

$siswaId = $_SESSION['siswa_id'];

$query = "
    SELECT b.id, book.title, b.loan_date, b.return_date
    FROM borrowing b
    JOIN book ON b.book_id = book.id
    WHERE b.borrower_id = '$siswaId'
    AND b.return_date < CURDATE()
    AND b.status = 'Belum Kembali'
    ORDER BY b.return_date ASC
";

$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Buku Terlambat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f8ff; /* biru sangat muda */
        }
        h3 {
            color: #0d6efd;
            font-weight: bold;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0 15px rgba(0,0,0,.1);
        }
        .table thead {
            background: #0d6efd;
            color: white;
        }
        .table tbody tr:hover {
            background-color: #e7f1ff;
        }
        .btn-secondary {
            background-color: #0d6efd;
            border: none;
        }
        .alert {
            border-radius: 1rem;
        }
    </style>
</head>
<body class="p-4">
    <div class="container">
        <div class="card p-4">
            <h3 class="mb-4 text-center">📚 Buku yang Terlambat Dikembalikan</h3>
            
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-center mb-0 shadow-sm">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Judul Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Harus Kembali</th>
                                <th>Terlambat (hari)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td><?= date('d M Y', strtotime($row['loan_date'])) ?></td>
                                    <td><?= date('d M Y', strtotime($row['return_date'])) ?></td>
                                    <td class="fw-bold text-danger">
                                        <?php
                                        $hariTerlambat = (new DateTime())->diff(new DateTime($row['return_date']))->days;
                                        echo $hariTerlambat . ' hari';
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center mt-3">
                    🎉 Tidak ada buku yang terlambat dikembalikan.
                </div>
            <?php endif; ?>

            <!-- Tombol Kembali -->
            <div class="text-center mt-4">
                <a href="javascript:history.back()" class="btn btn-secondary btn-lg">← Kembali</a>
            </div>
        </div>
    </div>
</body>
</html>
