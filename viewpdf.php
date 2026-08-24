<?php
// Validasi parameter file
if (!isset($_GET['file'])) {
    http_response_code(400);
    echo "File tidak ditemukan.";
    exit;
}

// Bersihkan dan amankan nama file dari user input
$filename = basename($_GET['file']); // Mencegah path traversal
$filepath = __DIR__ . "/assets/pdf/" . $filename;

// Cek apakah file benar-benar ada
if (!file_exists($filepath)) {
    http_response_code(404);
    echo "File tidak ditemukan.";
    exit;
}

// Set header agar PDF ditampilkan di browser, bukan di-download
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));

// Baca dan kirim file ke browser
readfile($filepath);
exit;