<?php 
session_start();

if (isset($_GET['cetak'])) {
    include_once("../koneks.php");
    require('../fpdf/fpdf.php');

    $pdf = new FPDF('l', 'mm', 'A5');
    $pdf->AddPage();

    // Header dan Logo
    $pdf->Image('C:/xampp/htdocs/perpusada/assets/images/logos/samarfa.jpg', 10, 10, 32);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetY(14);
    $pdf->Cell(190, 7, 'E-LIBRARY SD SANTA MARIA FATIMA', 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Jalan Jatinegara Barat No.122 Bidara Cina, Jatinegara, RT.7/RW.1, Kp. Melayu,', 0, 1, 'C');
    $pdf->Cell(0, 5, 'Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13320', 0, 1, 'C');
    $pdf->Cell(0, 5, 'Telepon: (021) 85902383', 0, 1, 'C');
    $pdf->SetY(44);
    $pdf->Cell(190, 7, 'DAFTAR REQUEST PEMINJAMAN', 0, 1, 'C');

    $pdf->Cell(10, 18, '', 0, 1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(15, 6, 'No', 1, 0, 'C');  
    $pdf->Cell(40, 6, 'Peminjam', 1, 0);  
    $pdf->Cell(50, 6, 'Buku', 1, 0);  
    $pdf->Cell(35, 6, 'Tanggal Permintaan', 1, 0, 'C');  
    $pdf->Cell(40, 6, 'Status', 1, 1, 'C');  

    $pdf->SetFont('Arial', '', 10);
    $data_borrow = mysqli_query($con, "SELECT * FROM borrowing WHERE status='Request Peminjaman'");
    $hitung = 1; 
    while ($row = mysqli_fetch_assoc($data_borrow)) {
        $borrower = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM siswa WHERE id={$row['borrower_id']}"));
        $book = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM book WHERE id={$row['book_id']}"));

        $pdf->Cell(15, 6, $hitung++, 1, 0, 'C');
        $pdf->Cell(40, 6, $borrower['fullname'], 1, 0);
        $pdf->Cell(50, 6, $book['title'], 1, 0);
        $pdf->Cell(35, 6, $row['loan_date'], 1, 0, 'C');  
        $pdf->Cell(40, 6, $row['status'], 1, 1, 'C');  
    }
    $pdf->Output();

} else {
    $_SESSION['navigation'] = "6";
    include_once("../navbar.php");

    // Proses Konfirmasi
    if (isset($_POST['konfirm'])) {
    $book = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM book WHERE id={$_POST['id_book']}"));

    if ($book['amount'] <= 0) {
        $_SESSION['error'] = "Stok buku sudah habis. Tidak bisa mengkonfirmasi peminjaman.";
        echo '<script>window.location.href="request.php";</script>';
        exit;
    }

    $amountbook = $book['amount'] - 1;
$currentDate = date('Y-m-d');
$currentDate = date('Y-m-d');
$returnDate = date('Y-m-d', strtotime($currentDate . ' +2 days'));

$update = mysqli_query($con, "UPDATE borrowing 
    SET status='Dipinjam', 
        loan_date='$currentDate', 
        return_date='$returnDate',
        actual_return_date=NULL
    WHERE id={$_POST['id_borrowing']}");


    if ($update) {
        mysqli_query($con, "UPDATE book SET amount='$amountbook' WHERE id={$_POST['id_book']}");
        $_SESSION['success'] = "Berhasil Mengkonfirmasi Peminjaman";
        echo '<script>window.location.href="request.php";</script>';
    } else {
        $_SESSION['error'] = "Gagal Mengkonfirmasi data buku";
        echo '<script>window.location.href="request.php";</script>';
    }

    
}


    // Proses Hapus
    if (isset($_POST['delete'])) {
        $id_borrowing = $_POST['id_borrowing'];
        mysqli_query($con, "DELETE FROM borrowing WHERE id=$id_borrowing");
    }
?>
<h3>Request Peminjaman Buku</h3>
<div class="row">
    <div class="col-lg-12 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body p-4">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if(!isset($_POST['details'])){ ?>
                    <a href="../borrowing/" class="btn btn-primary mb-3">Peminjam Baru</a>
                    <a href="?cetak" target="_blank" class="btn btn-primary mb-3 mx-1">Cetak PDF</a>

                    <!-- Form Pencarian -->
                    <form action="request.php" method="post" class="mb-3 d-flex">
                        <input type="text" class="form-control input" name="value_search" placeholder="Cari berdasarkan nama peminjam dan judul buku" style="flex: 1;">
                        <button name="search" class="btn btn-primary mx-2">Cari</button>
                    </form>

                    <!-- Tabel -->
                    <div class="table-responsive">
                        <table class="table text-nowrap mb-0 align-middle">
                            <thead class="text-dark fs-4">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Peminjam</th>
                                    <th>Nama Buku</th>
                                    <th class="text-center">Tanggal Permintaan</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($_POST['search'])) {
                                    $search = $_POST['value_search'];
                                    $result = mysqli_query($con, "SELECT borrowing.*, book.title as book_title, siswa.fullname as borrower_name 
                                        FROM borrowing
                                        JOIN book ON borrowing.book_id = book.id
                                        JOIN siswa ON borrowing.borrower_id = siswa.id
                                        WHERE borrowing.status = 'Request Peminjaman'
                                        AND (book.title LIKE '%$search%' OR siswa.fullname LIKE '%$search%')
                                        ORDER BY borrowing.id DESC");

                                } else {
                                    $result = mysqli_query($con, "SELECT * FROM borrowing WHERE status='Request Peminjaman' ORDER BY id DESC");
                                }
                                $no = 0;
                                while ($borrowing = mysqli_fetch_assoc($result)) {
                                    $no++;
                                    $borrower = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM siswa WHERE id={$borrowing['borrower_id']}"));
                                    $book = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM book WHERE id={$borrowing['book_id']}"));
                                ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= $borrower['fullname'] ?></td>
                                    <td><?= $book['title'] ?></td>
                                    <td class="text-center"><?= $borrowing['loan_date'] ?></td>
                                    <td><?= $borrowing['status'] ?></td>
                                    <td class="text-center">
                                        <form method="post" style="display: inline;" onsubmit="return confirm('Konfirmasi permintaan ini?')">
                                            <input type="hidden" name="id_borrowing" value="<?= $borrowing['id'] ?>">
                                            <input type="hidden" name="id_book" value="<?= $borrowing['book_id'] ?>">
                                            <button type="submit" name="konfirm" class="btn btn-primary btn-sm">Konfirmasi</button>
                                        </form>
                                        <form method="post" style="display: inline;">
                                            <input type="hidden" name="id_borrowing" value="<?= $borrowing['id'] ?>">
                                            <button type="submit" name="details" class="btn btn-secondary btn-sm">Detail</button>
                                        </form>
                                        <form method="post" style="display: inline;" onsubmit="return confirm('Hapus data ini?')">
                                            <input type="hidden" name="id_borrowing" value="<?= $borrowing['id'] ?>">
                                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else {
                    // Tampil Detail

// Ambil data
$details = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM borrowing WHERE id={$_POST['id_borrowing']}"));
$borrower = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM siswa WHERE id={$details['borrower_id']}"));
$book = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM book WHERE id={$details['book_id']}"));
?>
<h5 class="text-center mb-3 fw-bold text-primary">📄 Detail Request Peminjaman</h5>

<div class="row g-3 mb-3">
    <!-- Peminjam -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3 h-100 d-flex flex-column align-items-center">
            <div class="bg-primary bg-gradient text-white w-100 py-2 text-center">
                <small class="m-0">👤 Peminjam</small>
            </div>
            <div class="p-3 w-100 text-center">
                <img
                    src="../../assets/images/siswa/<?= !empty($borrower['photo_filename']) ? $borrower['photo_filename'] : 'user-1.jpg' ?>"
                    class="rounded mb-2 shadow"
                    style="width:150px; height:200px; object-fit:cover;"
                    alt="Foto Peminjam"
                >
                <h6 class="fw-semibold mb-1"><?= htmlspecialchars($borrower['fullname']) ?></h6>
                <small class="text-muted mb-2"><?= htmlspecialchars($borrower['email'] ?? '') ?></small>
                <ul class="list-unstyled small mt-2 text-start w-100 px-2 mb-0">
                    <li><strong>ID Anggota:</strong> <?= htmlspecialchars($borrower['siswa_id'] ?? '-') ?></li>
                    <li><strong>Kelas:</strong> <?= htmlspecialchars($borrower['kelas'] ?? '-') ?></li>
                    <li><strong>Alamat:</strong> <?= htmlspecialchars($borrower['address'] ?? '-') ?></li>
                    <li><strong>Telepon:</strong> <?= htmlspecialchars($borrower['phone_number'] ?? '-') ?></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Buku -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3 h-100 d-flex flex-column align-items-center">
            <div class="bg-success bg-gradient text-white w-100 py-2 text-center">
                <small class="m-0">📖 Buku</small>
            </div>
            <div class="p-3 w-100 text-center">
                <img
                    src="../../assets/images/book/<?= !empty($book['photo_filename']) ? $book['photo_filename'] : 'default.png' ?>"
                    class="rounded mb-2 shadow"
                    style="width:150px; height:200px; object-fit:cover;"
                    alt="Cover Buku"
                >
                <h6 class="fw-semibold mb-1"><?= htmlspecialchars($book['title']) ?></h6>
                <small class="text-muted mb-2"><?= htmlspecialchars($book['author'] ?? '') ?></small>
                <ul class="list-unstyled small mt-2 text-start w-100 px-2 mb-0">
                    <li><strong>ISBN:</strong> <?= htmlspecialchars($book['isbn'] ?? '-') ?></li>
                    <li><strong>Penerbit:</strong> <?= htmlspecialchars($book['publisher'] ?? '-') ?></li>
                    <li><strong>Tahun Terbit:</strong> <?= htmlspecialchars($book['year_published'] ?? '-') ?></li>
                    <li><strong>Kategori:</strong> <?= htmlspecialchars($book['category'] ?? '-') ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Informasi tambahan -->
<div class="card border-0 shadow-sm rounded-3 mb-3 p-3">
    <h6 class="text-center mb-2 text-secondary small">📋 Informasi Tambahan</h6>
    <div class="row text-center gy-2 small">
        <div class="col-6">
            <div class="text-muted">📅 Tanggal Permintaan</div>
            <div class="fw-medium"><?= date('d M Y', strtotime($details['loan_date'])) ?></div>
        </div>
        <div class="col-6">
            <div class="text-muted">📍 Status</div>
            <span class="badge <?= $details['status']==='Request Peminjaman' ? 'bg-warning text-dark' : 'bg-success' ?> px-2 py-1 rounded-pill small">
                <?= htmlspecialchars($details['status']) ?>
            </span>
        </div>
    </div>
</div>

<div class="text-center">
    <a href="request.php" class="btn btn-outline-primary btn-sm px-3 rounded-pill">⬅ Kembali</a>
</div>






                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php
    include_once("../footer.php");
}
?>
