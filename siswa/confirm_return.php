<?php 
include '../dashboard/koneks.php'; 
if (isset($_POST['borrowing_id'])) {
    $id = $_POST['borrowing_id'];
    $actual_return = date('Y-m-d');
    $update = mysqli_query($con, "UPDATE borrowing SET status='Sudah Kembali', return_date='$actual_return' WHERE id='$id'");
    
    if ($update) {
        header("Location: data_peminjaman.php?success=1");
    } else {
        echo "Gagal mengkonfirmasi pengembalian";
    }
}
?>