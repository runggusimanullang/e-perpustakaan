<?php
session_start();
include_once("../koneks.php");

 mysqli_query($con, "UPDATE borrowing 
    SET status = 'Belum Kembali'
    WHERE status = 'Dipinjam'
    AND return_date < NOW()
");

if (isset($_GET['cetak'])) {
    require('../fpdf/fpdf.php');

    // Buat PDF
    $pdf = new FPDF('L', 'mm', 'A5'); 
    $pdf->SetAutoPageBreak(true, 10); 
    $pdf->AddPage();

    // Logo dan Judul
    $pdf->Image('C:/xampp/htdocs/perpusada/assets/images/logos/samarfa.jpg', 10, 10, 24); 
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'E-LIBRARY SD SANTA MARIA FATIMA', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Jalan Jatinegara Barat No.122 Bidara Cina, Jatinegara, RT.7/RW.1, Kp. Melayu,', 0, 1, 'C');
    $pdf->Cell(0, 5, 'Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13320', 0, 1, 'C');
    $pdf->Cell(0, 5, 'Telepon: (021) 85902383', 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 7, 'DAFTAR DATA PEMINJAMAN', 0, 1, 'C');
    $pdf->Ln(5); 

    // Header tabel
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(8, 6, 'No', 1, 0, 'C');  
    $pdf->Cell(10, 6, 'ID', 1, 0, 'C');  
    $pdf->Cell(28, 6, 'Peminjam', 1, 0, 'C');  
    $pdf->Cell(35, 6, 'Buku', 1, 0, 'C');  
    $pdf->Cell(24, 6, 'Tgl Pinjam', 1, 0, 'C');  
    $pdf->Cell(24, 6, 'Tenggat Waktu', 1, 0, 'C');  
    $pdf->Cell(24, 6, 'Tgl Kembali', 1, 0, 'C');  
    $pdf->Cell(22, 6, 'Status', 1, 1, 'C');

    // Isi tabel
$pdf->SetFont('Arial', '', 8);
$data_borrow = mysqli_query($con, "SELECT * FROM borrowing ORDER BY id ASC");
$no = 1;

while ($row = mysqli_fetch_assoc($data_borrow)) {
    $borrower = mysqli_fetch_assoc(
        mysqli_query($con, "SELECT fullname FROM siswa WHERE id=".$row['borrower_id'])
    );
    $book = mysqli_fetch_assoc(
        mysqli_query($con, "SELECT title FROM book WHERE id=".$row['book_id'])
    );

    $actual_return_date = $row['actual_return_date']
        ? date('Y-m-d', strtotime($row['actual_return_date'])) 
        : '';

    $returnDateTime = new DateTime($row['return_date']);
    $currentDate = new DateTime();


    // Status logika sesuai kebutuhan
    if ($row['status'] === "Sudah Kembali") {
        $display_status = "Sudah Kembali";
    } elseif ($row['status'] === "Request Peminjaman") {
        $display_status = "Request";
        $return_date = ''; // Kosongkan tanggal tenggat waktu
        $actual_return_date = ''; // Kosongkan tanggal kembali
    } elseif ($row['status'] === "Belum Kembali") {
        if ($currentDate > $returnDateTime) {
            $daysDifference = $currentDate->diff($returnDateTime)->days;
            $display_status = "Terlambat {$daysDifference} hari";
        } else {
            $display_status = "Dipinjam";
        }
    } else {
        $display_status = $row['status'];
    }

    // Tanggal pinjam dan tenggat waktu
    $loan_date = date('Y-m-d', strtotime($row['loan_date']));
    $return_date = ($row['status'] === "Request Peminjaman") ? '' : date('Y-m-d', strtotime($row['return_date']));

    // Isi tabel ke dalam PDF
    $pdf->Cell(8, 6, $no, 1, 0, 'C');  
    $pdf->Cell(10, 6, $row['id'], 1, 0, 'C');  
    $pdf->Cell(28, 6, mb_strimwidth($borrower['fullname'], 0, 18, '...'), 1, 0);  
    $pdf->Cell(35, 6, mb_strimwidth($book['title'], 0, 22, '...'), 1, 0);  
    $pdf->Cell(24, 6, $loan_date, 1, 0, 'C');  
    $pdf->Cell(24, 6, $return_date, 1, 0, 'C');  
    $pdf->Cell(24, 6, $actual_return_date, 1, 0, 'C');  
    $pdf->Cell(22, 6, $display_status, 1, 1, 'C');  

    $no++;
}

    $pdf->Output();
}


if (isset($_POST['konfirms'])) {
    $id = (int)$_POST['id_borrowing'];
    $id_book = (int)$_POST['id_book'];

    // Ambil data buku
    $book_query = mysqli_query($con, "SELECT * FROM book WHERE id=$id_book");
    $book = mysqli_fetch_assoc($book_query);

    // Cek stok
    if (empty($_POST['id_borrowing']) || empty($_POST['id_book'])) {
    $_SESSION['error'] = "Data peminjaman tidak lengkap!";
    echo '<script>window.location.href="index.php";</script>';}
    if ($book['amount'] <= 0) {
        $_SESSION['error'] = "Stok buku habis!";
        echo '<script>window.location.href="index.php";</script>';
        exit;
    }

    // Kurangi stok
    $amountbook = $book['amount'] - 1;
    mysqli_query($con, "UPDATE book SET amount='$amountbook' WHERE id=$id_book");

    // Set tanggal pinjam dan kembali
    $loan_date = date('Y-m-d'); // gunakan tanggal konfirmasi
    $return_date = date('Y-m-d', strtotime($loan_date . ' +2 days'));

    // Status
    $today = new DateTime();
    $returnDateTime = new DateTime($return_date);
    $status = ($today > $returnDateTime) 
              ? "Belum Kembali (Terlambat " . $today->diff($returnDateTime)->days . " hari)" 
              : "Dipinjam";

    // Update DB
    $query = "UPDATE borrowing 
              SET status='$status',
                  loan_date='$loan_date',
                  return_date='$return_date',
                  actual_return_date=NULL
              WHERE id='$id'";

    if (mysqli_query($con, $query)) {
        $_SESSION['success'] = "Peminjaman dikonfirmasi.";
        header('Location: index.php');
        exit;
    } else {
        echo "Gagal: " . mysqli_error($con);
    }
}

 else {
    $_SESSION['navigation'] = "5";
    include_once("../navbar.php");

    if (isset($_POST['return'])) {
        $bookdatas = mysqli_query($con, "SELECT * FROM book WHERE id=$_POST[id_book]"); 
        $books = mysqli_fetch_assoc($bookdatas);
        $amountbook = $books['amount']+1;
        $today = date('Y-m-d');
        $update = mysqli_query($con, "UPDATE borrowing SET status='Sudah Kembali',
        actual_return_date='$today' WHERE id='$_POST[id_borrowing]'");
        if ($update) {
            mysqli_query($con, "UPDATE book SET amount='$amountbook' WHERE id=$_POST[id_book]");
            $_SESSION['success'] = "Berhasil mengembalikan buku";
            echo '<script>window.location.href = "../loan_data/";</script>';
        } else {
            $_SESSION['error'] = "Gagal mengubah data buku";
        }
    }

    if (isset($_POST['delete'])) {
        $id_borrowing = $_POST['id_borrowing'];
        $delete_query = "DELETE FROM borrowing WHERE id = $id_borrowing";
        if (mysqli_query($con, $delete_query)) {
            echo "Data berhasil dihapus";
        } else {
            echo "Error deleting record: " . mysqli_error($con);
        }
    }

?>


<style>
    .bi {
  display: inline-block;
  font-family: "bootstrap-icons" !important;
  font-style: normal;
  font-weight: normal;
  font-size: 1rem;
  line-height: 1;
  vertical-align: -.125em;
}

</style>



<h3 class="">Data Peminjaman</h3>
<div class="row">
    <div class="col-lg-12 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body p-4">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if(!isset($_POST['details'])){ ?>
                <a href="../borrowing/" class="btn btn-primary w-20 py-8 fs-4 mb-4 rounded-1">Peminjaman Baru</a>
                <a href="?cetak" class="btn btn-primary w-20 py-8 fs-4 mb-4 rounded-1 mx-1" target="_blank"><svg
                        xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                        <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                        <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                    </svg></a>
                <a href="?late" class="btn btn-danger w-20 py-8 fs-4 mb-4 rounded-1"><svg
                        xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-time" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                        <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                        <path d="M15 3v4" />
                        <path d="M7 3v4" />
                        <path d="M3 11h16" />
                        <path d="M18 16.496v1.504l1 1" />
                    </svg></a>
                <a href="late_cetak.php" target="_blank" title="Cetak Belum Kembali" style="text-decoration: none; position: relative; top: -12px;">
                    <button style="background-color:#c82333; border: none; padding: 8px 10px; border-radius: 4px; cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="35" height="32" fill="#fff" viewBox="0 0 24 24">
                        <path d="M6 9V2h12v7h2a2 2 0 012 2v6h-4v5H8v-5H4v-6a2 2 0 012-2h2zm2 11h8v-3H8v3zM8 4v3h8V4H8zm10 7a1 1 0 100 2 1 1 0 000-2z"/>
                        </svg>
                    </button>
                    </a>

                    <div style="position: relative;">
                        <a button type="button" class="btn btn-info" style="position: absolute; right: 0; margin-top: -60px;" href="../home/index.php">Dashboard</a>
                    </div>


                <form action="../loan_data/" method="post">
                    <div class="mb-3 " style="display: flex; align-items: center;">
                        <input type="text" class="form-control input" name="value_search"
                            placeholder="Cari berdasarkan nama peminjam dan judul buku" style="flex: 1;">
                        <button name="search" class="btn btn-primary mx-2"><svg xmlns="http://www.w3.org/2000/svg"
                                class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                <path d="M21 21l-6 -6" />
                            </svg></button>
                    </div>
                </form>
                

                <div id="fullnameHelp" class="form-text mx-3">Urutan berdasarkan data terbaru</div>
                    <div class="table-responsive">
                    <table class="table text-nowrap mb-0 align-middle">
                        <thead class="text-dark fs-4">
                         <tr>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">No</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">ID Peminjam</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Nama Peminjam</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Nama Buku</h6>
                                </th>
                                <th class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-0">Tanggal Pinjam</h6>
                                </th>
                                <th class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-0">Tenggat Pengembalian</h6>
                                </th>
                                <th class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-0">Dikembalikan</h6>
                                </th>
                                <th class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-0">Status</h6>
                                </th>
                                <th class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-0">Aksi</h6>
                                </th>
                            </tr>
                        </thead>

                        
                        <tbody>
                            <?php 
                            if(isset($_POST['search'])){
                                $search_title=$_POST['value_search'];
                                $result = mysqli_query($con, "SELECT borrowing.*, book.id AS book_id, book.title AS book_title, siswa.id AS borrower_id, siswa.fullname AS borrower_name FROM borrowing JOIN book ON borrowing.book_id = book.id JOIN siswa ON borrowing.borrower_id = siswa.id WHERE book.title LIKE '%$search_title%' OR siswa.fullname LIKE '%$search_title%' ORDER BY borrowing.id DESC");

                            } else if (isset($_GET['late'])) {
                            $result = mysqli_query(
                                $con, 
                                "SELECT * FROM borrowing 
                                WHERE status = 'Belum Kembali'
                                AND return_date IS NOT NULL
                                AND return_date < NOW()
                                ORDER BY id DESC"
                            );
                            } else {
                                $result = mysqli_query($con, "SELECT * FROM borrowing ORDER BY id DESC");
                            }
                            $iteration = 0;
                            while ($borrowing = mysqli_fetch_array($result)) {
                                $iteration ++;
                            ?>
                            <tr>
                                <td class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0"><?= $iteration ?></h6>
                                </td>
                                <td class="border-bottom-0">
                                    <?php $borrowerdata = mysqli_query($con, "SELECT * FROM siswa WHERE id=$borrowing[borrower_id] "); 
                                    $borrower = mysqli_fetch_assoc($borrowerdata);?>
                                    <h6 class="fw-semibold mb-1"><?= $borrower['id'] ?></h6>
                                </td>
                                <td class="border-bottom-0">                                  
                                    <h6 class="fw-semibold mb-1"><?= $borrower['fullname'] ?></h6>
                                </td>
                                <td class="border-bottom-0">
                                    <?php $bookdata = mysqli_query($con, "SELECT * FROM book WHERE id=$borrowing[book_id] "); 
                                    $book = mysqli_fetch_assoc($bookdata);?>
                                    <h6 class="fw-semibold mb-1"><?= $book['title'] ?></h6>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-1 "><?= $borrowing['loan_date'] ?></h6>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-1">
                                        <?php 
                                        if ($borrowing['status'] === "Request Peminjaman") {
                                            echo "-"; // kosong
                                        } else {
                                            echo $borrowing['return_date'];
                                        }
                                        ?>
                                    </h6>
                                </td>
                                <td class="border-bottom-0 text-center">
                                <h6 class="fw-semibold mb-1">
                                <?php 
                                if ($borrowing['status'] === "Sudah Kembali" && !empty($borrowing['actual_return_date'])) {
                                    echo date('Y-m-d', strtotime($borrowing['actual_return_date']));
                                } else {
                                    echo "-";
                                }
                                ?>
                                </h6>
                                </td>
<td class="border-bottom-0 text-center">
    <div class="d-flex align-items-center gap-2 justify-content-center">
        <?php
        // Tetap ambil return_date & sekarang
        $returnDateTime = new DateTime($borrowing['return_date']);
        $currentDate    = new DateTime();
        $dateDifference = $currentDate->diff($returnDateTime);
        $daysDifference = $dateDifference->days;

        if ($borrowing['status'] === "Sudah Kembali") {
            echo '<span class="badge bg-success rounded-3 fw-semibold" style="width:145px;">Sudah Kembali</span>';
        } elseif ($borrowing['status'] === "Request Peminjaman") {
            echo '<span class="badge bg-info rounded-3 fw-semibold" style="width:145px;">Request Peminjaman</span>';
        } elseif ($borrowing['status'] === "Dipinjam") {
            echo '<span class="badge bg-warning rounded-3 fw-semibold" style="width:145px;">Dipinjam</span>';
        } elseif (strpos($borrowing['status'], "Belum Kembali") !== false) {
            echo '<span class="badge bg-danger rounded-3 fw-semibold" style="width:145px;">'.$borrowing['status'].'</span>';
        }
        else {
            // Jika ada status lain, tampilkan langsung
            echo '<span class="badge bg-secondary rounded-3 fw-semibold" style="width:145px;">'.$borrowing['status'].'</span>';
        }
        ?>
    </div>
</td>
                            <td class="border-bottom-0 text-center">                  
                                <form role="form" method="post" action="../loan_data/">
                                  <input type="hidden" name="id_borrowing" value="<?= $borrowing['id'] ?>">
                                    <input type="hidden" name="id_book" value="<?= $borrowing['book_id'] ?>">
                                    <input type="hidden" name="id_borrower" value="<?= $borrowing['borrower_id'] ?>">



                                    <!-- Tombol sesuai status -->
                                    <?php if ($borrowing['status'] === "Request Peminjaman"): ?>
                                        <button type="submit" name="konfirms" class="btn btn-info me-1" title="Konfirmasi Peminjaman"
                                            onclick="return confirm('Anda yakin ingin mengkonfirmasi peminjaman ini?');">
                                            <!-- Check-circle icon SVG -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.97-3.03a.75.75 0 0 0-1.06 0L7 7.94 5.53 6.47a.75.75 0 1 0-1.06 1.06l2 2a.75.75 0 0 0 1.06 0l3.5-3.5a.75.75 0 0 0 0-1.06z"/>
                                            </svg>
                                        </button>

                                    <?php elseif ($borrowing['status'] === "Belum Kembali" || $borrowing['status'] === "Dipinjam"): ?>
                                        <button type="submit" name="return" class="btn btn-primary me-1" title="Kembalikan Buku" onclick="return confirm('Anda yakin buku sudah dikembalikan?');">
                                            <!-- Arrow-repeat icon SVG -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M2.854 7.146a.5.5 0 1 0-.708.708L4.293 10H2.5a.5.5 0 0 0 0 1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-1 0v1.793L2.854 7.146z"/>
                                                <path d="M13.146 8.854a.5.5 0 0 0 .708-.708L11.707 6H13.5a.5.5 0 0 0 0-1h-3a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 1 0V6.707l2.146 2.147z"/>
                                                <path d="M8 1a7 7 0 1 0 0 14 7 7 0 0 0 0-14zm0 1a6 6 0 1 1 0 12A6 6 0 0 1 8 2z"/>
                                            </svg>
                                        </button>

                                        

                                    <?php else: ?>
                                        <button type="button" class="btn btn-success me-1" disabled title="Sudah Kembali">
                                            <!-- Check icon SVG -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M10.97 4.97a.75.75 0 0 1 1.06 1.06L7.53 10.53a.75.75 0 0 1-1.06 0L3.97 8.03a.75.75 0 1 1 1.06-1.06L7 8.94l3.97-3.97z"/>
                                            </svg>
                                        </button>
                                    <?php endif; ?>

                                    <!-- Detail -->
                                    <button type="submit" name="details" class="btn btn-secondary me-1" title="Lihat Detail">
                                        <!-- Eye icon SVG -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M16 8s-3.582-5.5-8-5.5S0 8 0 8s3.582 5.5 8 5.5S16 8 16 8zM8 3.5c3.038 0 5.507 2.24 6.418 4.5C13.507 10.26 11.038 12.5 8 12.5S2.493 10.26 1.582 8C2.493 5.74 4.962 3.5 8 3.5z"/>
                                            <path d="M8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6zm0 1a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/>
                                        </svg>
                                    </button>

                                    <!-- Delete -->
                                    <button type="submit" name="delete" class="btn btn-danger" title="Hapus Data" onclick="return confirm('Yakin mau hapus data ini?');">
                                        <!-- Trash icon SVG -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1-.5-.5z"/>
                                            <path d="M3.5 2a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1V3h1a.5.5 0 0 1 0 1h-.5v9a2 2 0 0 1-2 2h-7a2 2 0 0 1-2-2V4H1.5a.5.5 0 0 1 0-1h1V2zm1 2v9a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V4h-9zM5 2v1h6V2H5z"/>
                                        </svg>
                                    </button>
                                </form>
                                </div>
                            </td>
                            </tr>
                            
                            <?php 
                            } 
                            if($iteration===0){?>
                            <td class="border-bottom-0 text-center" colspan="6">
                                <h3 class="fw-semibold mb-0 text-center">Data peminjaman tidak ditemukan</h3>
                            </td>

                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php }
              
              if(isset($_POST['details'])){ 
                    $detailsborrowing = mysqli_query($con, "SELECT * FROM borrowing WHERE id=$_POST[id_borrowing] ");  
                    $details = mysqli_fetch_array($detailsborrowing); 
                ?>
                <h5 class="card-title fw-semibold mb-4">Detail Peminjaman</h5>
                <div class="card">
                    <div class="card-body">

                        <div class="mb-3 d-flex">
                            <h4 class="fw-semibold mb-0 fs-6 col-3 mx-4 px-4">Peminjam </h4>
                            <?php $borrowerdata = mysqli_query($con, "SELECT * FROM siswa WHERE id=$details[borrower_id] "); 
                                      $borrower = mysqli_fetch_assoc($borrowerdata);?>
                            <h4 class="fw-semibold mb-0 fs-6 col-3"> Buku yang dipinjam</h4>
                        </div>
                        <div class="mb-3 d-flex">
                            <?php $bookdata = mysqli_query($con, "SELECT * FROM book WHERE id=$details[book_id] "); 
                                    $book = mysqli_fetch_assoc($bookdata);?>
                            <?php if(isset($borrower['photo_filename'])){ ?>
                            <img class="img-thumbnail rounded-circle col-6" alt="Preview"
                                src="../../assets/images/siswa/<?= $borrower['photo_filename'] ?>"
                                style="width: 200px; height: 200px;">
                            <?php } else { ?>
                            <img class="img-thumbnail rounded-circle col-6" alt="Preview"
                                src="../../assets/images/profile/user-1.jpg" style="width: 200px; height: 200px;">
                            <?php } ?>
                            <h4 class="col-1"></h4>
                            <?php if(isset($book['photo_filename'])){ ?>
                            <img class="img-thumbnail mx-4" alt="Preview"
                                src="../../assets/images/book/<?= $book['photo_filename'] ?>"
                                style="width: 200px; height: 200px;">
                            <?php } else { ?>
                            <img class="img-thumbnail mx-4" alt="Preview" src="../../assets/images/book/default.png"
                                style="width: 200px; height: 200px;">
                            <?php } ?>
                        </div>
                        
                        <div class="mb-5 d-flex">

                            <h4 class="fw-semibold mb-0 fs-6 col-3 mx-4 px-4"> <?= $borrower['fullname'] ?> </h4>
                            <h4 class="fw-semibold mb-0 fs-6 col-3"><?= $book['title'] ?></h4>
                        </div>
                        <div class="mb-3 d-flex">
                            <h4 class="fw-semibold mb-0 fs-6 col-3"> Tanggal Pinjam </h4>
                            <h4 class="fw-semibold mb-0 fs-6 col-1"> : </h4>
                            <h4 class="fw-semibold mb-0 fs-6 col-6"><?= $details['loan_date'] ?></h4>
                        </div>

                        <div class="mb-3 d-flex">
                            <h4 class="fw-semibold mb-0 fs-6 col-3"> Tenggat waktu Kembali </h4>
                            <h4 class="fw-semibold mb-0 fs-6 col-1"> : </h4>
                            <h4 class="fw-semibold mb-0 fs-6 col-6">
                                <?= $details['status']==='Request Peminjaman' ? '' : $details['return_date'] ?>
                            </h4>
                        </div>

                        <div class="mb-3 d-flex">
                            <h4 class="fw-semibold mb-0 fs-6 col-3"> Tanggal Kembali </h4>
                            <h4 class="fw-semibold mb-0 fs-6 col-1"> : </h4>
                            <h4 class="fw-semibold mb-0 fs-6 col-6">
                                <?php
                                // Jika status bukan Request Peminjaman dan bukan Belum Kembali
                                if ($details['status'] === "Sudah Kembali" && $details['actual_return_date'] !== '0000-00-00 00:00:00' && !empty($details['actual_return_date'])) {
                                    echo date('Y-m-d', strtotime($details['actual_return_date']));
                                } else {
                                    echo '';
                                }
                                ?>
                            </h4>
                        </div>

                        <div class="mb-3 d-flex">
                            <h4 class="fw-semibold mb-0 fs-6 col-3 "> Status</h4>
                            <h4 class="fw-semibold mb-0 fs-6 col-1 "> : </h4>
                            <h4 class="fw-semibold mb-0 fs-6 col-6"> <?= $details['status'] ?>

                              <?php
                                if ($details['status'] === "Belum Kembali") {
                                    $returnDateTime = new DateTime($details['return_date']);
                                    $currentDate = new DateTime();
                                    if ($currentDate > $returnDateTime) {
                                        $dateDifference = $currentDate->diff($returnDateTime);
                                        $daysLate = $dateDifference->days;
                                        echo "<p class='text-danger'>( Terlambat $daysLate hari )</p>";
                                    }
                                }
                                ?>
                                

</h4>
</div>
</div>
</div>

<?php if($details['status']==='Request Peminjaman'): ?>
    <form method="post" style="display: inline;" onsubmit="return confirm('Konfirmasi permintaan ini?')">
        <input type="hidden" name="id_borrowing" value="<?= $details['id'] ?>">
        <input type="hidden" name="id_book" value="<?= $details['book_id'] ?>">
        <button type="submit" name="konfirms" class="btn btn-primary btn-sm">Konfirmasi Peminjaman</button>
    </form>

<?php elseif($details['status']==='Belum Kembali' || $details['status']==='Dipinjam'): ?>
    <form method="post" style="display: inline;" onsubmit="return confirm('Yakin ingin mengembalikan buku sekarang?');">
        <input type="hidden" name="id_borrowing" value="<?= $details['id'] ?>">
        <input type="hidden" name="id_book" value="<?= $details['book_id'] ?>">
        <button type="submit" name="return" class="btn btn-success btn-sm">Kembalikan Buku</button>
    </form>

<?php else: ?>
    <!-- Jika sudah kembali -->
    <a href="../loan_data/" class="btn btn-secondary btn-sm">← Kembali</a>

<?php endif; ?>







                <?php }?>
            </div>
        </div>
    </div>
</div>


<?php
include_once("../footer.php");
                            }
?>

