<?php 
include_once("header.php");
?>
<!--  Row 1 -->
<div class="row">
    <div class="col-lg-12">
        <div class="row">            
            <div class="col-lg-3">
                <!-- Total Buku -->
                <div class="card">
                    <div class="card-body">
                        <a href="buku.php" class="">
                            <div class="row align-items-start">
                                <div class="col-8">
                                    <?php 
                                    $countbook = mysqli_query($con, "SELECT COUNT(*) as total FROM book");
                                    $bookcount = mysqli_fetch_assoc($countbook);
                                    ?>
                                    <h5 class="card-title mb-9 fw-semibold">Total <br>Buku</h5>
                                    <h4 class="fw-semibold mb-3"><?= $bookcount['total']?></h4>
                                </div>
                                <div class="col-4">
                                    <div class="d-flex justify-content-end">
                                        <div class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-book fs-6"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <!-- Total Peminjaman -->
                <div class="card">
                    <div class="card-body">
                        <a href="peminjaman.php" class="">
                            <div class="row align-items-start">
                                <div class="col-8">
                                    <?php 
                                    $saya = $_SESSION['siswa_id'];
                                    $countborrowing = mysqli_query($con, "SELECT COUNT(*) as total FROM borrowing WHERE borrower_id='$saya'");
                                    $borrowingcount = mysqli_fetch_assoc($countborrowing);
                                    ?>
                                    <h5 class="card-title mb-9 fw-semibold">Total<br> Peminjaman</h5>
                                    <h4 class="fw-semibold mb-3"><?= $borrowingcount['total']?></h4>
                                </div>
                                <div class="col-4">
                                    <div class="d-flex justify-content-end">
                                        <div class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-cards fs-6"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <!-- Total Terlambat -->
                <div class="card">
                    <div class="card-body">
                        <?php 
                        $countlate = mysqli_query($con, "SELECT COUNT(*) as total FROM borrowing WHERE borrower_id='$saya' AND return_date < CURDATE() AND status = 'Belum Kembali'");
                        $borrowinglate = mysqli_fetch_assoc($countlate);
                        $totalLate = $borrowinglate['total'];
                        ?>
                        <!-- cek totalLate -->
                        <?php if($totalLate > 0): ?>
                            <!-- Link aktif -->
                            <a href="total_terlambat.php" class="">
                        <?php else: ?>
                            <!-- Link dummy dan alert -->
                            <a href="javascript:void(0);" onclick="alert('Tidak ada buku terlambat dikembalikan.');" class="">
                        <?php endif; ?>
                                <div class="row align-items-start">
                                    <div class="col-8">
                                        <h5 class="card-title mb-9 fw-semibold">Total <br> Terlambat</h5>
                                        <h4 class="fw-semibold mb-3"><?= $totalLate ?></h4>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex justify-content-end">
                                            <div class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-file-description fs-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bagian Riwayat Peminjaman Tetap Sama -->
<div class="row">
    <div class="col-lg-12 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body p-4">
                <h5 class="card-title fw-semibold mb-4">Riwayat Peminjaman Baru</h5>
                <div class="table-responsive">
                    <table class="table text-nowrap mb-0 align-middle">
                        <thead class="text-dark fs-4">
                            <tr>
                                <th class="border-bottom-0"><h6 class="fw-semibold mb-0">No</h6></th>
                                <th class="border-bottom-0"><h6 class="fw-semibold mb-0">Nama Buku</h6></th>
                                <th class="border-bottom-0"><h6 class="fw-semibold mb-0 text-center">Tanggal Pinjam</h6></th>
                                <th class="border-bottom-0"><h6 class="fw-semibold mb-0 text-center">Tenggat Pengembalian</h6></th>
                                <th class="border-bottom-0"><h6 class="fw-semibold mb-0 mx-4">Status</h6></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $result = mysqli_query($con, "SELECT * FROM borrowing WHERE borrower_id='$saya' ORDER BY id DESC LIMIT 5");
                            $iteration = 1;
                            while ($borrowing = mysqli_fetch_assoc($result)) {
                                $bookdata = mysqli_query($con, "SELECT * FROM book WHERE id={$borrowing['book_id']}");
                                $book = mysqli_fetch_assoc($bookdata);
                                ?>
                                <tr>
                                    <td class="border-bottom-0"><h6 class="fw-semibold mb-0"><?= $iteration ?></h6></td>                                   
                                    <td class="border-bottom-0">
                                        <h6 class="fw-semibold mb-1"><?= $book['title'] ?> (<?= $book['tahun'] ?>)</h6>
                                        <small>Kategori: <?= $book['category'] ?> | Lokasi: <?= $book['nomor_rak_buku'] ?></small>
                                    </td>
                                    <td class="border-bottom-0 text-center"><?= date('d-m-Y', strtotime($borrowing['loan_date'])) ?></td>

                                    <!-- Perbaikan bagian tenggat pengembalian -->
                                    <td class="border-bottom-0 text-center">
                                        <?php if ($borrowing['status'] === "Request Peminjaman") : ?>
                                            -
                                        <?php else: ?>
                                            <?= date('d-m-Y', strtotime($borrowing['return_date'])) ?>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Status -->
                                    <td class="border-bottom-0">
                                        <div class="d-flex align-items-center gap-2">
                                            <?php
                                            $returnDateTime = new DateTime($borrowing['return_date']);
                                            $currentDate = new DateTime();

                                            if ($borrowing['status'] === "Belum Kembali") {
                                                if ($returnDateTime < $currentDate) {
                                                    $dateDifference = $currentDate->diff($returnDateTime)->days;
                                                    ?>
                                                    <span class="badge bg-danger rounded-3 fw-semibold" style="width: 145px;">
                                                        Belum Kembali<br>(Terlambat <?= $dateDifference ?> hari)
                                                    </span>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <span class="badge bg-warning text-dark rounded-3 fw-semibold" style="width: 145px;">Dipinjam</span>
                                                    <?php
                                                }
                                            } elseif ($borrowing['status'] === "Request Peminjaman") {
                                                ?>
                                                <span class="badge bg-primary rounded-3 fw-semibold" style="width: 155px;">Request Peminjaman</span>
                                                <?php
                                            } else {
                                                ?>
                                                <span class="badge bg-success rounded-3 fw-semibold" style="width: 145px;"><?= $borrowing['status'] ?></span>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php $iteration++; ?>
                            <?php } ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("footer.php"); ?>
