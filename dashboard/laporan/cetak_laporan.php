<?php
include_once("../koneks.php");
require('../fpdf/fpdf.php');

$start = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$where = '';
if ($start && $end) {
    $where = "WHERE loan_date BETWEEN '$start' AND '$end'";
}

$query = "SELECT * FROM borrowing $where ORDER BY id DESC";
$result = mysqli_query($con, $query);

$pdf = new FPDF('L', 'mm', 'A5');
$pdf->AddPage();

// ========== LOGO & HEADER ==========
$pdf->Image('C:/xampp/htdocs/perpusada/assets/images/logos/samarfa.jpg', 10, 10, 20);

$pdf->SetY(12);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, utf8_decode('LAPORAN PEMINJAMAN BUKU'), 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, utf8_decode('SD SANTA MARIA FATIMA'), 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Jalan Jatinegara Barat No.122 Bidara Cina, Jatinegara, RT.7/RW.1, Kp. Melayu,', 0, 1, 'C');
$pdf->Cell(0, 5, 'Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13320', 0, 1, 'C');
$pdf->Cell(0, 5, 'Telepon: (021) 85902383', 0, 1, 'C');

$pdf->Ln(1);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// ========== PERIODE ==========
if ($start && $end) {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 6, "Periode: $start s.d $end", 0, 1, 'C');
    $pdf->Ln(3);
}

// ========== HEADER TABEL ==========
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(8, 7, 'No', 1, 0, 'C', true);
$pdf->Cell(35, 7, 'Peminjam', 1, 0, 'C', true);
$pdf->Cell(48, 7, 'Judul Buku', 1, 0, 'C', true);
$pdf->Cell(23, 7, 'Pinjam', 1, 0, 'C', true);
$pdf->Cell(23, 7, 'Jatuh Tempo', 1, 0, 'C', true);
$pdf->Cell(23, 7, 'Dikembalikan', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'Status', 1, 1, 'C', true);

// ========== ISI TABEL ==========
$pdf->SetFont('Arial', '', 9);
$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $siswa = mysqli_fetch_assoc(mysqli_query($con, "SELECT fullname FROM siswa WHERE id = {$row['borrower_id']}"));
    $book = mysqli_fetch_assoc(mysqli_query($con, "SELECT title FROM book WHERE id = {$row['book_id']}"));

    $loanDate = date('d-m-Y', strtotime($row['loan_date']));
    if (strtolower($row['status']) === 'request peminjaman') {
    $returnDate = '';
} else {
    $returnDate = date('d-m-Y', strtotime($row['return_date']));
}

    // ✅ Perbaikan: hanya tampilkan tanggal dikembalikan jika status "Sudah Kembali"
    $actualDateRaw = trim($row['actual_return_date']);
$status = strtolower(trim($row['status']));

$actualReturn = '';
if (
    $status === 'sudah kembali' &&
    !empty($actualDateRaw) &&
    $actualDateRaw !== '0000-00-00' &&
    $actualDateRaw !== '0000-00-00 00:00:00' &&
    strtolower($actualDateRaw) !== 'null'
) {
    $actualReturn = date('d-m-Y', strtotime($actualDateRaw));
}


    $pdf->Cell(8, 7, $no++, 1, 0, 'C');
    $pdf->Cell(35, 7, utf8_decode($siswa['fullname']), 1, 0);
    $pdf->Cell(48, 7, utf8_decode($book['title']), 1, 0);
    $pdf->Cell(23, 7, $loanDate, 1, 0, 'C');
    $pdf->Cell(23, 7, $returnDate, 1, 0, 'C');
    $pdf->Cell(23, 7, $actualReturn, 1, 0, 'C');

 // Tentukan teks dan warna status
$statusText = $row['status'];
if ($status === 'belum kembali') {
    $returnDateTime = new DateTime($row['return_date']);
    $currentDate = new DateTime();
    if ($currentDate <= $returnDateTime) {
        $statusText = 'Dipinjam';
        $pdf->SetTextColor(255, 215, 0); // Kuning
    } else {
        $daysLate = $currentDate->diff($returnDateTime)->days;
        $statusText = "Terlambat {$daysLate} hari";
        $pdf->SetTextColor(255, 0, 0); // Merah
    }
} elseif ($status === 'sudah kembali') {
    $statusText = $row['status'];
    $pdf->SetTextColor(0, 128, 0); // Hijau
} elseif ($status === 'request peminjaman') {
    $statusText = 'Request';
    $pdf->SetTextColor(0, 0, 255); // Biru
} else {
    $statusText = $row['status'];
    $pdf->SetTextColor(0, 0, 0); // Hitam
}


// Cetak status
$pdf->Cell(25, 7, utf8_decode($statusText), 1, 1, 'C');
$pdf->SetTextColor(0, 0, 0); // Reset warna

}

// ========== KOSONG ==========
if ($no === 1) {
    $pdf->Cell(0, 10, 'Tidak ada data ditemukan.', 1, 1, 'C');
}

$pdf->Output('I', 'Laporan_Peminjaman_Buku.pdf');
?>
