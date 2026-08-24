<?php   include_once("header.php"); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">


<h3 class="">Riwayat Peminjaman Buku</h3>
<div class="row">
    <div class="col-lg-12 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body p-4">   
            <a href="peminjaman_tambah.php" class="btn btn-primary w-20 py-8 fs-4 mb-4 rounded-1">Peminjaman</a>


                <form action="" method="post">
                    <div class="mb-3 " style="display: flex; align-items: center;">
                        <input type="text" class="form-control input" name="cari"
                        placeholder="Cari berdasarkan nama peminjam dan judul buku" style="flex: 1;">
                        <button name="search" class="btn btn-primary mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                <path d="M21 21l-6 -6" />
                            </svg>
                        </button>
                    </div>
                </form>


                <div class="table-responsive">

                    <?php 
                    if(isset($_POST['cari'])){
                        $cari = $_POST['cari'];
                        echo "<b>Hasil pencarian : ".$cari."</b>";
                    }
                    ?>


                    <?php 
// ... kode sebelumnya ...
?>
<table class="table text-nowrap mb-0 align-middle">
    <thead class="text-dark fs-4">
        <tr>
            <th class="border-bottom-0">
                <h6 class="fw-semibold mb-0">No</h6>
            </th>                               
            <th class="border-bottom-0">
                <h6 class="fw-semibold mb-0">Nama Buku</h6>
            </th>
            <th class="border-bottom-0">
                <h6 class="fw-semibold mb-0 text-center">Tanggal Pinjam</h6>
            </th>
            <th class="border-bottom-0">
                <h6 class="fw-semibold mb-0 text-center">Tenggat Pengembalian</h6>
            </th>
            <th class="border-bottom-0">
                <h6 class="fw-semibold mb-0 text-center">Dikembalikan</h6>
            </th>
            <th class="border-bottom-0 ">
                <h6 class="fw-semibold mb-0 mx-4">Status</h6>
            </th>
            <th class="border-bottom-0">
                <h6 class="fw-semibold mb-0 text-center">Aksi</h6>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $saya = $_SESSION['siswa_id'] ?? null;
        $iteration = 0;
        $query = "SELECT borrowing.*, 
        book.title, book.tahun, book.nomor_rak_buku, book.photo_filename AS book_photo,
        siswa.fullname, siswa.photo_filename AS siswa_photo
        FROM borrowing
        JOIN book ON borrowing.book_id=book.id
        JOIN siswa ON borrowing.borrower_id=siswa.id
        WHERE borrowing.borrower_id='$saya'";
        
        if(isset($_POST['search'])){
            $cari = mysqli_real_escape_string($con,$_POST['cari']);
            $query .= " AND (book.title LIKE '%$cari%' OR siswa.fullname LIKE '%$cari%')";
        }
        $query .= " ORDER BY borrowing.id DESC";
        $result = mysqli_query($con,$query);
        
        while($row = mysqli_fetch_assoc($result)){ 
            $iteration++; 
            // Gunakan $row untuk mengakses data
            ?>
            <tr>
                <td class="border-bottom-0">
                    <h6 class="fw-semibold mb-0"><?= $iteration ?></h6>
                </td>                                   
                <td class="border-bottom-0">
                    <!-- Hapus query ulang, gunakan data dari $row -->
                    <h6 class="fw-semibold mb-1"><?= $row['title'] ?> (<?= $row['tahun'] ?>)</h6>
                    <small>Lokasi Rak (<?= $row['nomor_rak_buku'] ?>)</small>
                </td>
                <td class="border-bottom-0 text-center">
                    <h6 class="fw-semibold mb-1"><?= date('d-m-Y', strtotime($row['loan_date'])) ?></h6>
                </td>
                <td class="border-bottom-0 text-center">
                    <h6 class="fw-semibold mb-1">
                        <?php
                        if ($row['status'] === "Request Peminjaman") {
                            echo '-';
                        } else {
                            echo date('d-m-Y', strtotime($row['return_date']));
                        }
                        ?>
                    </h6>
                </td>
                <td class="border-bottom-0 text-center">
                    <h6 class="fw-semibold mb-1">
                        <?php 
                        if ($row['status'] === "Sudah Kembali" && !empty($row['actual_return_date'])) {
                            echo date('Y-m-d', strtotime($row['actual_return_date']));
                        } else {
                            echo "-";
                        }
                        ?>
                    </h6>
                </td>
                <td class="border-bottom-0">
                    <div class="d-flex align-items-center gap-2">
                        <?php
                        if ($row['status'] === "Request Peminjaman") {
                            echo '<span class="badge bg-primary rounded-3 fw-semibold" style="width: 145px;">Request Peminjaman</span>';
                        } elseif ($row['status'] === "Dipinjam") {
                            $returnDateTime = new DateTime($row['return_date']);
                            $currentDate = new DateTime();
                            if ($returnDateTime < $currentDate) {
                                $daysDifference = $currentDate->diff($returnDateTime)->days;
                                echo '<span class="badge bg-danger rounded-3 fw-semibold" style="width:145px;">Belum Kembali<br>(Terlambat ' . $daysDifference . ' hari)</span>';
                            } else {
                                echo '<span class="badge bg-warning text-dark rounded-3 fw-semibold" style="width: 145px;">Dipinjam</span>';
                            }
                        } elseif ($row['status'] === "Belum Kembali") {
                            $returnDateTime = new DateTime($row['return_date']);
                            $currentDate = new DateTime();
                            $daysDifference = $currentDate->diff($returnDateTime)->days;
                            echo '<span class="badge bg-danger rounded-3 fw-semibold" style="width:145px;">Belum Kembali<br>(Terlambat ' . $daysDifference . ' hari)</span>';
                        } else {
                            echo '<span class="badge bg-success rounded-3 fw-semibold" style="width: 145px;">Sudah Kembali</span>';
                        }
                        ?>
                    </div>
                </td>
                <td class="border-bottom-0">
                    <?php 
                    if($row['status'] == "Request Peminjaman"){
                        ?>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapus<?= $row['id'] ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M4 7l16 0"></path>
                                <path d="M10 11l0 6"></path>
                                <path d="M14 11l0 6"></path>
                                <path d="M5 7l1 12a 2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                            </svg>
                        </button> 

                        <!-- The Modal -->
                        <div class="modal" id="hapus<?= $row['id'] ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form role="form" method="post" action="peminjaman_hapus.php">  
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Hapus Peminjaman</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <span class="badge bg-danger mb-3">
                                                Penting !! Status Masih <b>Request Peminjaman.</b><br>Jadi Anda Masih bisa menghapus data peminjaman
                                            </span>
                                            <h5>Apakah Anda yakin ingin menghapus data ini?</h5>
                                        </div>
                                        <div class="modal-footer justify-content-center">
                                            <button type="submit" class="btn btn-success">Ya</button>
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tidak</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <a href="peminjaman_cetak.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-text" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21H7a2 2 0 0 1 -2 -2V5a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                            <path d="M9 9h1m-1 4h6m-6 4h6" />
                        </svg>
                    </a>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#detail<?= $row['id'] ?>">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                        </svg>
                    </button>

                    <!-- Modal Detail Peminjaman -->
                    <div class="modal fade" id="detail<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-md">
                            <div class="modal-content border-0 rounded-4 shadow-lg">
                                <div class="modal-header bg-primary text-white border-0">
                                    <h5 class="modal-title d-flex align-items-center gap-2 fs-6">
                                        <i class="bi bi-info-circle-fill"></i> Detail Peminjaman
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-3 bg-light-subtle">
                                    <div class="row g-3 mb-3">
                                        <div class="col-6 text-center">
                                            <div class="card border-0 shadow-sm p-2 h-100 hover-shadow-sm">
                                                <img
                                                    class="img-fluid rounded-circle mb-2 mx-auto border border-2 border-primary-subtle"
                                                    style="width:80px; height:80px; object-fit:cover;"
                                                    src="../assets/images/siswa/<?= htmlspecialchars($row['siswa_photo']) ?>"
                                                    alt="Peminjam"
                                                />
                                                <small class="text-muted d-block mb-1">Peminjam</small>
                                                <span class="fw-semibold"><?= htmlspecialchars($row['fullname']) ?></span>
                                            </div>
                                        </div>
                                        <div class="col-6 text-center">
                                            <div class="card border-0 shadow-sm p-2 h-100 hover-shadow-sm">
                                                <img
                                                    class="img-fluid rounded mb-2 mx-auto border border-2 border-primary-subtle"
                                                    style="width:80px; height:80px; object-fit:cover;"
                                                    src="../assets/images/book/<?= htmlspecialchars($row['book_photo']) ?>"
                                                    alt="Buku"
                                                />
                                                <small class="text-muted d-block mb-1">Buku</small>
                                                <span class="fw-semibold"><?= htmlspecialchars($row['title']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-white p-2 rounded-3 shadow-sm small">
                                        <div class="row mb-1 align-items-center">
                                            <div class="col-5 text-muted"><i class="bi bi-calendar-plus me-1 text-primary"></i> Tanggal Pinjam</div>
                                            <div class="col-7 fw-medium"><?= date('d-m-Y', strtotime($row['loan_date'])) ?></div>
                                        </div>
                                        <div class="row mb-1 align-items-center">
                                            <div class="col-5 text-muted"><i class="bi bi-calendar-check me-1 text-success"></i> Tenggat Pengembalian</div>
                                            <div class="col-7 fw-medium">
                                                <?php
                                                if ($row['status'] === "Request Peminjaman") {
                                                    echo '-';
                                                } else {
                                                    echo date('d-m-Y', strtotime($row['return_date']));
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="row mb-1 align-items-center">
                                            <div class="col-5 text-muted"><i class="bi bi-clock-history me-1 text-info"></i> Dikembalikan Pada</div>
                                            <div class="col-7 fw-medium">
                                                <?php
                                                if ($row['status'] === "Sudah Kembali" && !empty($row['actual_return_date'])) {
                                                    echo date('Y-m-d', strtotime($row['actual_return_date']));
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="row align-items-center">
                                            <div class="col-5 text-muted"><i class="bi bi-exclamation-circle me-1 text-warning"></i> Status</div>
                                            <div class="col-7 fw-medium">
                                                <?= $row['status'] ?>
                                                <?php
                                                if ($row['status'] === "Belum Kembali") {
                                                    $returnDateTime = new DateTime($row['return_date']);
                                                    $currentDate = new DateTime();
                                                    $daysDifference = $currentDate->diff($returnDateTime)->days;
                                                    if ($returnDateTime < $currentDate) {
                                                        echo '<span class="badge bg-danger ms-2">Terlambat '.$daysDifference.' hari</span>';
                                                    } else {
                                                        echo '<span class="badge bg-warning ms-2">Segera jatuh tempo</span>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 bg-light p-2">
                                    <button type="button" class="btn btn-danger btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <?php 
        }
        
        if ($iteration === 0) { ?>
            <tr>
                <td class="border-bottom-0 text-center" colspan="7">
                    <h3 class="fw-semibold mb-0 text-center">Data tidak ditemukan</h3>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<!-- ... kode setelahnya ... -->
                </div>

            </div>
        </div>
    </div>
</div>

<?php
include_once("footer.php");
?>