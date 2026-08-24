<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projecteperpus"; // Ganti dengan nama database Anda

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari form
$title = $_POST['title'];
$adminor = $_POST['adminor'];
$publisher = $_POST['publisher'];
$amount = $_POST['amount'];
$category = $_POST['category'];

// Upload foto
$photo_filename = $_FILES['photo']['name'];
$photo_tmp = $_FILES['photo']['tmp_name'];
move_uploaded_file($photo_tmp, "assets/photo/" . $photo_filename);

// Upload PDF
$pdf_filename = $_FILES['pdf']['name'];
$pdf_tmp = $_FILES['pdf']['tmp_name'];
move_uploaded_file($pdf_tmp, "assets/pdf/" . $pdf_filename);

// Simpan data ke database
$sql = "INSERT INTO book (title, adminor, publisher, amount, category, photo_filename, pdf_filename) 
        VALUES ('$title', '$adminor', '$publisher', '$amount', '$category', '$photo_filename', '$pdf_filename')";

if ($conn->query($sql) === TRUE) {
    echo "Data berhasil disimpan.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>