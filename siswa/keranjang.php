<?php
session_start();

// Pastikan file functions.php hanya dipanggil sekali dan sesuai path
require_once '../functions.php'; // Pastikan path-nya sesuai

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Hapus buku dari keranjang (HARUS sebelum ada output HTML!)
if (isset($_GET['hapus'])) {
    $hapus_id = $_GET['hapus'];
    $_SESSION['keranjang'] = array_filter($_SESSION['keranjang'], fn($id) => $id != $hapus_id);
    header("Location: keranjang.php");
    exit;
}

include_once("header.php");

// Jika keranjang kosong
if (count($_SESSION['keranjang']) === 0) {
    echo "<div class='container mt-5'><div class='alert alert-info'>Keranjang masih kosong.</div></div>";
    include_once("footer.php");
    exit;
}

// Proses pinjam (contoh sederhana)
if (isset($_POST['pinjam'])) {
    $peminjaman = $_SESSION['keranjang'];
    unset($_SESSION['keranjang']);
    echo "<div class='container mt-5'><div class='alert alert-success'>Berhasil meminjam " . count($peminjaman) . " buku.</div></div>";
    include_once("footer.php");
    exit;
}

// Ambil data buku berdasarkan ID dalam keranjang
$ids = implode(',', array_map('intval', $_SESSION['keranjang']));
$result = mysqli_query($con, "SELECT * FROM book WHERE id IN ($ids)");
?>

<div class="container mt-5">
    <h3>Keranjang Peminjaman</h3>
        <form method="post" action="peminjaman_aksi.php">
            <input type="hidden" name="loan_date" value="<?= date('Y-m-d') ?>">
            <input type="hidden" name="return_date" value="<?= date('Y-m-d', strtotime('+2 days')) ?>">

            <div class="row row-cols-1 row-cols-md-4 g-4 mt-3">
                <?php while ($buku = mysqli_fetch_assoc($result)): ?>
                    <div class="col">
                        <div class="card h-100">
                            <img src="<?= !empty($buku['photo_filename']) ? "../assets/images/book/{$buku['photo_filename']}" : "../assets/images/book/default.png" ?>" 
                                class="card-img-top" alt="<?= $buku['title'] ?>" style="height: 180px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= $buku['title'] ?></h5>
                                <p class="card-text"><small><?= $buku['adminor'] ?></small></p>

                                <!-- Hidden input untuk mengirim ID buku -->
                                <input type="hidden" name="book_id[]" value="<?= $buku['id'] ?>">

                                <a href="keranjang.php?hapus=<?= $buku['id'] ?>" class="btn btn-danger btn-sm mt-auto">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <a href="buku.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Buku
                </a>
                <button type="submit" name="pinjam" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Pinjam Sekarang
                </button>
            </div>
        </form>

</div>

<?php include_once("footer.php"); ?>
