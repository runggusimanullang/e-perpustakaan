<?php
session_start();
include_once("header.php");
include '../dashboard/koneks.php';
$borrower_id = $_SESSION['siswa_id'] ?? null;
if (!$borrower_id) {
    echo "Anda harus login sebagai siswa.";
    exit;
}
// === Aksi Pengembalian ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'process_return') {
    $borrowing_id = (int)$_POST['borrowing_id'];
    $book_condition = $_POST['book_condition'];
    $book_id = (int)$_POST['book_id'];
    
    // Ambil data peminjaman
    $getBorrowing = mysqli_query($con, "SELECT * FROM borrowing WHERE id=$borrowing_id AND borrower_id=$borrower_id AND status IN ('Dipinjam', 'Belum Kembali')");
    $borrowingData = mysqli_fetch_assoc($getBorrowing);
    
    if (!$borrowingData) {
        echo "<script>alert('Data peminjaman tidak ditemukan atau sudah dikembalikan.');</script>";
    } else {
        // Proses berdasarkan kondisi buku
        if ($book_condition === 'baik') {
            // Update status peminjaman
            $update = mysqli_query($con, "UPDATE borrowing 
                SET status='Sudah Kembali', actual_return_date=NOW() 
                WHERE id=$borrowing_id");
            
            if ($update) {
                // Tambah jumlah buku
                mysqli_query($con, "UPDATE book SET amount = amount + 1 WHERE id = $book_id");
                echo "<script>alert('Buku berhasil dikembalikan!'); window.location='pengembalian.php';</script>";
            } else {
                echo "<script>alert('Gagal mengembalikan buku!');</script>";
            }
        } 
        elseif ($book_condition === 'rusak') {
            // Update status peminjaman dengan catatan rusak
            $update = mysqli_query($con, "UPDATE borrowing 
                SET status='Rusak', actual_return_date=NOW() 
                WHERE id=$borrowing_id");
            
            if ($update) {
                // Tambah catatan kerusakan ke tabel bills
                $damage_fee = 50000; // Contoh biaya kerusakan, bisa disesuaikan
                mysqli_query($con, "INSERT INTO bills (borrowing_id, borrower_id, amount, description, status, created_at) 
                    VALUES ($borrowing_id, $borrower_id, $damage_fee, 'Biaya perbaikan buku rusak', 'Belum Dibayar', NOW())");
                
                echo "<script>alert('Buku rusak telah dicatat. Silakan lakukan pembayaran biaya perbaikan.'); window.location='pengembalian.php';</script>";
            } else {
                echo "<script>alert('Gagal mencatat buku rusak!');</script>";
            }
        } 
        elseif ($book_condition === 'hilang') {
            // Validasi pilihan hilang
            if (!isset($_POST['loss_option'])) {
                echo "<script>alert('Silakan pilih opsi untuk buku hilang!'); window.history.back();</script>";
                exit;
            }
            
            $loss_option = $_POST['loss_option'];
            
            if ($loss_option === 'bayar') {
                // Ambil harga buku
                $book_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT price FROM book WHERE id = $book_id"));
                $loss_fee = $book_info['price'] ?? 100000;
                // Update status peminjaman jadi menunggu pembayaran
                mysqli_query($con, "UPDATE borrowing 
                    SET status='Hilang - Menunggu Pembayaran', actual_return_date=NOW() 
                    WHERE id=$borrowing_id");
                // Buat tagihan di tabel bills
                mysqli_query($con, "INSERT INTO bills (borrowing_id, borrower_id, amount, description, status, created_at) 
                    VALUES ($borrowing_id, $borrower_id, $loss_fee, 'Biaya penggantian buku hilang', 'Belum Dibayar', NOW())");
                $bill_id = mysqli_insert_id($con);
                // Arahkan ke halaman upload bukti pembayaran
                echo "<script>
                    alert('Buku hilang dicatat. Silakan unggah bukti pembayaran penggantian sebesar Rp " . number_format($loss_fee, 0, ',', '.') . "');
                    window.location='upload_bukti.php?bill_id=$bill_id';
                </script>";
            } 
            elseif ($loss_option === 'ganti') {
                // Proses ganti buku seperti sebelumnya
                if (!empty($_FILES['book_photo']['name']) && !empty($_POST['new_book_title'])) {
                    $new_book_title = mysqli_real_escape_string($con, $_POST['new_book_title']);
                    
                    // Upload foto buku
                    $target_dir = "../assets/images/book/";
                    $file_extension = pathinfo($_FILES["book_photo"]["name"], PATHINFO_EXTENSION);
                    $new_filename = uniqid() . '.' . $file_extension;
                    $target_file = $target_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES["book_photo"]["tmp_name"], $target_file)) {
                        $book_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT adminor, publisher, tahun, category, nomor_rak_buku, isbn FROM book WHERE id = $book_id"));
                        
                        mysqli_query($con, "INSERT INTO book (title, adminor, publisher, tahun, category, photo_filename, amount, nomor_rak_buku, isbn) 
                            VALUES ('$new_book_title', '{$book_info['adminor']}', '{$book_info['publisher']}', {$book_info['tahun']}, '{$book_info['category']}', '$new_filename', 1, '{$book_info['nomor_rak_buku']}', '{$book_info['isbn']}')");
                        
                        mysqli_query($con, "UPDATE borrowing 
                            SET status='Sudah Kembali', actual_return_date=NOW() 
                            WHERE id=$borrowing_id");
                        
                        echo "<script>alert('Buku pengganti berhasil ditambahkan dan pengembalian selesai.'); window.location='pengembalian.php';</script>";
                    } else {
                        echo "<script>alert('Gagal mengupload foto buku!');</script>";
                    }
                } else {
                    echo "<script>alert('Silakan lengkapi data buku pengganti!');</script>";
                }
            }
        }
    }
}
// === Aksi Upload Bukti Pembayaran ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_payment_proof') {
    $borrowing_id = (int)$_POST['borrowing_id'];
    $bill_id = (int)$_POST['bill_id'];
    
    // PERBAIKAN: Cek apakah ada bukti pembayaran sebelumnya
    $check_bill = mysqli_fetch_assoc(mysqli_query($con, "SELECT payment_proof FROM bills WHERE id = $bill_id"));
    $old_payment_proof = $check_bill ? $check_bill['payment_proof'] : null;
    
    // Proses upload file
    if (!empty($_FILES['payment_proof']['name'])) {
        $target_dir = "../assets/images/payments/";
        // Buat direktori jika belum ada
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES["payment_proof"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["payment_proof"]["tmp_name"], $target_file)) {
            // PERBAIKAN: Hapus file lama jika ada
            if ($old_payment_proof && file_exists($target_dir . $old_payment_proof)) {
                unlink($target_dir . $old_payment_proof);
            }
            
            // Update tabel bills dengan bukti pembayaran
            $update = mysqli_query($con, "UPDATE bills SET payment_proof = '$new_filename', status = 'Menunggu Konfirmasi' WHERE id = $bill_id");
            
            if ($update) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data pembayaran.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengupload file.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Tidak ada file yang diupload.']);
    }
    exit;
}
// Ambil daftar buku yang masih dipinjam siswa atau memiliki tagihan yang belum selesai
$query = mysqli_query($con, "
    SELECT b.id, bk.title, bk.photo_filename, b.loan_date, b.return_date, bk.id as book_id, bk.price, b.status
    FROM borrowing b
    JOIN book bk ON b.book_id = bk.id
    WHERE b.borrower_id = $borrower_id AND b.status IN ('Dipinjam', 'Belum Kembali', 'Rusak', 'Hilang - Menunggu Pembayaran')
");
// Periksa juga apakah ada tagihan yang belum dikonfirmasi
$pending_payments = [];
$bills_query = mysqli_query($con, "
    SELECT bil.borrowing_id, bil.status as bill_status, bil.payment_proof
    FROM bills bil
    JOIN borrowing b ON bil.borrowing_id = b.id
    WHERE bil.borrower_id = $borrower_id AND bil.status IN ('Belum Dibayar', 'Menunggu Konfirmasi')
");
while ($bill = mysqli_fetch_assoc($bills_query)) {
    $pending_payments[$bill['borrowing_id']] = [
        'status' => $bill['bill_status'],
        'payment_proof' => $bill['payment_proof']
    ];
}
// Simpan hasil query ke dalam array
$borrowed_books = [];
while ($row = mysqli_fetch_assoc($query)) {
    // Pastikan harga ada, jika tidak gunakan default
    if (!isset($row['price'])) {
        $row['price'] = 100000; // Default price
    }
    $borrowed_books[] = $row;
}
?>
<div class="container mt-5">
    <h3 class="mb-4 text-primary">📚 Daftar Buku yang Sedang Dipinjam</h3>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php foreach ($borrowed_books as $row) { 
            $cover = !empty($row['photo_filename']) ? "../assets/images/book/{$row['photo_filename']}" : "../assets/images/book/default.png";
            
            // Tentukan kelas badge berdasarkan status
            $status_class = '';
            $status_text = $row['status'];
            
            switch ($row['status']) {
                case 'Dipinjam':
                    $status_class = 'bg-primary';
                    break;
                case 'Belum Kembali':
                    $status_class = 'bg-warning';
                    break;
                case 'Rusak':
                    $status_class = 'bg-danger';
                    break;
                case 'Hilang - Menunggu Pembayaran':
                    $status_class = 'bg-info';
                    break;
            }
            
            // Periksa apakah ada pembayaran pending
            $has_pending_payment = isset($pending_payments[$row['id']]);
            $payment_status = $has_pending_payment ? $pending_payments[$row['id']]['status'] : '';
            $has_payment_proof = $has_pending_payment && !empty($pending_payments[$row['id']]['payment_proof']);
            
            // PERBAIKAN: Tampilkan tombol upload jika status pembayaran "Belum Dibayar" meskipun sudah ada bukti pembayaran sebelumnya
            $show_upload_button = ($row['status'] === 'Rusak' || $row['status'] === 'Hilang - Menunggu Pembayaran') && 
                                 (!$has_payment_proof || $payment_status === 'Belum Dibayar');
        ?>
        <div class="col">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="img-wrapper bg-light d-flex justify-content-center align-items-center" style="height: 200px; overflow: hidden; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                    <img src="<?= $cover ?>" alt="cover buku" style="max-height: 180px; max-width: 100%; object-fit: contain;">
                    <?php if ($has_pending_payment) { ?>
                        <div class="position-absolute top-0 end-0 p-2">
                            <span class="badge bg-danger">Menunggu Pembayaran</span>
                        </div>
                    <?php } ?>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title text-dark mb-0"><?= htmlspecialchars($row['title']) ?></h5>
                        <span class="badge <?= $status_class ?>"><?= $status_text ?></span>
                    </div>
                    <p class="card-text mb-1"><i class="bi bi-calendar-event"></i> <strong>Pinjam:</strong> <?= $row['loan_date'] ?></p>
                    <p class="card-text"><i class="bi bi-clock-history"></i> <strong>Kembali:</strong> <?= $row['return_date'] ?></p>
                    
                    <?php if ($has_pending_payment) { ?>
                        <div class="alert alert-warning py-2 mb-2">
                            <small>
                                <?php if ($payment_status === 'Belum Dibayar') { ?>
                                    <i class="bi bi-exclamation-triangle"></i> Anda memiliki tagihan yang belum dibayar
                                <?php } elseif ($payment_status === 'Menunggu Konfirmasi') { ?>
                                    <i class="bi bi-clock"></i> Menunggu konfirmasi pembayaran dari admin
                                <?php } ?>
                            </small>
                        </div>
                    <?php } ?>
                    
                    <div class="mt-auto">
                        <?php if ($row['status'] === 'Dipinjam' || $row['status'] === 'Belum Kembali') { ?>
                            <button type="button" class="btn btn-success btn-sm w-100 rounded-pill mt-3" 
                                    data-bs-toggle="modal" data-bs-target="#returnModal<?= $row['id'] ?>">
                                <i class="bi bi-arrow-return-left"></i> Kembalikan Buku
                            </button>
                        <?php } elseif ($show_upload_button) { ?>
                            <button type="button" class="btn btn-warning btn-sm w-100 rounded-pill mt-3" 
                                    data-bs-toggle="modal" data-bs-target="#paymentModal<?= $row['id'] ?>">
                                <i class="bi bi-cash"></i> Upload Bukti Pembayaran
                            </button>
                        <?php } elseif ($has_payment_proof && $payment_status === 'Menunggu Konfirmasi') { ?>
                            <button type="button" class="btn btn-info btn-sm w-100 rounded-pill mt-3" disabled>
                                <i class="bi bi-check-circle"></i> Menunggu Konfirmasi
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal Pengembalian Buku -->
        <div class="modal fade" id="returnModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="returnModalLabel<?= $row['id'] ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="returnModalLabel<?= $row['id'] ?>">Form Pengembalian Buku</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" enctype="multipart/form-data" id="returnForm<?= $row['id'] ?>">
                        <div class="modal-body">
                            <input type="hidden" name="action" value="process_return">
                            <input type="hidden" name="borrowing_id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="book_id" value="<?= $row['book_id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Judul Buku: <strong><?= htmlspecialchars($row['title']) ?></strong></label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kondisi Buku Saat Dikembalikan:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="book_condition" id="conditionBaik<?= $row['id'] ?>" value="baik" checked>
                                    <label class="form-check-label" for="conditionBaik<?= $row['id'] ?>">
                                        Baik (tidak ada kerusakan)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="book_condition" id="conditionRusak<?= $row['id'] ?>" value="rusak">
                                    <label class="form-check-label" for="conditionRusak<?= $row['id'] ?>">
                                        Rusak (ada kerusakan pada buku)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="book_condition" id="conditionHilang<?= $row['id'] ?>" value="hilang">
                                    <label class="form-check-label" for="conditionHilang<?= $row['id'] ?>">
                                        Hilang (buku tidak dapat dikembalikan)
                                    </label>
                                </div>
                            </div>
                            <!-- Opsi Hilang -->
                            <div class="mb-3" id="lossOptions<?= $row['id'] ?>" style="display: none;">
                                <label class="form-label">Pilihan untuk buku hilang:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="loss_option" id="lossBayar<?= $row['id'] ?>" value="bayar">
                                    <label class="form-check-label" for="lossBayar<?= $row['id'] ?>">
                                        Bayar biaya penggantian buku (Rp. <?= number_format($row['price'], 0, ',', '.') ?>)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="loss_option" id="lossGanti<?= $row['id'] ?>" value="ganti">
                                    <label class="form-check-label" for="lossGanti<?= $row['id'] ?>">
                                        Ganti dengan buku baru
                                    </label>
                                </div>
                            </div>
                            <!-- Form Ganti Buku -->
                            <div class="mb-3" id="newBookForm<?= $row['id'] ?>" style="display: none;">
                                <label class="form-label">Data Buku Pengganti:</label>
                                <div class="mb-2">
                                    <label for="newBookTitle<?= $row['id'] ?>" class="form-label">Judul Buku Baru:</label>
                                    <input type="text" class="form-control" id="newBookTitle<?= $row['id'] ?>" name="new_book_title">
                                </div>
                                <div class="mb-2">
                                    <label for="newBookPhoto<?= $row['id'] ?>" class="form-label">Foto Buku Baru:</label>
                                    <input type="file" class="form-control" id="newBookPhoto<?= $row['id'] ?>" name="book_photo" accept="image/*">
                                </div>
                            </div>
                            <!-- Form Pembayaran -->
                            <div class="mb-3" id="paymentForm<?= $row['id'] ?>" style="display: none;">
                                <label class="form-label">Pembayaran:</label>
                                <div class="mb-2">
                                    <img src="assets/images/qris.jpeg" alt="QRIS" style="max-width: 100%; height: auto; border: 1px solid #ccc; padding: 5px;">
                                    <p class="mt-2 mb-1"><strong>No. Rekening:</strong> 1234567890 (Bank ABC)</p>
                                    <p><strong>Total Bayar:</strong> Rp. <?= number_format($row['price'], 0, ',', '.') ?></p>
                                </div>
                                <div class="alert alert-info p-2">Silakan lakukan pembayaran dan konfirmasi ke petugas perpustakaan.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Proses Pengembalian</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Modal Pembayaran untuk buku rusak/hilang -->
        <?php if ($row['status'] === 'Rusak' || $row['status'] === 'Hilang - Menunggu Pembayaran') { 
            // Ambil informasi tagihan
            $bill_query = mysqli_query($con, "SELECT * FROM bills WHERE borrowing_id = {$row['id']} AND borrower_id = $borrower_id");
            $bill = mysqli_fetch_assoc($bill_query);
            $bill_amount = $bill ? $bill['amount'] : 0;
            $bill_id = $bill ? $bill['id'] : 0;
            
            // PERBAIKAN: Tambahkan informasi tentang status pembayaran saat ini
            $current_payment_status = $bill ? $bill['status'] : '';
            $has_previous_payment = $bill && !empty($bill['payment_proof']);
        ?>
        <div class="modal fade" id="paymentModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="paymentModalLabel<?= $row['id'] ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel<?= $row['id'] ?>">
                            <?php if ($row['status'] === 'Rusak') { ?>
                                Pembayaran Denda Buku Rusak
                            <?php } else { ?>
                                Pembayaran Penggantian Buku Hilang
                            <?php } ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Judul Buku: <strong><?= htmlspecialchars($row['title']) ?></strong></label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Pembayaran:</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" value="<?= number_format($bill_amount, 0, ',', '.') ?>" readonly>
                            </div>
                        </div>
                        
                        <?php if ($has_previous_payment && $current_payment_status === 'Belum Dibayar') { ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i> Anda telah mengupload bukti pembayaran sebelumnya, tetapi status telah diubah kembali menjadi "Belum Dibayar". Silakan upload bukti pembayaran yang baru.
                            </div>
                        <?php } ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="paymentTransfer<?= $row['id'] ?>" value="transfer" checked>
                                <label class="form-check-label" for="paymentTransfer<?= $row['id'] ?>">
                                    Transfer Bank
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="paymentQris<?= $row['id'] ?>" value="qris">
                                <label class="form-check-label" for="paymentQris<?= $row['id'] ?>">
                                    QRIS
                                </label>
                            </div>
                        </div>
                        <div id="paymentDetails<?= $row['id'] ?>">
                            <div class="mb-3">
                                <img src="assets/images/qris.jpeg" alt="QRIS" style="max-width: 100%; height: auto; border: 1px solid #ccc; padding: 5px;">
                                <p class="mt-2 mb-1"><strong>No. Rekening:</strong> 1234567890 (Bank ABC)</p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="paymentProof<?= $row['id'] ?>" class="form-label">Upload Bukti Pembayaran:</label>
                            <input type="file" class="form-control" id="paymentProof<?= $row['id'] ?>" name="payment_proof" accept="image/*">
                        </div>
                        <div class="alert alert-info">
                            Setelah mengupload bukti pembayaran, silakan tunggu konfirmasi dari admin.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" onclick="uploadPaymentProof(<?= $row['id'] ?>, <?= $bill_id ?>)">Upload Bukti Pembayaran</button>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php } ?>
        
        <?php if (empty($borrowed_books)) { ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> Anda tidak memiliki buku yang sedang dipinjam atau dalam proses pengembalian.
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Untuk setiap modal
        <?php foreach ($borrowed_books as $row) { ?>
        const conditionHilang<?= $row['id'] ?> = document.getElementById('conditionHilang<?= $row['id'] ?>');
        const lossOptions<?= $row['id'] ?> = document.getElementById('lossOptions<?= $row['id'] ?>');
        const lossGanti<?= $row['id'] ?> = document.getElementById('lossGanti<?= $row['id'] ?>');
        const newBookForm<?= $row['id'] ?> = document.getElementById('newBookForm<?= $row['id'] ?>');
        const returnForm<?= $row['id'] ?> = document.getElementById('returnForm<?= $row['id'] ?>');
        const lossBayar<?= $row['id'] ?> = document.getElementById('lossBayar<?= $row['id'] ?>');
        const paymentForm<?= $row['id'] ?> = document.getElementById('paymentForm<?= $row['id'] ?>');
        
        if (conditionHilang<?= $row['id'] ?>) {
            conditionHilang<?= $row['id'] ?>.addEventListener('change', function () {
                if (this.checked) {
                    lossOptions<?= $row['id'] ?>.style.display = 'block';
                } else {
                    lossOptions<?= $row['id'] ?>.style.display = 'none';
                    newBookForm<?= $row['id'] ?>.style.display = 'none';
                    paymentForm<?= $row['id'] ?>.style.display = 'none';
                }
            });
        }
        
        if (lossBayar<?= $row['id'] ?>) {
            lossBayar<?= $row['id'] ?>.addEventListener('change', function () {
                if (this.checked) {
                    paymentForm<?= $row['id'] ?>.style.display = 'block';
                    newBookForm<?= $row['id'] ?>.style.display = 'none';
                }
            });
        }
        if (lossGanti<?= $row['id'] ?>) {
            lossGanti<?= $row['id'] ?>.addEventListener('change', function () {
                if (this.checked) {
                    newBookForm<?= $row['id'] ?>.style.display = 'block';
                    paymentForm<?= $row['id'] ?>.style.display = 'none';
                }
            });
        }
        
        // Validasi form sebelum submit
        returnForm<?= $row['id'] ?>?.addEventListener('submit', function(e) {
            const conditionHilangChecked = document.getElementById('conditionHilang<?= $row['id'] ?>').checked;
            
            if (conditionHilangChecked) {
                const lossOptionSelected = document.querySelector('input[name="loss_option"]:checked');
                
                if (!lossOptionSelected) {
                    e.preventDefault();
                    alert('Silakan pilih opsi untuk buku hilang!');
                    return;
                }
                
                if (lossOptionSelected.value === 'ganti') {
                    const newBookTitle = document.getElementById('newBookTitle<?= $row['id'] ?>').value;
                    const newBookPhoto = document.getElementById('newBookPhoto<?= $row['id'] ?>').files.length;
                    
                    if (!newBookTitle || newBookPhoto === 0) {
                        e.preventDefault();
                        alert('Silakan lengkapi data buku pengganti!');
                        return;
                    }
                }
            }
        });
        <?php } ?>
    });
    
    function uploadPaymentProof(borrowing_id, bill_id) {
        const fileInput = document.getElementById(`paymentProof${borrowing_id}`);
        
        if (fileInput.files.length === 0) {
            alert('Silakan pilih file bukti pembayaran!');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'upload_payment_proof');
        formData.append('borrowing_id', borrowing_id);
        formData.append('bill_id', bill_id);
        formData.append('payment_proof', fileInput.files[0]);
        
        fetch('pengembalian.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Bukti pembayaran berhasil diupload! Silakan tunggu konfirmasi dari admin.');
                location.reload();
            } else {
                alert('Gagal mengupload bukti pembayaran: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengupload bukti pembayaran.');
        });
    }
</script>
<!-- Tambahkan Bootstrap JS jika belum ada -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<style>
    body {
        background-color: #f9f9fa;
    }
    h3 {
        font-weight: bold;
    }
    .img-wrapper {
        background-color: #f1f1f1;
        height: 200px;
        border-radius: 1rem 1rem 0 0;
        position: relative;
    }
    .card-img-top {
        object-fit: contain;
        max-height: 180px;
        width: auto;
        margin: auto;
    }
    .badge {
        font-size: 0.75rem;
    }
</style>