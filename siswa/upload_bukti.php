<?php
session_start();
include '../dashboard/koneks.php';

$borrower_id = $_SESSION['siswa_id'] ?? null;
$bill_id = $_GET['bill_id'] ?? null;

if (!$borrower_id || !$bill_id) {
    die("Akses tidak valid.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_FILES['bukti']['name'])) {
        $target_dir = "../assets/images/payments/";
        
        // Pastikan folder ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . strtolower($ext);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file)) {
            // Gunakan kolom payment_proff sesuai nama di database
            $query = "UPDATE bills 
                      SET payment_proof='$filename', status='Menunggu Konfirmasi' 
                      WHERE id=$bill_id AND borrower_id=$borrower_id";
            mysqli_query($con, $query) or die(mysqli_error($con));

            echo "<script>
                alert('Bukti pembayaran berhasil diunggah, menunggu konfirmasi admin.');
                window.location='riwayat_pembayaran.php';
            </script>";
        } else {
            echo "<script>alert('Gagal mengunggah bukti pembayaran.');</script>";
        }
    }
}
?>

<form method="post" enctype="multipart/form-data">
    <h3>Unggah Bukti Pembayaran</h3>
    <input type="file" name="bukti" accept="image/*" required>
    <button type="submit">Kirim</button>
</form>
