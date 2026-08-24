<?php
session_start();
include_once("../dashboard/koneks.php");

// Validasi dan casting ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Ambil data peminjaman berdasarkan ID
    $getBorrowing = mysqli_query($con, "SELECT * FROM borrowing WHERE id = $id");
    
    if ($getBorrowing && mysqli_num_rows($getBorrowing) > 0) {
        $borrowing = mysqli_fetch_assoc($getBorrowing);

        if ($borrowing['status'] === 'Belum Kembali') {
            $book_id = $borrowing['book_id'];

            // Update status dan actual return date
            $updateBorrowing = mysqli_query($con, "
                UPDATE borrowing 
                SET status = 'Sudah Kembali', actual_return_date = CURDATE() 
                WHERE id = $id");

            // Tambahkan jumlah stok kembali
            $updateBook = mysqli_query($con, "
                UPDATE book 
                SET amount = amount + 1 
                WHERE id = $book_id");

            if ($updateBorrowing && $updateBook) {
                $_SESSION['success'] = "Pengembalian berhasil.";
            } else {
                $_SESSION['error'] = "Gagal update pengembalian: " . mysqli_error($con);
            }
        } else {
            $_SESSION['error'] = "Buku ini sudah dikembalikan sebelumnya.";
        }
    } else {
        $_SESSION['error'] = "Data peminjaman tidak ditemukan.";
    }
} else {
    $_SESSION['error'] = "ID tidak valid.";
}

// Redirect kembali ke halaman daftar peminjaman
header("Location: ./index.php");
exit;
?>
