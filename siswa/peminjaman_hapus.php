<?php
session_start();
include '../dashboard/koneks.php'; // Perhatikan perbedaan path di sini

if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = (int)$_POST['id'];
    $borrower_id = (int)$_SESSION['siswa_id'];
    $sql = "DELETE FROM borrowing WHERE id = $id AND borrower_id = $borrower_id";
    if (mysqli_query($con, $sql)) {
        header('Location: peminjaman.php?status=deleted');
        exit;
    } else {
        die('Error hapus: '.mysqli_error($con));
    }
} else {
    header('Location: peminjaman.php?status=invalid_id');
    exit;
}
