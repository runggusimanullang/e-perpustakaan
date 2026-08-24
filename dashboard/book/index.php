<?php
session_start();
if (isset($_GET['cetak'])) {
    include_once("../koneks.php");
    require('../fpdf/fpdf.php');
    
    $pdf = new FPDF('L', 'mm', 'A5');
    $pdf->AddPage();
    $pdf->Image('C:/xampp/htdocs/perpusada/assets/images/logos/samarfa.jpg', 10, 10, 32);
    
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetY(14);
    $pdf->Cell(190, 7, 'E-LIBRARY SD SANTA MARIA FATIMA', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Jalan Jatinegara Barat No.122 Bidara Cina, Jatinegara, RT.7/RW.1, Kp. Melayu,', 0, 1, 'C');
    $pdf->Cell(0, 5, 'Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13320', 0, 1, 'C');
    $pdf->Cell(0, 5, 'Telepon: (021) 85902383', 0, 1, 'C');
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetY(44);
    $pdf->Cell(190, 7, 'DAFTAR DATA BUKU', 0, 1, 'C');
    
    $pdf->Cell(10, 10, '', 0, 1); // Space
    $pdf->SetFont('Arial', 'B', 10);
    
    // Header tabel
    $pdf->Cell(10, 6, 'No', 1, 0, 'C');
    $pdf->Cell(40, 6, 'Judul', 1, 0);
    $pdf->Cell(40, 6, 'Pengarang', 1, 0);
    $pdf->Cell(40, 6, 'Penerbit', 1, 0);
    $pdf->Cell(28, 6, 'ISBN', 1, 0, 'C');
    $pdf->Cell(25, 6, 'Jumlah', 1, 0, 'C');
    $pdf->Cell(30, 6, 'Harga', 1, 1, 'C');
    
    $pdf->SetFont('Arial', '', 10);
    $data_book = mysqli_query($con, "SELECT * FROM book");
    $hitung = 1;
    
    while ($row = mysqli_fetch_array($data_book)) {
        $pdf->Cell(10, 6, $hitung, 1, 0, 'C');
        $pdf->Cell(40, 6, $row['title'], 1, 0);
        $pdf->Cell(40, 6, $row['adminor'], 1, 0);
        $pdf->Cell(40, 6, $row['publisher'], 1, 0);
        $pdf->Cell(28, 6, $row['isbn'], 1, 0, 'C');
        $pdf->Cell(25, 6, $row['amount'], 1, 0, 'C');
        $pdf->Cell(30, 6, 'Rp ' . number_format($row['price'], 0, ',', '.'), 1, 1, 'C');
        $hitung++;
        
        // Cek jika sudah mencapai batas halaman
        if ($hitung % 13 == 0) {
            $pdf->AddPage(); // Tambah halaman baru
            $pdf->SetY(14);
            $pdf->Cell(190, 7, 'E-LIBRARY SD SANTA MARIA FATIMA', 0, 1, 'C');
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetY(24);
            $pdf->Cell(190, 7, 'DAFTAR DATA BUKU', 0, 1, 'C');
            $pdf->Cell(10, 10, '', 0, 1); // Space
            $pdf->SetFont('Arial', 'B', 10);
            // Header tabel
            $pdf->Cell(10, 6, 'No', 1, 0, 'C');
            $pdf->Cell(40, 6, 'Judul', 1, 0);
            $pdf->Cell(40, 6, 'Pengarang', 1, 0);
            $pdf->Cell(40, 6, 'Penerbit', 1, 0);
            $pdf->Cell(28, 6, 'ISBN', 1, 0, 'C');
            $pdf->Cell(25, 6, 'Jumlah', 1, 0, 'C');
            $pdf->Cell(30, 6, 'Harga', 1, 1, 'C');
            $pdf->SetFont('Arial', '', 10);
        }
    }
    
    // Menambahkan nomor halaman
    $pdf->SetY(-15); // Posisi Y untuk nomor halaman
    $pdf->SetFont('Arial', 'I', 8);
    
    $pdf->Output();
} else {
    $_SESSION['navigation'] = "3";
    include_once("../navbar.php");
    if (isset($_POST['create'])) {
        $title = $_POST['title'];
        $adminor = $_POST['adminor'];
        $publisher = $_POST['publisher'];
        $amount = $_POST['amount'];
        $price = $_POST['price']; // Tambahkan ini
        $tahun = $_POST['tahun'];
        $isbn = $_POST['isbn'];
        $no_rak = $_POST['no_rak'];
        $category = $_POST['category'];
        $description = $_POST['description'];
        $allowedPhotoExtensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp');
        $uploadedExtension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowedPdfExtensions = ['pdf'];
        $uploadedPdfExtension = strtolower(pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION));
        $error = false;
        $errtitle = false;
        $erradminor = false;
        $errpublisher = false;
        $erramount = false;
        $errprice = false; // Tambahkan ini
        $errtahun = false;
        $errisbn = false;
        $errnorak = false;
        $errcategory = false;
        $errphoto = false;
        $errdescription = false;
        $errpdf = false;
        
        // Validasi Inputan 
        if (preg_match('/[<>]/', $title) || preg_match('/[;\'"()|&%*$^]/', $title)) {
            $error = true;
            $errtitle = true;
        }
        if (!preg_match('/^[a-zA-Z\s.]+$/', $adminor) || preg_match('/[<>]/', $adminor) || preg_match('/[;\'"()|&%*$^]/', $adminor)) {
            $error = true;
            $erradminor = true;
        }
        if (!preg_match('/^[a-zA-Z\s.]+$/', $publisher) || preg_match('/[<>]/', $publisher) || preg_match('/[;\'"()|&%*$^]/', $publisher)) {
            $error = true;
            $errpublisher = true;
        }
        
        if (strlen($amount) > 3) {
            $error = true;
            $erramount = true;
        }
        
        // Validasi harga
        if (!is_numeric($price) || $price < 0) {
            $error = true;
            $errprice = true;
        }
        
        if (!in_array($uploadedExtension, $allowedPhotoExtensions)) {
            $error = true;
            $errphoto = true;
        }
        
        // Cek jika file PDF valid
        if (!in_array($uploadedPdfExtension, $allowedPdfExtensions)) {
            $_SESSION['error'] = "Hanya menerima file PDF.";
            header("Location: ../book/?tambah");
            exit();
        }
        
        // Validasi ISBN
        if (!preg_match('/^\d{13}$/', $isbn)) {
            $_SESSION['error'] = "ISBN harus terdiri dari 13 digit angka!";
            header("Location: ?tambah");
            exit;
        }
        
        // Check Hasil validasi
        if ($error === true) {
            $_SESSION['error'] = "Ada kesalahan pada data yang diinputkan :";
            if ($errtitle === true) {
                $_SESSION['error'] .= "<br> - Judul buku tidak boleh mengandung karakter HTML atau Query SQL.";
            }
            if ($erradminor === true) {
                $_SESSION['error'] .= "<br> - Nama pengarang hanya boleh mengandung huruf, spasi dan titik.";
            }
            if ($errpublisher === true) {
                $_SESSION['error'] .= "<br> - Nama penerbit hanya boleh mengandung huruf, spasi dan titik.";
            }
            if ($erramount === true) {
                $_SESSION['error'] .= "<br> - Jumlah buku tidak boleh lebih dari 3 digit.";
            }
            if ($errprice === true) {
                $_SESSION['error'] .= "<br> - Harga buku harus berupa angka yang valid.";
            }
            if ($errtahun === true) {
                $_SESSION['error'] .= "<br> - Tahun buku tidak boleh lebih dari 4 digit.";
            }
            if ($errisbn === true){
                $_SESSION['error'] .= "<br> - Nomor ISBN tidak boleh lebih dan kurang dari 13 .";
            }
            if ($errpdf === true) {
                $_SESSION['error'] .= "<br> - Hanya menerima file PDF.";
            }
            if ($errphoto === true) {
                $_SESSION['error'] .= "<br> - Ekstensi file foto tidak diizinkan. Silakan upload file dengan ekstensi: " . implode(', ', $allowedPhotoExtensions);
            }
            echo '<script type="text/javascript">window.location.href = "../book/?tambah";</script>';
        } else {
            // Upload photo
            $targetDirectory = "../../assets/images/book/";
            // Create the directory if it doesn't exist
            if (!is_dir($targetDirectory)) {
                mkdir($targetDirectory, 0755, true);
            }           
            $currentTime = date('Ymd_His'); // Format: YYYYMMDD_HHMMSS
            $photoFileName = $title . '_' . $currentTime . '.' . pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $targetFilePath = $targetDirectory . $photoFileName;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFilePath)) {
                $_SESSION['success'] = "Berhasil menambah data buku baru";
                echo '<script type="text/javascript">window.location.href = "../book";</script>';
            } else {
                $_SESSION['error'] = "Gagal mengupload foto. Silakan coba lagi.";
            }
            
            $pdf = $_FILES['pdf'];
           
            if ($pdf['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = "Gagal mengupload file PDF.";
                echo '<script type="text/javascript">window.location.href = "../book/?tambah";</script>';
                exit;
            }
        
            $uploadedPdfExtension = strtolower(pathinfo($pdf['name'], PATHINFO_EXTENSION));
            
            // Validasi PDF
            if (!in_array($uploadedPdfExtension, $allowedPdfExtensions)) {
                $error = true;
                $errpdf = true;
            }
        
            // Cek hasil validasi
            if ($error === true) {
                // ... kode untuk menangani kesalahan ...
            } else {
                // Upload photo
                // ... kode untuk upload foto ...
        
                // Upload PDF
                $targetDirPdf = "../../assets/pdf/";
                if (!is_dir($targetDirPdf)) {
                    mkdir($targetDirPdf, 0755, true);
                }
                $pdfFileName = $title . '_' . time() . '.' . $uploadedPdfExtension;
                $targetFilePathPdf = $targetDirPdf . $pdfFileName;
        
                // Setelah meng-upload file PDF
                if (move_uploaded_file($_FILES['pdf']['tmp_name'], $targetFilePathPdf)) {
                    // Simpan data buku ke database
                    $pdfFileName = $title . '_' . time() . '.' . $uploadedPdfExtension; // Menghasilkan nama file PDF
                    $createbook = mysqli_query($con, "INSERT INTO book (title, adminor, publisher, amount, price, category, description, photo_filename, pdf_filename, nomor_rak_buku, tahun, isbn) VALUES ('$title','$adminor', '$publisher', '$amount', '$price', '$category','$description', '$photoFileName', '$pdfFileName', '$no_rak','$tahun', '$isbn')");
                    
                    if ($createbook) {
                        $_SESSION['success'] = "Berhasil menambah data buku baru";
                        header("Location: ../book");
                    } else {
                        $_SESSION['error'] = "Gagal menambah data buku.";
                        header("Location: ../book/?tambah");
                    }
                } else {
                    $_SESSION['error'] = "Gagal mengupload file PDF.";
                    header("Location: ../book/?tambah");
                }
            }
        }
    }
    
    function saveBook($con, $title, $adminor, $publisher, $amount, $price, $category, $description, $photoFileName, $pdfFileName, $no_rak, $tahun, $isbn) {
        return mysqli_query($con, "INSERT INTO book (title, adminor, publisher, amount, price, category, description, photo_filename, pdf_filename, nomor_rak_buku, tahun, isbn) VALUES ('$title','$adminor', '$publisher', '$amount', '$price', '$category','$description', '$photoFileName', '$pdfFileName', '$no_rak','$tahun', '$isbn')");
    }

    if (isset($_POST['update'])) {
        $id_update = $_POST['id_book_update'];
        $title = $_POST['title'];
        $adminor = $_POST['adminor'];
        $publisher = $_POST['publisher'];
        $amount = $_POST['amount'];
        $price = $_POST['price']; // Tambahkan ini
        $tahun = $_POST['tahun'];
        $isbn = $_POST['isbn'];
        $category = $_POST['category'];
        $no_rak = $_POST['no_rak'];
        $description = $_POST['description'];
        $allowedPhotoExtensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp');
        $allowedPdfExtensions = ['pdf'];
        $error = false;
        
        // Validasi
        if (preg_match('/[<>]/', $title) || preg_match('/[;\'"()|&%*$^]/', $title)) {
            $error = true;
        }
        if (!preg_match('/^[a-zA-Z\s.]+$/', $adminor) || preg_match('/[<>]/', $adminor) || preg_match('/[;\'"()|&%*$^]/', $adminor)) {
            $error = true;
        }
        if (!preg_match('/^[a-zA-Z\s.]+$/', $publisher) || preg_match('/[<>]/', $publisher) || preg_match('/[;\'"()|&%*$^]/', $publisher)) {
            $error = true;
        }
        if (strlen($amount) > 3) {
            $error = true;
        }
        
        // Validasi harga
        if (!is_numeric($price) || $price < 0) {
            $error = true;
        }
        
        if ($error) {
            $_SESSION['error'] = "Ada kesalahan pada data yang diinputkan.";
            echo '<script type="text/javascript">window.location.href = "../book/?tambah";</script>';
            exit;
        }
        
        // Ambil data lama
        $getData = mysqli_query($con, "SELECT photo_filename, pdf_filename FROM book WHERE id = '$id_update'");
        $oldData = mysqli_fetch_assoc($getData);
        $oldPhotoFilename = $oldData['photo_filename'];
        $oldPdfFilename = $oldData['pdf_filename'];
        $targetDirectory = "../../assets/images/book/";
        $targetDirPdf = "../../assets/pdf/";
        
        // Default gunakan file lama
        $photoFileName = $oldPhotoFilename;
        $pdfFileName = $oldPdfFilename;
        
        // Jika ada file foto baru
        if ($_FILES['photo']['name']) {
            $uploadedExtension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            if (in_array($uploadedExtension, $allowedPhotoExtensions)) {
                $currentTime = date('Ymd_His');
                $photoFileName = $title . '_' . $currentTime . '.' . $uploadedExtension;
                $targetFilePath = $targetDirectory . $photoFileName;
                if (!is_dir($targetDirectory)) {
                    mkdir($targetDirectory, 0755, true);
                }
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFilePath)) {
                    // Hapus file lama
                    $photoPath = $targetDirectory . $oldPhotoFilename;
                    if (file_exists($photoPath) && is_file($photoPath)) {
                        unlink($photoPath);
                    }
                } else {
                    $_SESSION['error'] = "Gagal mengupload foto.";
                    echo '<script type="text/javascript">window.location.href = "../book/?tambah";</script>';
                    exit;
                }
            } else {
                $_SESSION['error'] = "Ekstensi foto tidak valid.";
                echo '<script type="text/javascript">window.location.href = "../book/?tambah";</script>';
                exit;
            }
        }
        
        // Jika ada file PDF baru
        if ($_FILES['pdf']['name']) {
            $uploadedPdfExtension = strtolower(pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION));
            if (in_array($uploadedPdfExtension, $allowedPdfExtensions)) {
                $pdfFileName = $title . '_' . time() . '.' . $uploadedPdfExtension;
                $targetFilePathPdf = $targetDirPdf . $pdfFileName;
                if (!is_dir($targetDirPdf)) {
                    mkdir($targetDirPdf, 0755, true);
                }
                if (move_uploaded_file($_FILES['pdf']['tmp_name'], $targetFilePathPdf)) {
                    // Hapus file lama
                    $oldPdfPath = $targetDirPdf . $oldPdfFilename;
                    if (file_exists($oldPdfPath) && is_file($oldPdfPath)) {
                        unlink($oldPdfPath);
                    }
                } else {
                    $_SESSION['error'] = "Gagal mengupload file PDF.";
                    echo '<script type="text/javascript">window.location.href = "../book/?tambah";</script>';
                    exit;
                }
            } else {
                $_SESSION['error'] = "Hanya menerima file PDF.";
                echo '<script type="text/javascript">window.location.href = "../book/?tambah";</script>';
                exit;
            }
        }
        
        // Validasi ISBN
        if (!preg_match('/^\d{13}$/', $isbn)) {
            $_SESSION['error'] = "ISBN harus terdiri dari 13 digit angka!";
            header("Location: ?edit&id_book=".$_POST['id_book_update']);
            exit;
        }
        
        // Update data
        $update = mysqli_query($con, "UPDATE book SET title= '$title', adminor = '$adminor', publisher = '$publisher', amount = '$amount', price = '$price', category = '$category', description = '$description', photo_filename='$photoFileName', pdf_filename='$pdfFileName', nomor_rak_buku='$no_rak', tahun='$tahun', isbn='$isbn' WHERE id = '$id_update'");
        if ($update) {
            $_SESSION['success'] = "Berhasil mengubah data buku";
            echo '<script>window.location.href = "../book/";</script>';
        } else {
            $_SESSION['error'] = "Gagal mengubah data buku";
            echo '<script type="text/javascript">window.location.href = "../book/?tambah";</script>';
        }
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['id_book'];
        try {
            // Fetch the photo filename from the database
            $getPhotoFilename = mysqli_query($con, "SELECT photo_filename FROM book WHERE id = '$id'");
            $row = mysqli_fetch_assoc($getPhotoFilename);
            $photoFilename = $row['photo_filename'];
            // Ambil nama file PDF dari database
            $getPdfFilename = mysqli_query($con, "SELECT pdf_filename FROM book WHERE id = '$id'");
            $row = mysqli_fetch_assoc($getPdfFilename);
            $pdfFilename = $row['pdf_filename'];
            // Cobalah untuk menjalankan query penghapusan
            $delete_book = mysqli_query($con, "DELETE FROM book WHERE id = '$id'");
            if ($delete_book) {
                // Delete the associated photo file
                $photoPath = "../../assets/images/book/" . $photoFilename;
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
                 // Hapus file PDF yang terkait
                 $pdfPath = "../../assets/pdf/" . $pdfFilename;
                 if (file_exists($pdfPath)) {
                     unlink($pdfPath);
                 }
                $_SESSION['success'] = "Berhasil menghapus data buku";
                echo '<script>window.location.href = "../book";</script>';
            } else {
                throw new Exception("Gagal menghapus data buku");
            }
        } catch (Exception $e) {
            // Tangkap kesalahan dan tetapkan pesan sesuai
            $_SESSION['error'] = "Gagal menghapus data buku karena terhubung dengan data peminjaman";
            echo '<script>window.location.href = "../book";</script>';
        }
    }
?>
<h3 class="">Data Buku</h3>
<div class="row">
    <div class="col-lg-12 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body p-4">
                <?php 
                if (isset($_POST['delete'])) {
                    if (isset($_SESSION['error'])) { 
                        echo '<div class="alert alert-danger col-8 mx-auto text-center p-2 border rounded text-center">' . $_SESSION['error'] . '</div>'; 
                        unset($_SESSION['error']); 
                    } 
                }
                if (!isset($_GET['tambah']) && !isset($_POST['edit']) && !isset($_POST['details'])) { ?>
                <a href="?tambah" class="btn btn-primary w-20 py-8 fs-4 mb-4 rounded-1">Tambah Buku Perpustakaan</a>
                <a href="?cetak" class="btn btn-primary w-20 py-8 fs-4 mb-4 rounded-1 mx-1" target="_blank"><svg
                        xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                        <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                        <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                    </svg></a>
                    <div style="position: relative;">
                        <a button type="button" class="btn btn-info" style="position: absolute; right: 0; margin-top: -60px;" href="../home/index.php">Dashboard</a>
                    </div>
                <form action="../book/" method="post">
                    <div class="mb-3 " style="display: flex; align-items: center;">
                        <input type="text" class="form-control input" name="title_search"
                            placeholder="Cari berdasarkan judul buku" style="flex: 1;">
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
                <div class="table-responsive">
                    <table class="table text-nowrap mb-0 align-middle">
                        <thead class="text-dark fs-4">
                            <tr>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">No</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Judul</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Pengarang</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Penerbit</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">ISBN</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Nomor Rak</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0 text-center">Jumlah</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0 text-center">Harga</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0 text-center">Aksi</h6>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (isset($_POST['search'])) {
                                $search_title = $_POST['title_search'];
                                $result = mysqli_query($con, "SELECT * FROM book WHERE title LIKE '%$search_title%'");
                            } else {
                                $result = mysqli_query($con, "SELECT * FROM book");
                            }
                            $iteration = 0;
                            while ($buku = mysqli_fetch_array($result)) {
                                $iteration++;
                            ?>
                            <tr>
                                <td class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0"><?= $iteration ?></h6>
                                </td>
                                <td class="border-bottom-0">
                                    <h6 class="fw-semibold mb-1"><?= $buku['title'] ?> (<?= $buku['tahun'] ?>)</h6>
                                    <small>Kategori : <?= $buku['category'] ?></small>
                                </td>
                                <td class="border-bottom-0">
                                    <p class="mb-0 fw-normal"><?= isset($buku['adminor']) ? $buku['adminor'] : 'Nilai Default' ?></p>
                                </td>
                                <td class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0 fs-4"><?= $buku['publisher'] ?></h6>
                                </td>
                                <td class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0"><?= $buku['isbn'] ?></h6>
                                </td>
                                <td class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0 fs-4"><?= $buku['nomor_rak_buku'] ?></h6>
                                </td>
                                <td class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0 fs-4 text-center"><?= $buku['amount'] ?></h6>
                                </td>
                                <td class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0 fs-4 text-center">Rp <?= number_format($buku['price'], 0, ',', '.') ?></h6>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <form role="form" method="post" action="../book/">
                                        <input type="hidden" name="id_book" value="<?= $buku['id'] ?>">
                                        <button type="submit" name="edit" class="btn btn-sm btn-primary"><svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-pencil" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                <path d="M13.5 6.5l4 4" />
                                            </svg></button>
                                        <button type="submit" name="delete" class="btn btn-sm btn-danger mx-1"><svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-trash" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a 2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                            </svg></button>
                                        <button type="submit" name="details" class="btn btn-sm btn-secondary"><svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-eye" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                            </svg></button>
                                    </form>
                                </td>
                            </tr>
                            <?php 
                            }
                            
                            if ($iteration === 0) { ?>
                            <td class="border-bottom-0 text-center" colspan="7">
                                <h3 class="fw-semibold mb-0 text-center">Buku tidak ditemukan</h3>
                            </td>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php } 
                if (isset($_GET['tambah']) && !isset($_POST['edit'])) { ?>
                <h5 class="card-title fw-semibold mb-4">Forms Tambah</h5>
                <?php 
                    if (isset($_GET['tambah'])) {
                        if (isset($_SESSION['error'])) { 
                            echo '<div class="alert alert-danger col-8 mx-auto text-center p-2 border rounded text-center">' . $_SESSION['error'] . '</div>'; 
                            unset($_SESSION['error']); 
                        } 
                    }
                ?>
                <div class="card">
                    <div class="card-body">
                        <form role="form" method="post" action="./index.php" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Foto</label>
                                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
                                    <div id="photoHelp" class="form-text">Hanya menerima foto ber ekstensi ('jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp')</div>
                                </div>
                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul Buku</label>
                                    <input type="text" class="form-control" id="title" placeholder="Masukkan judul buku" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="pdf" class="form-label">Upload PDF</label>
                                    <input type="file" class="form-control" id="pdf" name="pdf" accept=".pdf" required>
                                    <div id="pdfHelp" class="form-text">Hanya menerima file PDF.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="adminor" class="form-label">Pengarang</label>
                                    <input type="text" class="form-control" id="adminor" placeholder="Masukkan nama pengarang" name="adminor" required>
                                </div>
                                <div class="mb-3">
                                    <label for="publisher" class="form-label">Penerbit</label>
                                    <input type="text" class="form-control" id="publisher" placeholder="Masukkan nama penerbit" name="publisher" required>
                                </div>
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Jumlah</label>
                                    <input type="number" class="form-control" id="amount" placeholder="1" name="amount" required>
                                </div>
                                <div class="mb-3">
                                    <label for="price" class="form-label">Harga Buku</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="price" placeholder="0" name="price" required min="0" step="1000">
                                    </div>
                                    <div id="priceHelp" class="form-text">Masukkan harga buku dalam Rupiah</div>
                                </div>
                                <div class="mb-3">
                                    <label for="tahun" class="form-label">Tahun Terbit</label>
                                    <input type="year" class="form-control" id="tahun" placeholder="misal : 2020" name="tahun" required>
                                </div>
                                <div class="mb-3">
                                    <label for="isbn" class="form-label">ISBN</label>
                                    <input type="tel" class="form-control" id="isbn" placeholder="Masukkan ISBN buku" name="isbn" required pattern="\d{13}" title="13 digit angka">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="category" class="form-label">Kategori</label>
                                    <input type="text" class="form-control" id="category" placeholder="Masukkan kategori buku" name="category" list ="category-options" required>
                                    <datalist id="category-options">
                                        <option value="Sastra">
                                        <option value="Matematika">
                                        <option value="Sains dan Teknologi">
                                        <option value="Novel">
                                        <option value="Ensiklopedia">
                                    </datalist>
                                </div>
                                <div class="mb-3">
                                    <label for="no_rak" class="form-label">Nomor Rak</label>
                                    <input type="text" class="form-control" id="no_rak" placeholder="misal : LKP01" name="no_rak" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi Buku</label>
                                    <textarea class="form-control" id="description" name="description" placeholder="Masukkan deskripsi buku" required></textarea>
                                </div>
                            <button type="submit" name="create" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>

                
                <?php }  
                if (isset($_POST['edit'])) { 
                    $editbook = mysqli_query($con, "SELECT * FROM book WHERE id = {$_POST['id_book']}");  
                    $old_data = mysqli_fetch_array($editbook);  
                ?>
                <h5 class="card-title fw-semibold mb-4">Forms Edit</h5>
                <?php 
                    if (isset($_POST['edit'])) {
                        if (isset($_SESSION['error'])) { 
                            echo '<div class="alert alert-danger col-8 mx-auto text-center p-2 border rounded text-center">' . $_SESSION['error'] . '</div>'; 
                            unset($_SESSION['error']); 
                        } 
                    }
                ?>
                <div class="card">
                    <div class="card-body">
                        <form role="form" method="post" action="./index.php" enctype="multipart/form-data">
                            <input type="hidden" name="id_book_update" value="<?= $old_data['id'] ?>">
                            
                            <div class="mb-3">
                                <label for="photo" class="form-label">Foto</label>
                                <div class="image-preview-container mb-3">
                                    <?php if (isset($old_data['photo_filename'])) { ?>
                                        <img class="img-thumbnail image-preview" alt="Preview"
                                            src="../../assets/images/book/<?= $old_data['photo_filename'] ?>"
                                            style="width: 200px; height: 200px;">
                                    <?php } else { ?>
                                        <img class="img-thumbnail image-preview" alt="Preview"
                                            src="../../assets/images/book/default.png" style="width: 200px; height: 200px;">
                                    <?php } ?>
                                </div>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*"
                                    onchange="previewImage(this)">
                                <div id="photoHelp" class="form-text">Hanya menerima foto dengan ekstensi ('jpg','jpeg',
                                    'png', 'gif', 'bmp', 'webp')</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Judul Buku</label>
                                <input type="text" class="form-control" id="title" placeholder="Masukkan judul buku"
                                    name="title" value="<?= $old_data['title'] ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="pdf" class="form-label">Upload PDF</label>
                                <input type="file" class="form-control" id="pdf" name="pdf" accept=".pdf">
                                <div id="pdfHelp" class="form-text">Hanya menerima file PDF.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="adminor" class="form-label">Pengarang</label>
                                <input type="text" class="form-control" id="adminor"
                                    placeholder="Masukkan nama pengarang" name="adminor"
                                    value="<?= $old_data['adminor'] ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="publisher" class="form-label">Penerbit</label>
                                <input type="text" class="form-control" id="publisher"
                                    placeholder="Masukkan nama penerbit" name="publisher"
                                    value="<?= $old_data['publisher'] ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="amount" class="form-label">Jumlah Buku</label>
                                <input type="number" class="form-control" id="amount" placeholder="1" name="amount"
                                    value="<?= $old_data['amount'] ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="price" class="form-label">Harga Buku</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="price" placeholder="0" name="price" 
                                           value="<?= $old_data['price'] ?>" required min="0" step="1000">
                                </div>
                                <div id="priceHelp" class="form-text">Masukkan harga buku dalam Rupiah</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="tahun" class="form-label">Tahun Terbit</label>
                                <input type="text" class="form-control" id="tahun" placeholder="misal : 2020" name="tahun"
                                    value="<?= $old_data['tahun'] ?>" required>
                            </div>
                            
                           <div class="mb-3">
                                    <label for="isbn" class="form-label">ISBN</label>
                                    <input type="tel" class="form-control" id="isbn" placeholder="Masukkan ISBN buku" name="isbn" required pattern="\d{13}" title="13 digit angka"
                                    value="<?= $old_data['isbn'] ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">Kategori</label>
                                <input type="text" class="form-control" id="category" placeholder="Masukkan kategori buku" name="category" value="<?= $old_data['category'] ?>" list="category-options" required>
                                <datalist id="category-options">
                                    <option value="Sastra">
                                    <option value="Matematika">
                                    <option value="Sains dan Teknologi">
                                    <option value="Novel">
                                    <option value="Ensiklopedia">
                                </datalist>
                            </div>
                            
                            <div class="mb-3">
                                <label for="no_rak" class="form-label">Nomor Rak Buku</label>
                                <input type="text" class="form-control" id="no_rak" placeholder="misal : LKP01" name="no_rak"
                                    value="<?= $old_data['nomor_rak_buku'] ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi Buku</label>
                                <textarea class="form-control" id="description" name="description" placeholder="Masukkan deskripsi buku" required><?= $old_data['description'] ?></textarea>
                            </div>
                            
                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
                <?php }
                if (isset($_POST['details'])) { 
                    $detailsbook = mysqli_query($con, "SELECT * FROM book WHERE id=$_POST[id_book] ");  
                    $details = mysqli_fetch_array($detailsbook); 
                ?>
                <h5 class="card-title fw-semibold mb-4">Detail Buku</h5>
<div class="card shadow-lg rounded-4 border-0 overflow-hidden">
    <div class="row g-0">
        <div class="col-md-4 bg-light d-flex align-items-center justify-content-center p-3">
            <?php if (!empty($details['photo_filename'])) { ?>
                <img class="img-fluid rounded shadow-sm" alt="Preview"
                     src="../../assets/images/book/<?= $details['photo_filename'] ?>"
                     style="max-height: 300px; object-fit: cover;">
            <?php } else { ?>
                <img class="img-fluid rounded shadow-sm" alt="Preview" src="../../assets/images/book/default.png"
                     style="max-height: 300px; object-fit: cover;">
            <?php } ?>
        </div>
        <div class="col-md-8">
            <div class="card-body p-4">
                <h3 class="card-title text-primary fw-bold mb-3"><?= strtoupper($details['title']) ?></h3>
                <p><strong>Pengarang:</strong> <?= $details['adminor'] ?></p>
                <p><strong>Penerbit:</strong> <?= $details['publisher'] ?></p>
                <p><strong>Jumlah Buku:</strong> <span class="badge bg-secondary"><?= $details['amount'] ?></span></p>
                <p><strong>Harga Buku:</strong> <span class="badge bg-success">Rp <?= number_format($details['price'], 0, ',', '.') ?></span></p>
                <p><strong>Tahun Terbit:</strong> <?= $details['tahun'] ?? 'Tidak tersedia' ?></p>
                <p><strong>ISBN:</strong> <?= $details['isbn'] ?? 'Tidak ditemukan' ?></p>
                <p><strong>Nomor Rak Buku:</strong> <?= $details['nomor_rak_buku'] ?? 'Tidak ditemukan' ?></p>
                <hr>
                <div style="max-height: 150px; overflow-y: auto;" class="bg-light p-2 rounded shadow-sm">
                    <p class="mb-0"><?= nl2br($details['description']) ?></p>
                </div>
                <a href="../book/" class="btn btn-outline-primary mt-4">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php
include_once("../footer.php");
}
?>