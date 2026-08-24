<?php
session_start();

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    // Jika sudah login, arahkan ke dashboard
    header('Location: ../dashboard/home/');
    exit();
} else {
    // Jika belum login, arahkan ke login
    $_SESSION['warning'] = "Silahkan login terlebih dahulu untuk mengakses halaman dashboard";
    header('Location: ../login.php');
    exit();
}
?>
