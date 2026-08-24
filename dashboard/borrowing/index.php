<?php
session_start();
$_SESSION['navigation'] = "4";
include_once("../navbar.php");

if (isset($_POST['create'])) {
    $borrower_id = $_POST['borrower_id'];
    $book_id = $_POST['book_id'];
    $loan_date = $_POST['loan_date'];
    $return_date = $_POST['return_date'];


    // validasi cek borrower_id
    if (empty($borrower_id)) {
        $_SESSION['error'] = "Nama peminjam tidak ditemukan. Pastikan Anda memilih dari daftar yang tersedia.";
        echo '<script>window.location.href = "./index.php";</script>';
        exit;
    }

        // Cek apakah buku ditemukan
    if (empty($book_id)) {
        $_SESSION['error'] = "Buku tidak ditemukan. Pastikan Anda memilih buku dari daftar.";
        echo '<script>window.location.href = "./index.php";</script>';
        exit;
    }

    // Validasi apakah masih ada peminjaman aktif
    $cekPeminjaman = mysqli_query($con, "SELECT * FROM borrowing 
    WHERE borrower_id = '$borrower_id' 
    AND book_id = '$book_id' 
    AND status IN ('Dipinjam', 'Belum Kembali')");
    if (mysqli_num_rows($cekPeminjaman) > 0) {
        $_SESSION['error'] = "Peminjam ini masih meminjam buku tersebut dan belum mengembalikannya.";
        echo '<script>window.location.href = "./index.php";</script>';
        exit;
    }

    // Ambil data buku & kurangi stok
    $bookdatas = mysqli_query($con, "SELECT * FROM book WHERE id=$book_id");
    $books = mysqli_fetch_assoc($bookdatas);
    $amountbook = $books['amount'] - 1;

    // Simpan peminjaman
    $queryInsert = "INSERT INTO borrowing (borrower_id, book_id, loan_date, return_date, actual_return_date, status) 
                    VALUES ('$borrower_id','$book_id', '$loan_date', '$return_date', NULL, 'Dipinjam')";
    $createborrowing = mysqli_query($con, $queryInsert);

    if (!$createborrowing) {
        die("Query gagal: " . mysqli_error($con));
    }

    if ($createborrowing) {
        mysqli_query($con, "UPDATE book SET amount='$amountbook' WHERE id = $book_id");
        $_SESSION['success'] = "Berhasil menambah data peminjaman baru";
        echo '<script type="text/javascript">window.location.href = "../loan_data";</script>';
    }
}
?>

<h3>Transaksi</h3>
<div class="row">
    <div class="col-lg-12 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body p-4">
                <h5 class="card-title fw-semibold mb-4">Form Peminjaman</h5>

                <?php 
                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger col-8 mx-auto text-center p-2 border rounded text-center">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                } 
                ?>

                <div class="card">
                    <div class="card-body">
                        <form role="form" method="post" action="">

                            <!-- Nama Peminjam -->
                            <div class="mb-3">
                                <label for="borrower_name" class="form-label">Nama Peminjam</label>
                                <input class="form-control" list="siswaList" id="borrower_name" placeholder="Ketik nama peminjam..." required>
                                <datalist id="siswaList">
                                    <?php $allsiswa = mysqli_query($con, "SELECT * FROM siswa"); ?>
                                    <?php while ($siswa = mysqli_fetch_assoc($allsiswa)) { ?>
                                        <option value="<?= $siswa['fullname'] ?>" data-id="<?= $siswa['id'] ?>"></option>
                                    <?php } ?>
                                </datalist>
                                <!-- Input hidden untuk ID peminjam -->
                                <input type="hidden" name="borrower_id" id="borrower_id">
                            </div>

                            <script>
                            const borrowerNameInput = document.getElementById('borrower_name');
                            const borrowerIdInput = document.getElementById('borrower_id');

                            borrowerNameInput.addEventListener('input', function() {
                                const option = document.querySelector(`#siswaList option[value="${this.value}"]`);
                                borrowerIdInput.value = option ? option.getAttribute('data-id') : '';
                            });
                            </script>



                            <!-- Nama Buku -->
                            <div class="mb-3">
                                <label for="book_name" class="form-label">Nama Buku</label>
                                <input class="form-control" list="bookList" id="book_name" placeholder="Ketik judul buku..." required>
                                <datalist id="bookList">
                                    <?php $allbook = mysqli_query($con, "SELECT * FROM book WHERE amount != 0"); ?>
                                    <?php while ($book = mysqli_fetch_assoc($allbook)) { ?>
                                        <option value="<?= $book['title'] ?>" data-id="<?= $book['id'] ?>" data-amount="<?= $book['amount'] ?>"></option>
                                    <?php } ?>
                                </datalist>
                                <!-- Input hidden untuk ID buku -->
                                <input type="hidden" name="book_id" id="book_id">
                            </div>

                            <script>
                            const bookNameInput = document.getElementById('book_name');
                            const bookIdInput = document.getElementById('book_id');

                            bookNameInput.addEventListener('input', function() {
                                const option = document.querySelector(`#bookList option[value="${this.value}"]`);
                                bookIdInput.value = option ? option.getAttribute('data-id') : '';
                            });
                            </script>



                            <?php
                            $currentDate = date('Y-m-d');
                            $returnDate = date('Y-m-d', strtotime($currentDate . ' +2 days'));
                            ?>

                            <!-- Tanggal Peminjaman -->
                            <div class="mb-3">
                                <label for="loan_date" class="form-label">Tanggal Peminjaman</label>
                                <h6 class="fw-semibold mb-1 mx-2 mt-2"><?= $currentDate ?></h6>
                                <input type="hidden" name="loan_date" value="<?= $currentDate ?>" readonly required>
                            </div>

                            <!-- Tenggat Pengembalian -->
                            <div class="mb-3">
                                <label for="return_date" class="form-label">Tenggat Pengembalian</label>
                                <h6 class="fw-semibold mb-2 mx-2 mt-2"><?= $returnDate ?></h6>
                                <input type="hidden" name="return_date" value="<?= $returnDate ?>" readonly required>
                            </div>

                            <!-- Dikembalikan -->
                            <div class="mb-3">
                                <label class="form-label">Dikembalikan</label>
                                <h6 class="fw-semibold mb-1 mx-2 mt-2">-</h6>
                            </div>
                               
                            <!-- Tombol -->
                            <button type="submit" name="create" class="btn btn-primary">Simpan Data</button>
                            <a href="../home/index.php" class="btn btn-danger ms-2">Batal</a>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include_once("../footer.php"); ?>
