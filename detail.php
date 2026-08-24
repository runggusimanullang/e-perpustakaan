<?php
session_start();
require 'functions.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $book = query("SELECT * FROM book WHERE id = $id");
    if (empty($book)) {
        echo "Buku tidak ditemukan.";
        exit;
    }
    $book = $book[0];
} else {
    echo "ID buku tidak valid.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Buku</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: url('assets/images/logos/newlog.png') no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        padding: 0;
    }
    .center-wrapper {
        min-height: 100vh;
    }
    .detail-card {
        background: rgba(255, 255, 255, 0.2); /* transparan */
        border-radius: 20px;
        padding: 30px;
        backdrop-filter: blur(10px); /* membuat blur background */
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }
    .book-cover {
        border-radius: 16px;
        width: 100%;
        max-height: 320px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .book-cover:hover {
        transform: scale(1.03);
    }
    h1 {
        font-size: 1.8rem;
        font-weight: 600;
        color: #222;
    }
    .card-text {
        color: #333;
        font-size: 0.95rem;
        margin-bottom: 8px;
    }
    .btn-primary, .btn-outline-primary {
        border-radius: 50px;
        padding: 8px 24px;
        font-weight: 500;
    }
    .btn-primary {
        background: #3b82f6;
        border: none;
    }
    .btn-primary:hover {
        background: #2563eb;
    }
</style>
</head>
<body>

<div class="center-wrapper d-flex align-items-center justify-content-center p-3">
    <div class="detail-card row g-4 align-items-center w-100" style="max-width: 900px;">
        <div class="col-md-4 text-center">
            <img src="assets/images/book/<?= htmlspecialchars($book['photo_filename'] ?? 'default.png') ?>" class="book-cover" alt="Cover Buku">
        </div>
        <div class="col-md-8">
            <h1><?= htmlspecialchars($book['title']) ?></h1>
            <p class="card-text"><strong>Penulis:</strong> <?= htmlspecialchars($book['adminor']) ?></p>
            <p class="card-text"><strong>Penerbit:</strong> <?= htmlspecialchars($book['publisher']) ?></p>
            <p class="card-text"><strong>Tahun:</strong> <?= htmlspecialchars($book['tahun']) ?></p>
            <p class="card-text"><strong>ISBN:</strong> <?= htmlspecialchars($book['isbn']) ?></p>
            <p class="card-text"><strong>Deskripsi:</strong> <?= nl2br(htmlspecialchars($book['description'] ?? 'Tidak ada deskripsi')) ?></p>
            <div class="mt-4 d-flex flex-wrap gap-2">
                <a href="koleksi.php" class="btn btn-outline-primary">← Kembali</a>
                <a href="login.php" class="btn btn-primary">📖 Baca Buku</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
