<?php
include_once("../koneks.php");
if (!isset($_SESSION['login'])) {

      $_SESSION['warning'] = "Silahkan login terlebih dahulu untuk mengakses halaman dashboard";
  header('Location: ../login.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>E-Perpustakaan SD Santa Maria Fatima</title>
    <link rel="shortcut icon" type="image/png" href="../../assets/images/logos/logoNav.png" />
    <link rel="stylesheet" href="../../assets/css/styles.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" integrity="sha512-QZXHpScT6U0JuDzTVK/j3cAgUbl+73eBgkSh8e0UY8eHNWwwlPYsU7NYdPVWcgeUJcPSN1mG0cPUpK5/NyMu5w==" crossorigin="anonymous" referrerpolicy="no-referrer" />


</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div>
                <div class="brand-logo d-flex align-items-center justify-content-between">
                    <h4 class="text-center">
                        <img src="../../assets/images/logos/logoNav.png" width="30" alt="" class="mb-2">
                        E-Perpus SaMarFa
                    </h4>
                    <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                        <i class="ti ti-x fs-8"></i>
                    </div>
                </div>
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                    <ul id="sidebarnav">
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Home</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link <?php if(isset($_SESSION['navigation'])&&$_SESSION['navigation'] === "1"){echo "bg-primary text-white";}?>"
                                href="../home/" aria-expanded="false">
                                <span>
                                    <i class="ti ti-layout-dashboard"></i>
                                </span>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Master Data</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link <?php if(isset($_SESSION['navigation'])&&$_SESSION['navigation'] === "2"){echo "bg-primary text-white";}?>"
                                href="../siswa" aria-expanded="false">
                                <span>
                                    <i class="ti ti-article"></i>
                                </span>
                                <span class="hide-menu">Data Anggota</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link <?php if(isset($_SESSION['navigation'])&&$_SESSION['navigation'] === "3"){echo "bg-primary text-white";}?>"
                                href="../book" aria-expanded="false">
                                <span>
                                    <i class="ti ti-article"></i>
                                </span>
                                <span class="hide-menu">Data Buku</span>
                            </a>
                        </li>

                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Transaksi</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link <?php if($_SESSION['navigation'] === "6"){echo "bg-primary text-white";}?>"
                                href="../loan_data/request.php" aria-expanded="false">
                                <span>
                                    <i class="ti ti-article"></i>
                                </span>
                                <span class="hide-menu">Request Peminjaman</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link <?php if($_SESSION['navigation'] === "4"){echo "bg-primary text-white";}?>"
                                href="../borrowing" aria-expanded="false">
                                
                                <span>  
                                <i class="ti ti-article"></i>
                                </span>
                                <span class="hide-menu">Peminjaman</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link <?php if($_SESSION['navigation'] === "5"){echo "bg-primary text-white";}?>"
                                href="../loan_data" aria-expanded="false">
                                <span>
                                    <i class="ti ti-article"></i>
                                </span>
                                <span class="hide-menu">Data Peminjaman</span>
                            </a>
                        </li><li class="sidebar-item">
                            <a class="sidebar-link <?php if($_SESSION['navigation'] === "8"){echo "bg-primary text-white";}?>"
                                href="../pembayaran/bills.php" aria-expanded="false">
                                <span>
                                    <i class="ti ti-article"></i>
                                </span>
                                <span class="hide-menu">Pembayaran</span>
                            </a>
                        </li>

                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Laporan</span>
                        </li>
                         <li class="sidebar-item">
                            <a class="sidebar-link <?php if($_SESSION['navigation'] === "7"){echo "bg-primary text-white";}?>"
                                href="../laporan" aria-expanded="false">
                                <span>
                                    <i class="ti ti-file"></i>
                                </span>
                                <span class="hide-menu">Laporan Peminjaman</span>
                            </a>
                        </li><li class="sidebar-item">
                            <a class="sidebar-link <?php if($_SESSION['navigation'] === "9"){echo "bg-primary text-white";}?>"
                                href="../pembayaran/financial_report.php" aria-expanded="false">
                                <span>
                                    <i class="ti ti-file"></i>
                                </span>
                                <span class="hide-menu">Laporan Keuangan</span>
                            </a>
                        </li>


                         <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">admin</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link " href="../../admin/logout.php" aria-expanded="false">
                                <span>
                                    <i class="ti ti-login"></i>
                                </span>
                                <span class="hide-menu">Logout</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <?php
                        if (isset($_SESSION['success'])) { echo '
                        <div class="alert alert-success col-8 mx-auto text-center p-2 border rounded text-center">' . $_SESSION['success'] . '</div>
                        '; unset($_SESSION['success']); } 
                    ?>

                    <?php 
                    $id = $_SESSION['admin_id'];
                    $cek = mysqli_query($con,"select * from admin where id='$id'");
                    $c = mysqli_fetch_assoc($cek);
                     ?>
                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                            <li class="nav-item dropdown">
                                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php 
                                    if($c['foto'] ==""){
                                        ?>
                                        <img src="../../assets/images/profile/user-1.jpg" alt="" width="35" height="35"
                                        class="rounded-circle" />
                                        <?php
                                    }else{
                                         ?>
                                        <img src="../../assets/images/admin/<?php echo $c['foto'] ?>" alt="" width="35" height="35"
                                        class="rounded-circle" />
                                        <?php
                                    }
                                     ?>
                                  
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                                    aria-labelledby="drop2">
                                    <div class="message-body">
                                        <a href="../profil"
                                            class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-user fs-6"></i>
                                            <p class="mb-0 fs-3">My Profile</p>
                                        </a>
                                        <a href="../../admin/logout.php"
                                            class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!--  Header End -->
            <div class="container-fluid">
                <style>
                .form-container {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                </style>