<?php
session_start();
include '../dashboard/koneks.php';

if (!isset($_SESSION['siswa_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: ../home.php?pesan=id_tidak_ditemukan");
    exit;
}

$siswa_id = $_SESSION['siswa_id'];
$book_id = intval($_GET['id']);
$tanggal_pinjam = date("Y-m-d");
$tanggal_kembali = date("Y-m-d", strtotime("+2 days", strtotime($tanggal_pinjam)));

// Periksa apakah buku tersedia
$cek = mysqli_query($con, "SELECT * FROM book WHERE id = '$book_id' AND amount > 0");
if (mysqli_num_rows($cek) == 0) {
    header("Location: ../home.php?pesan=buku_tidak_tersedia");
    exit;
}

// Buat permintaan peminjaman dengan status 'menunggu'
$query = mysqli_query($con, "INSERT INTO borrowing (borrower_id, book_id, loan_date, return_date, status) VALUES ('$siswa_id', '$book_id', '$tanggal_pinjam', '$tanggal_kembali', 'menunggu')");


if ($query) {
    header("Location: ../home.php?pesan=request_berhasil");
} else {
    header("Location: ../home.php?pesan=request_gagal");
}
?>
