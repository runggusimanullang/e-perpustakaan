<?php
// Pastikan session hanya dimulai sekali
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cegah akses tanpa login
if (!isset($_SESSION['success']) || $_SESSION['success'] !== "login_siswa") {
    header("location:../login.php?alert=belum_login");
    exit;
}

// Pastikan koneksi database hanya di-include sekali
require_once("../dashboard/koneks.php");
$siswa_id = $_SESSION['siswa_id'];

$query = mysqli_query($con, "SELECT * FROM siswa WHERE id = '$siswa_id'");
$data_siswa = mysqli_fetch_assoc($query);

$photoFilename = $data_siswa['photo_filename'];
if (!empty($photoFilename) && file_exists("../assets/images/siswa/" . $photoFilename)) {
    $photoURL = "../assets/images/siswa/" . $photoFilename;
} else {
    $photoURL = "../assets/images/profile/user-1.jpg"; // fallback default
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>E-Perpustakaan SD Santa Maria Fatima</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/logoNav.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <!-- Sidebar Start -->
        <aside class="left-sidebar">
            <div>
                <div class="brand-logo d-flex align-items-center justify-content-between">
                    <h4 class="text-center">
                        <img src="../assets/images/logos/logoNav.png" width="30" alt="" class="mb-2">
                        E-Perpus SaMarFa
                    </h4>
                    <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                        <i class="ti ti-x fs-8"></i>
                    </div>
                </div>

                <!-- Sidebar navigation -->
                <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                    <ul id="sidebarnav">
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">SISWA</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="index.php">
                                <i class="ti ti-layout-dashboard"></i>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>                                        
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="buku.php">
                                <i class="ti ti-article"></i>
                                <span class="hide-menu">Data Buku</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="peminjaman.php">
                                <i class="ti ti-article"></i>
                                <span class="hide-menu">Riwayat Peminjaman</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="peminjaman_tambah.php">
                                <i class="ti ti-article"></i>
                                <span class="hide-menu">Peminjaman</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="pengembalian.php">
                                <i class="ti ti-article"></i>
                                <span class="hide-menu">Pengembalian</span>
                            </a>
                        </li><li class="sidebar-item">
                            <a class="sidebar-link" href="riwayat_pembayaran.php">
                                <i class="ti ti-article"></i>
                                <span class="hide-menu">Riwayat Pembayaran</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="logout.php">
                                <i class="ti ti-login"></i>
                                <span class="hide-menu">Logout</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Body -->
        <div class="body-wrapper">
            <!-- Header -->
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                            <span class="me-3"><?= htmlspecialchars($_SESSION['siswa_nama']) ?> - Siswa</span>
                            <li class="nav-item dropdown">
                                <a class="nav-link nav-icon-hover" href="#" id="drop2" data-bs-toggle="dropdown">
                                    <img src="<?= $photoURL ?>" alt="" width="35" height="35" class="rounded-circle" />
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                                    <div class="message-body">
                                        <a href="profil.php" class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-user fs-6"></i>
                                            <p class="mb-0 fs-3">My Profile</p>
                                        </a>
                                        <a href="logout.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>

            <!-- Container Start -->
            <div class="container-fluid">
                <style>
                    .form-container {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }
                </style>
