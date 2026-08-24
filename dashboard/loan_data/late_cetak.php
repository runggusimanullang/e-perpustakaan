<?php
session_start();
include_once("../koneks.php");
require('../fpdf/fpdf.php');

// Instansiasi FPDF
$pdf = new FPDF('L', 'mm', 'A5'); 
$pdf->SetAutoPageBreak(true, 10); 
$pdf->AddPage();

// Logo
$pdf->Image('C:/xampp/htdocs/perpusada/assets/images/logos/samarfa.jpg', 10, 10, 24); 

// Judul
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'E-LIBRARY SD SANTA MARIA FATIMA', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Jalan Jatinegara Barat No.122 Bidara Cina, Jatinegara, RT.7/RW.1, Kp. Melayu,', 0, 1, 'C');
$pdf->Cell(0, 5, 'Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13320', 0, 1, 'C');
$pdf->Cell(0, 5, 'Telepon: (021) 85902383', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, 'DAFTAR PEMINJAMAN TERLAMBAT', 0, 1, 'C'); // Ubah judul
$pdf->Ln(5); 

// Header tabel
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(8, 6, 'No', 1, 0, 'C');  
$pdf->Cell(10, 6, 'ID', 1, 0, 'C');  
$pdf->Cell(28, 6, 'Peminjam', 1, 0, 'C');  
$pdf->Cell(35, 6, 'Buku', 1, 0, 'C');  
$pdf->Cell(24, 6, 'Tgl Pinjam', 1, 0, 'C');  
$pdf->Cell(24, 6, 'Tenggat', 1, 0, 'C');  
$pdf->Cell(22, 6, 'Status', 1, 1, 'C');  

// Isi tabel
$pdf->SetFont('Arial', '', 8);
$data_borrow = mysqli_query(
    $con,
    "SELECT * FROM borrowing WHERE status='Belum Kembali' ORDER BY id ASC"
);

$no = 1;
$today = new DateTime();

while ($row = mysqli_fetch_assoc($data_borrow)) {
    $return_date = new DateTime($row['return_date']);
    if ($today <= $return_date) {
        // Lewati jika belum terlambat
        continue;
    }

    // Jika sudah lewat tenggat
    $diff = $today->diff($return_date)->days;
    $status_text = "Terlambat {$diff} hari";

    $borrower = mysqli_fetch_assoc(
        mysqli_query($con, "SELECT fullname FROM siswa WHERE id=".$row['borrower_id'])
    );

    $book = mysqli_fetch_assoc(
        mysqli_query($con, "SELECT title FROM book WHERE id=".$row['book_id'])
    );

    $pdf->Cell(8, 6, $no++, 1, 0, 'C');  
    $pdf->Cell(10, 6, $row['id'], 1, 0, 'C');  
    $pdf->Cell(28, 6, mb_strimwidth($borrower['fullname'], 0, 18, '...'), 1, 0);  
    $pdf->Cell(35, 6, mb_strimwidth($book['title'], 0, 22, '...'), 1, 0);  
    $pdf->Cell(24, 6, $row['loan_date'], 1, 0, 'C');  
    $pdf->Cell(24, 6, $row['return_date'], 1, 0, 'C');  
    $pdf->Cell(22, 6, $status_text, 1, 1, 'C');  
}

// Output file
$pdf->Output();
