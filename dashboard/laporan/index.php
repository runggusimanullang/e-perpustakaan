<?php
session_start();
$_SESSION['navigation'] = "7";
include_once("../navbar.php");
include_once("../koneks.php");
?>

<h3 class="">Laporan Peminjaman</h3>

<!-- Form Filter Tanggal -->
<form method="GET" class="mb-4">
    <div class="row">
        <div class="col-md-3">
            <label for="start_date" class="form-label">Tanggal Mulai</label>
            <input type="date" class="form-control" name="start_date" id="start_date"
                   value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
        </div>
        <div class="col-md-3">
            <label for="end_date" class="form-label">Tanggal Selesai</label>
            <input type="date" class="form-control" name="end_date" id="end_date"
                   value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="index.php" class="btn btn-secondary ms-2">Reset</a>
        </div>
    </div>
</form>

<!-- Tombol Cetak -->
<div class="row mb-4">
    <div class="col-md-6 d-flex">
        <?php if(isset($_GET['start_date']) && isset($_GET['end_date']) && $_GET['start_date'] && $_GET['end_date']) { ?>
            <a href="cetak_laporan.php?start_date=<?= $_GET['start_date'] ?>&end_date=<?= $_GET['end_date'] ?>" 
               class="btn btn-success" target="_blank">
                Cetak Laporan
            </a>
        <?php } else { ?>
            <a href="cetak_laporan.php" class="btn btn-success" target="_blank">Cetak Semua</a>
        <?php } ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body p-4">
                <?php if(!isset($_POST['details'])){ ?>                

                <div class="table-responsive">
                    <table class="table text-nowrap mb-0 align-middle">
                        <thead class="text-dark fs-4">
                            <tr>
                                <th>No</th>
                                <th>Nama Peminjam</th>
                                <th>Nama Buku</th>
                                <th class="text-center">Tanggal Pinjam</th>
                                <th class="text-center">Tenggat Pengembalian</th>
                                <th class="text-center">Tanggal Pengembalian</th>
                                <th class="mx-4">Status</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Filter query
                            $where = "";
                            if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
                                $start = $_GET['start_date'];
                                $end = $_GET['end_date'];
                                if ($start && $end) {
                                    $where = "WHERE loan_date BETWEEN '$start' AND '$end'";
                                }
                            }

                            $result = mysqli_query($con, "SELECT * FROM borrowing $where ORDER BY loan_date DESC");
                            $iteration = 0;
                            while ($borrowing = mysqli_fetch_array($result)) {
                                $iteration++;
                            ?>
                            <tr>
                                <td><?= $iteration ?></td>
                                <td>
                                    <?php 
                                    $borrowerdata = mysqli_query($con, "SELECT * FROM siswa WHERE id=$borrowing[borrower_id]"); 
                                    $borrower = mysqli_fetch_assoc($borrowerdata);
                                    echo $borrower['fullname'];
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $bookdata = mysqli_query($con, "SELECT * FROM book WHERE id=$borrowing[book_id]"); 
                                    $book = mysqli_fetch_assoc($bookdata);
                                    echo $book['title'];
                                    ?>
                                </td>
                                <td class="text-center"><?= $borrowing['loan_date'] ?></td>
                                <td class="text-center">
                                    <?php 
                                    if ($borrowing['status'] === "Request Peminjaman") {
                                        echo "-";
                                    } else {
                                        echo $borrowing['return_date'];
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    if ($borrowing['status'] === "Request Peminjaman") {
                                        echo "-";
                                    } elseif ($borrowing['status'] === "Belum Kembali") {
                                        echo "-";
                                    } else {
                                        echo $borrowing['actual_return_date'] 
                                            ? date('Y-m-d', strtotime($borrowing['actual_return_date'])) 
                                            : "-";
                                    }
                                    ?>
                                </td>
                               <td>
                                    <div class="d-flex align-items-center gap-2">
                                    <?php if ($borrowing['status'] === "Request Peminjaman") { ?>
                                    <!-- Status Request Peminjaman -->
                                    <span class="badge bg-primary rounded-3 fw-semibold" style="width: 145px;">
                                        <?= $borrowing['status'] ?>
                                    </span>
                                <?php } elseif ($borrowing['status'] === "Belum Kembali" || $borrowing['status'] === "Dipinjam") {
                                    $returnDateTime = new DateTime($borrowing['return_date']);
                                    $currentDate = new DateTime();
                                    $dateDifference = $currentDate->diff($returnDateTime);
                                    $daysDifference = $dateDifference->days;

                                    if ($currentDate <= $returnDateTime) {
                                        ?>
                                        <span class="badge bg-warning text-dark rounded-3 fw-semibold" style="width: 145px;">
                                            Dipinjam
                                        </span>
                                        <?php
                                    } else {
                                        ?>
                                        <span class="badge bg-danger rounded-3 fw-semibold" style="width: 145px;">
                                            Belum Kembali <br>( Terlambat <?= $daysDifference ?> hari )
                                        </span>
                                        <?php
                                    }
                                } elseif ($borrowing['status'] === "Sudah Kembali") { ?>
                                    <span class="badge bg-success rounded-3 fw-semibold" style="width: 145px;">
                                        <?= $borrowing['status'] ?>
                                    </span>
                                <?php } else { ?>
                                    <!-- Jika status lain -->
                                    <span class="badge bg-secondary rounded-3 fw-semibold" style="width: 145px;">
                                        <?= $borrowing['status'] ?>
                                    </span>
                                <?php } ?>

                                    </div>
                            </td>
<td class="text-center">
    <!-- Aksi bisa ditambahkan di sini -->
</td>

                            </tr>
                            <?php } ?>
                            <?php if($iteration === 0){ ?>
                            <tr>
                                <td colspan="7" class="text-center">
                                    <h5 class="fw-semibold mb-0">Data peminjaman tidak ditemukan</h5>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include_once("../footer.php"); } ?>
