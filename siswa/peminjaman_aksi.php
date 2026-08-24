<?php
session_start();
include(__DIR__ . '/../dashboard/koneks.php');

if (!isset($_SESSION['siswa_id'])) {
    header("location:login.php");
    exit;
}

$borrower_id = $_SESSION['siswa_id'];
$book_ids = $_POST['book_id'];
$loan_date = date('Y-m-d');  // Tanggal request otomatis hari ini
$return_date = "NULL";  // Tidak diisi saat masih request

// Cek total peminjaman aktif
$cekTotalAktif = mysqli_query($con, "SELECT COUNT(*) AS total FROM borrowing 
    WHERE borrower_id = '$borrower_id' 
    AND status IN ('Request Peminjaman', 'Dipinjam', 'Belum Kembali')");

$dataAktif = mysqli_fetch_assoc($cekTotalAktif);
$totalAktif = $dataAktif['total'] ?? 0;

if ($totalAktif + count($book_ids) > 2) {
    $_SESSION['danger_message'] = "Batas maksimal peminjaman adalah 2 buku. Anda sudah memiliki $totalAktif buku aktif.";
    header("Location: peminjaman_tambah.php");
    exit;
}


// Cek dulu semua buku
foreach ($book_ids as $book_id) {
    $cekBuku = mysqli_query($con, "SELECT amount FROM book WHERE id = '$book_id'");
    $buku = mysqli_fetch_assoc($cekBuku);

    if ($buku['amount'] <= 0) {
        echo "<script>alert('Stok buku habis. Tidak bisa dipinjam lagi.'); window.location='peminjaman_tambah.php';</script>";
        exit;
    }

    /// Cek apakah sudah request
$cekRequest = mysqli_query($con, "SELECT * FROM borrowing 
    WHERE borrower_id = '$borrower_id' 
    AND book_id = '$book_id' 
    AND status = 'Request Peminjaman'");

if (mysqli_num_rows($cekRequest) > 0) {
    $_SESSION['warning_message'] = "Anda sudah request buku ini. Silakan tunggu konfirmasi admin.";
header("Location: peminjaman_tambah.php");
exit;

}

// Cek apakah masih dipinjam
$cekPinjam = mysqli_query($con, "SELECT * FROM borrowing 
    WHERE borrower_id = '$borrower_id' 
    AND book_id = '$book_id' 
    AND status = 'Dipinjam'");

if (mysqli_num_rows($cekPinjam) > 0) {
    $_SESSION['danger_message'] = "Anda masih meminjam buku ini. Harap kembalikan dulu sebelum meminjam lagi.";
header("Location: peminjaman_tambah.php");
exit;

}

// Cek apakah statusnya masih Belum Kembali
$cekBelumKembali = mysqli_query($con, "SELECT * FROM borrowing 
    WHERE borrower_id = '$borrower_id' 
    AND book_id = '$book_id' 
    AND status = 'Belum Kembali'");

if (mysqli_num_rows($cekBelumKembali) > 0) {
    $_SESSION['danger_message'] = "Anda belum mengembalikan buku ini. Silakan kembalikan sebelum meminjam lagi.";
    header("Location: peminjaman_tambah.php");
    exit;
}


}

// Kalau lolos semua cek, lakukan insert saja (TIDAK kurangi stok)
foreach ($book_ids as $book_id) {
    // Insert borrowing dengan status Request Peminjaman
    mysqli_query($con, "INSERT INTO borrowing (borrower_id, book_id, loan_date, return_date, status) 
        VALUES ('$borrower_id', '$book_id', '$loan_date', NULL, 'Request Peminjaman')");
}

// Bersihkan keranjang
unset($_SESSION['keranjang']);

header("location:peminjaman.php");
exit;
?>
