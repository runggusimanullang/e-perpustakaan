<?php
session_start();
include '../dashboard/koneks.php'; // pastikan koneksi database

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

$borrower_id = $_SESSION['siswa_id'] ?? null;

// Hitung jumlah buku aktif yang sedang diproses oleh siswa
$cek_total_aktif = mysqli_query($con, "
    SELECT COUNT(*) AS total FROM borrowing 
    WHERE borrower_id = '$borrower_id' 
    AND status IN ('Request Peminjaman', 'Dipinjam', 'Belum Kembali')
");
$data_total = mysqli_fetch_assoc($cek_total_aktif);
$jumlah_aktif = $data_total['total'] ?? 0;

// Hitung juga jumlah yang ada di keranjang
$jumlah_di_keranjang = count($_SESSION['keranjang']);
$total_diproses = $jumlah_aktif + $jumlah_di_keranjang;


if (isset($_POST['add_to_cart'])) {
    $book_id = $_POST['book_id'];

    // Hitung jumlah total proses (aktif di DB + di keranjang)
    $cek_total_aktif = mysqli_query($con, "
        SELECT COUNT(*) AS total FROM borrowing 
        WHERE borrower_id = '$borrower_id' 
        AND status IN ('Request Peminjaman', 'Dipinjam', 'Belum Kembali')
    ");
    $data_total = mysqli_fetch_assoc($cek_total_aktif);
    $jumlah_aktif = $data_total['total'] ?? 0;
    $jumlah_di_keranjang = count($_SESSION['keranjang']);
    $total_diproses = $jumlah_aktif + $jumlah_di_keranjang;

    if ($total_diproses >= 2) {
        $_SESSION['warning_message'] = "Batas maksimal peminjaman adalah 2 buku. Harap kembalikan buku terlebih dahulu.";
    } else {
        // Cek apakah buku yang sama sudah pernah diakses sebelumnya
        $cek_request = mysqli_query($con, "SELECT * FROM borrowing 
            WHERE borrower_id = '$borrower_id' AND book_id = '$book_id' AND status = 'Request Peminjaman'");
        $cek_pinjam = mysqli_query($con, "SELECT * FROM borrowing 
            WHERE borrower_id = '$borrower_id' AND book_id = '$book_id' AND status = 'Dipinjam'");
        $cek_belum_kembali = mysqli_query($con, "SELECT * FROM borrowing 
            WHERE borrower_id = '$borrower_id' AND book_id = '$book_id' AND status = 'Belum Kembali'");

        if (mysqli_num_rows($cek_request) > 0) {
            $_SESSION['warning_message'] = "Anda sudah request buku ini. Tidak bisa request lagi.";
        } elseif (mysqli_num_rows($cek_pinjam) > 0) {
            $_SESSION['warning_message'] = "Anda masih meminjam buku ini. Harap kembalikan terlebih dahulu.";
        } elseif (mysqli_num_rows($cek_belum_kembali) > 0) {
            $_SESSION['warning_message'] = "Anda belum mengembalikan buku ini. Silakan kembalikan sebelum meminjam lagi.";
        } elseif (!in_array($book_id, $_SESSION['keranjang'])) {
            $_SESSION['keranjang'][] = $book_id;
            $_SESSION['success_message'] = "Buku berhasil ditambahkan ke keranjang!";
        } else {
            $_SESSION['warning_message'] = "Buku sudah ada di keranjang.";
        }
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


?>

<?php include_once("header.php"); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<h3>Data Buku</h3>

<!-- Alert sukses atau warning -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success_message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['warning_message'])): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <?= $_SESSION['warning_message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['warning_message']); ?>
<?php endif; ?>

<a href="keranjang.php" class="btn btn-outline-dark mb-3">
    <i class="bi bi-cart"></i> Lihat Keranjang (<?= count($_SESSION['keranjang']) ?>)
</a>

<div class="row">
    <div class="col-lg-12 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body p-4">               
                <form action="" method="post">
                    <div class="mb-3" style="display: flex; align-items: center;">
                        <input type="text" class="form-control" name="cari"
                        placeholder="Cari berdasarkan judul buku" style="flex: 1;">
                        <button name="search" class="btn btn-primary mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                <path d="M21 21l-6 -6" />
                            </svg>
                        </button>
                    </div>
                </form>

                <?php 
                if(isset($_POST['cari'])){
                    $cari = $_POST['cari'];
                    echo "<b>Hasil pencarian : ".$cari."</b>";
                }
                ?>

                <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4 mt-3">
                    <?php 
                    if (isset($_POST['search'])) {
                        $search_title = $_POST['cari'];
                        $result = mysqli_query($con, "SELECT * FROM book WHERE title LIKE '%$search_title%'");
                    } else {
                        $result = mysqli_query($con, "SELECT * FROM book");
                    }

                    $iteration = 0;
                    while ($buku = mysqli_fetch_array($result)) {
                        $iteration++;
                        $cover = !empty($buku['photo_filename']) ? "../assets/images/book/{$buku['photo_filename']}" : "../assets/images/book/default.png";
                    ?>
                    <div class="col">
                        <div class="card h-100 d-flex flex-column" style="width: 180px;">
                            <img src="<?= $cover ?>" class="card-img-top" alt="<?= $buku['title'] ?>" style="height: 180px; object-fit: cover;">
                            
                            <div class="card-body flex-grow-1 d-flex flex-column">
                                <h5 class="card-title mb-1"><?= $buku['title'] ?></h5>

                                <div class="mt-auto pt-1 d-flex justify-content-center flex-wrap gap-1">
                                    <button type="button" class="btn btn-primary btn-sm px-2"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#detail<?= $buku['id'] ?>"
                                        style="white-space: nowrap; font-size: 12px;">
                                        Detail
                                    </button>

                                    <?php if (!empty($buku['pdf_filename'])): ?>
                                        <a href="preview_pdf.php?file=<?= urlencode($buku['pdf_filename']) ?>" 
                                            target="_blank" 
                                            class="btn btn-secondary btn-sm px-2"
                                            style="white-space: nowrap; font-size: 12px;">
                                            Baca
                                        </a>
                                        <form action="" method="post">
                                            <input type="hidden" name="book_id" value="<?= $buku['id'] ?>">
                                            <button type="submit" name="add_to_cart" class="btn btn-success btn-sm px-2" style="white-space: nowrap; font-size: 12px;">
                                                <i class="bi bi-cart-plus"></i> + keranjang
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Detail -->
                    <div class="modal" id="detail<?= $buku['id'] ?>">
                        <div class="modal-dialog">
                            <div class="modal-content shadow-lg rounded-4 border-0">
                                <div class="modal-header bg-primary text-white rounded-top-4">
                                    <h5 class="modal-title"><i class="bi bi-book"></i> Detail Buku</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body bg-light">
                                    <div class="row">
                                        <div class="col-md-4 text-center mb-3">
                                            <img class="img-fluid rounded shadow-sm" alt="Preview"
                                                src="<?= isset($buku['photo_filename']) ? "../assets/images/book/{$buku['photo_filename']}" : "../../assets/images/book/default.png" ?>"
                                                style="max-height: 220px; object-fit: cover;">
                                        </div>
                                        <div class="col-md-8">
                                            <h5 class="fw-bold mb-2"><?= strtoupper($buku['title']) ?></h5>
                                            <p><strong>Pengarang:</strong> <?= $buku['adminor'] ?></p>
                                            <p><strong>Penerbit:</strong> <?= $buku['publisher'] ?></p>
                                            <p><strong>Jumlah Buku:</strong> <span class="badge bg-secondary"><?= $buku['amount'] ?></span></p>
                                            <p><strong>Tahun Terbit:</strong> <?= $buku['tahun'] ?? 'Tidak tersedia' ?></p>
                                            <p><strong>ISBN:</strong> <?= $buku['isbn'] ?? 'Tidak ditemukan' ?></p>
                                            <p><strong>NO Rak Buku:</strong> <?= $buku['nomor_rak_buku'] ?? 'Tidak ditemukan' ?></p>
                                            <hr class="my-2">
                                            <div style="max-height: 100px; overflow-y: auto;" class="bg-white p-2 rounded shadow-sm">
                                                <p class="mb-0"><?= nl2br($buku['description']) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-white rounded-bottom-4">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle"></i> Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                    }
                    if ($iteration === 0) { ?>
                        <td class="border-bottom-0 text-center" colspan="6">
                            <h3 class="fw-semibold mb-0 text-center">Buku tidak ditemukan</h3>
                        </td>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.modal-content {
    transition: all 0.3s ease-in-out;
}
.modal-body p {
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}
</style>

<?php include_once("footer.php"); ?>
