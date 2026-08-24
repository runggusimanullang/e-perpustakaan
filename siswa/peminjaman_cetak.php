<?php
require('../dashboard/fpdf/fpdf.php');
include '../dashboard/koneks.php';

$id = $_GET['id'];

// Ambil data peminjaman dan detail buku
$query = mysqli_query($con, "SELECT * FROM borrowing, book, siswa WHERE borrowing.id='$id' AND borrowing.book_id=book.id AND borrowing.borrower_id=siswa.id");
$data = mysqli_fetch_assoc($query);

// Inisialisasi FPDF
$pdf = new FPDF('P', 'mm', array(80, 150)); // Ukuran kecil seperti struk
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 6, 'BUKTI PEMINJAMAN BUKU', 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Ln(2);
$pdf->Cell(60, 6, 'SD Santa Maria Fatima', 0, 1, 'C');
$pdf->Cell(60, 6, '------------------------------', 0, 1, 'C');

// Isi data umum
$pdf->Ln(2);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(25, 6, 'ID Pinjam', 0, 0);
$pdf->Cell(35, 6, ': ' . $data['id'], 0, 1);

$pdf->Cell(25, 6, 'Nama', 0, 0);
$pdf->Cell(35, 6, ': ' . $data['fullname'], 0, 1);

$pdf->Cell(25, 6, 'Judul Buku', 0, 0);
$pdf->Cell(35, 6, ': ' . $data['title'], 0, 1);

$pdf->Cell(25, 6, 'Penerbit', 0, 0);
$pdf->Cell(35, 6, ': ' . $data['publisher'], 0, 1);

$pdf->Cell(25, 6, 'Tahun Buku', 0, 0);
$pdf->Cell(35, 6, ': ' . $data['tahun'], 0, 1);

$pdf->Cell(25, 6, 'ISBN', 0, 0);
$pdf->Cell(35, 6, ': ' . $data['isbn'], 0, 1);

$pdf->Cell(25, 6, 'Rak Buku', 0, 0);
$pdf->Cell(35, 6, ': ' . $data['nomor_rak_buku'], 0, 1);

// Tanggal Pinjam (biru)
$pdf->SetTextColor(0, 0, 150);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 6, 'Tgl Pinjam', 0, 0);
$pdf->Cell(35, 6, ': ' . date('d-m-Y', strtotime($data['loan_date'])), 0, 1);

// Tenggat Pengembalian hanya jika bukan "Request Peminjaman"
if ($data['status'] !== "Request Peminjaman") {
    $pdf->SetTextColor(200, 0, 0);
    $pdf->Cell(25, 4, 'Tenggat', 0, 1);
    $pdf->Cell(25, 4, 'Pengembalian', 0, 0);
    $pdf->Cell(35, 4, ': ' . date('d-m-Y', strtotime($data['return_date'])), 0, 1);
}


// Dikembalikan → Tampilkan hanya jika sudah dikembalikan
if ($data['status'] === 'Sudah Kembali') {
    $pdf->SetTextColor(0, 0, 150);
    $pdf->Cell(25, 6, 'Dikembalikan', 0, 0);
    if (!empty($data['actual_return_date']) && $data['actual_return_date'] !== '0000-00-00 00:00:00') {
        $pdf->Cell(35, 6, ': ' . date('d-m-Y', strtotime($data['actual_return_date'])), 0, 1);
    } else {
        $pdf->Cell(35, 6, ': -', 0, 1);
    }
}

// Garis pembatas
$pdf->Ln(4);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 0, '------------------------------', 0, 1, 'C');
$pdf->Ln(2);

// Status Keterangan
$pdf->SetFont('Arial', 'B', 10);
if ($data['status'] === "Sudah Kembali") {
    $pdf->SetTextColor(0, 100, 0); // Hijau tua
    $pdf->Cell(60, 5, 'Buku ini sudah dikembalikan.', 0, 1, 'C');
} else if ($data['status'] === "Request Peminjaman") {
    $pdf->SetTextColor(0, 0, 150); // Biru
    $pdf->Cell(60, 5, 'Status: Menunggu persetujuan admin.', 0, 1, 'C');
} else {
    $pdf->SetTextColor(200, 0, 0); // Merah
    $pdf->Cell(60, 5, 'Harap simpan struk ini untuk pengembalian.', 0, 1, 'C');
    $pdf->Ln(2);
    $pdf->Cell(60, 5, 'Terima kasih.', 0, 1, 'C');
}

$pdf->Output();
?>
