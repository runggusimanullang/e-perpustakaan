<?php
include_once("header.php");

$borrower_id = $_SESSION['siswa_id'] ?? null;
if (!$borrower_id) {
    die("Anda harus login sebagai siswa.");
}

$query = mysqli_query($con, "SELECT 
        b.*, 
        bk.title AS book_title
    FROM bills b
    LEFT JOIN borrowing br ON b.borrowing_id = br.id
    LEFT JOIN book bk ON br.book_id = bk.id
    WHERE b.borrower_id = $borrower_id
    ORDER BY b.id DESC
");
?>

<h2>Riwayat Pembayaran</h2>
<hr>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>Nama Buku & Deskripsi</th>
            <th>Jumlah</th>
            <th>Status</th>
            <th>Bukti Pembayaran</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    if (mysqli_num_rows($query) > 0) {
        $no = 1;
        while ($row = mysqli_fetch_assoc($query)) {
            echo "<tr>";
            echo "<td>{$no}</td>";
            
            $namaBuku = $row['book_title'] ?? 'Tidak diketahui';
            echo "<td><strong>{$namaBuku}</strong><br>{$row['description']}</td>";
            
            echo "<td>Rp " . number_format($row['amount'], 0, ',', '.') . "</td>";
            echo "<td>{$row['status']}</td>";

            if (!empty($row['payment_proof'])) {
                echo "<td><img src='../assets/images/payments/{$row['payment_proof']}' width='100'></td>";
            } else {
                echo "<td>-</td>";
            }

            echo "<td>";
            if ($row['status'] == 'Belum Dibayar') {
                echo "<a href='upload_bukti.php?bill_id={$row['id']}' class='btn btn-sm btn-primary'>Upload Bukti</a>";
            } elseif ($row['status'] == 'Menunggu Konfirmasi') {
                echo "<span class='badge bg-warning text-dark'>Menunggu Konfirmasi</span>";
            } elseif ($row['status'] == 'Lunas') {
                echo "<span class='badge bg-success'>Lunas</span>";
            }
            echo "</td>";

            echo "</tr>";
            $no++;
        }
    } else {
        echo "<tr><td colspan='6' class='text-center'>Tidak ada riwayat pembayaran</td></tr>";
    }
    ?>
    </tbody>
</table>

<?php
include_once("footer.php");
?>
