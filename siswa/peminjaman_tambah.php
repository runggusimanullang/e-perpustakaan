<?php include_once("header.php"); ?>

<?php
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
         . $_SESSION['success_message'] .
         '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['warning_message'])) {
    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">'
         . $_SESSION['warning_message'] .
         '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    unset($_SESSION['warning_message']);
}

if (isset($_SESSION['danger_message'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
         . $_SESSION['danger_message'] .
         '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    unset($_SESSION['danger_message']);
}
?>


<h3 class="">Pinjam Buku</h3>
<div class="row">
    <div class="col-lg-12 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body p-4">
                <div class="card-body">
                    <form role="form" method="post" action="peminjaman_aksi.php">
                        <!-- Tambahan Select2 CSS -->
                        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
                        <div class="mb-3">
                            <label for="book_id" class="form-label">Nama Buku</label>
                            <select class="form-select select-book" name="book_id[]" id="book_id" required>

                                <option></option>
                                <?php
                                $iterationbook = 0;
                                $allbook = mysqli_query($con, "SELECT * FROM book WHERE amount > 0");
                                if (mysqli_num_rows($allbook) == 0) {
                                    echo '<option disabled>Buku tidak tersedia saat ini</option>';
                                } else {
                                    while ($book = mysqli_fetch_array($allbook)) {
                                        $iterationbook++;
                                        ?>
                                        <option value="<?= $book['id'] ?>">
                                            <?= $iterationbook . '. ' . $book['title'] . ' - Tersedia: ' . $book['amount'] ?>
                                        </option>
                                        <?php
                                    }
                                }

                                ?>
                            </select>
                        </div>

                        <!-- Tambahkan jQuery dan Select2 JS -->
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

                        <!-- Aktifkan Select2 -->
                        <script>
                        $(document).ready(function() {
                            $('.select-book').select2({
                                placeholder: "Ketik atau pilih buku...",
                                allowClear: true
                            });
                        });
                        </script>

                        <?php $currentDate = date('Y-m-d'); ?>
                        <div class="mb-3">
                            <label for="loan_date" class="form-label">Tanggal Peminjaman</label>
                            <h6 class="fw-semibold mb-1 mx-2 mt-2"><?= $currentDate ?></h6>
                            <input type="hidden" class="form-control" id="loan_date" name="loan_date" value="<?= $currentDate ?>" readonly required>
                        </div>

                        <?php $returnDate = date('Y-m-d', strtotime('+2 days', strtotime($currentDate))); ?>
                        <div class="mb-3">
                            <label for="return_date" class="form-label">Tenggat Pengembalian</label>
                            <h6 class="fw-semibold mb-2 mx-2 mt-2"><?= $returnDate ?></h6>
                            <input type="hidden" class="form-control" id="return_date" name="return_date" value="<?= $returnDate ?>" readonly required>
                            <div id="return_dateHelp" class="form-text mx-2">2 Hari setelah tanggal peminjaman</div>
                        </div>

                        <button type="submit" name="create" class="btn btn-primary">Simpan Data</button>
                        <div style="position: relative;">
                            <a class="btn btn-danger" style="position: absolute; left: 122px; margin-top: -37px;" href="peminjaman.php">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT UNTUK TAMPILKAN INFO BUKU -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectBook = document.querySelector('.select-book');
        const bookInfo = document.getElementById('book-info');
        const infoYear = document.getElementById('info-year');
        const infoPublisher = document.getElementById('info-publisher');
        const infoRack = document.getElementById('info-rack');

        $('.select-book').on('select2:select', function (e) {
            var selected = e.params.data.element;
            var $selected = $(selected);

            $('#book-info').show();
            $('#info-year').text($selected.data('year') || '-');
            $('#info-publisher').text($selected.data('publisher') || '-');
            $('#info-rack').text($selected.data('rack') || '-');
        });
    });
</script>


<?php include_once("footer.php"); ?>
