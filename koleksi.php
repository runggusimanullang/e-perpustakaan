<?php
session_start();
require 'functions.php';

// Query untuk mengambil semua buku
$books = query("SELECT * FROM book"); // Ambil semua buku

// Cek input pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Jika ada kata kunci pencarian, filter buku sesuai
if ($search) {
    $books = query("SELECT * FROM book WHERE (title LIKE '%" . htmlspecialchars($search) . "%' OR adminor LIKE '%" . htmlspecialchars($search) . "%')");
} else {
    // Jika tidak ada kata kunci pencarian, cek pemilihan kategori
    if (isset($_POST["show_all"])) {
        $books = query("SELECT * FROM book"); // Ambil semua buku
    } elseif (isset($_POST["Sastra"])) {
        $books = query("SELECT * FROM book WHERE category = 'Sastra'");
    } elseif (isset($_POST["bisnis"])) {
        $books = query("SELECT * FROM book WHERE category = 'Matematika'");
    } elseif (isset($_POST["filsafat"])) {
        $books = query("SELECT * FROM book WHERE category = 'Sains dan Teknologi'");
    } elseif (isset($_POST["novel"])) {
        $books = query("SELECT * FROM book WHERE category = 'Novel'");
    } elseif (isset($_POST["Ensiklopedia"])) {
        $books = query("SELECT * FROM book WHERE category = 'Ensiklopedia'");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512..." crossorigin="anonymous" />

    <style>
        body {
            background-color: rgba(173, 216, 230, 1);
            /* Warna latar belakang */
            background-image:
                url('assets/images/logos/samarfa.png'),
                /* Gambar latar belakang pertama */
                url('assets/images/logos/pngss.com.png');
            /* Gambar latar belakang kedua */
            background-size:
                80%,
                /* Ukuran gambar latar belakang pertama */
                90%;
            /* Ukuran gambar latar belakang kedua untuk menutupi seluruh layar */
            background-position:
                50% 150px,
                /* Posisi gambar latar belakang pertama */
                center;
            /* Posisi gambar latar belakang kedua */
            background-repeat:
                no-repeat,
                /* Tidak mengulang gambar latar belakang pertama */
                no-repeat;
            /* Tidak mengulang gambar latar belakang kedua */
            background-attachment: fixed;
            /* Gambar latar belakang tetap saat scroll */
        }

        @media (min-width: 768px) {

            /* Media query untuk tampilan desktop */
            body {
                background-size:
                    40%,
                    /* Ukuran lebih kecil untuk gambar latar belakang pertama di tampilan desktop */
                    cover;
                /* Gambar latar belakang kedua tetap menutupi seluruh layar */
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <style>
        .book-cover {
            width: 100%;
            /* Memastikan gambar memenuhi lebar kontainer */
            height: 150px;
            /* Atur tinggi yang diinginkan */
            object-fit: contain;
            /* Memastikan gambar tidak terdistorsi */
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #2f4f4f;
            min-height: 3em;
            /* agar tinggi konsisten meskipun judul pendek/panjang */
        }


        .card-text {
            font-size: 0.9rem;
            color: #555;
        }

        .card-hover:hover {
            transform: scale(1.03);
            /* Efek zoom */
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.2);
            /* Bayangan lebih jelas */
            transition: all 0.2s ease;
        }



        .card-body {
            padding: 10px;
            /* Atur jarak antara teks dan gambar */
            font-size: 1.5vw;
            /* Ukuran font mengikuti lebar viewport */
            border-top: 1px solid #ccc;
            /* Garis pembatas di atas body */
            margin-top: 25px;
            /* Menambahkan margin atas untuk menggeser body ke bawah */
        }



        /* Media query untuk desktop */
        @media (min-width: 768px) {
            .book-cover {
                height: 200px;
                /* Tinggi gambar lebih besar pada desktop */
            }
        }

        .input-group .form-control {
            background-color: rgba(255, 255, 255, 0.7);
            /* Latar belakang putih dengan transparansi */
            border: 1px solid rgba(0, 0, 0, 0.2);
            /* Border hitam dengan transparansi */
            color: #333;
            /* Warna teks */
        }

        .input-group .btn {
            background-color: rgba(0, 123, 255, 0.7);
            /* Warna latar belakang tombol biru dengan transparansi */
            border: 1px solid rgba(0, 123, 255, 0.5);
            /* Border tombol biru dengan transparansi */
            color: white;
            /* Warna teks tombol */
        }

        .content-margin {
            margin-top: 100px;
            /* Sesuaikan dengan tinggi navbar Anda */
        }

        #bukus {
            padding-top: 70px;
            /* Sesuaikan dengan tinggi navbar Anda */
        }

        .header-container {
            display: flex;
            /* Menggunakan Flexbox */
            justify-content: space-between;
            /* Menyebar elemen ke kiri dan kanan */
            align-items: center;
            /* Memusatkan elemen secara vertikal */
        }

        .button-group {
            display: flex;
            /* Menggunakan Flexbox untuk tombol */
            gap: 10px;
            /* Jarak antara tombol */
        }
    </style>

    <div class="container mt-4">
        <div class="header-container">
            <h2 class="mb-4">Daftar Buku</h2>
            <div class="button-group">
                <a href="home.php" class="btn btn-info">Dashboard</a>
                <a href="#about" class="btn btn-info">Kontak</a>
            </div>
        </div>
        <!-- Form Pencarian -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari buku/Nama Penulis..." aria-label="Cari buku..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
            </div>
        </form>
        <style>
            .btn-custom {
                background-color: blue;
                /* Warna latar belakang biru */
                color: white;
                /* Warna teks putih */
                border: none;
                /* Menghapus border */
                transition: background-color 0.3s;
                /* Efek transisi */
            }

            .btn-custom:hover {
                background-color: #333;
                /* Warna saat hover */
            }

            .btn-outline-custom {
                border: 1px solid black;
                /* Border hitam */
                color: black;
                /* Warna teks hitam */
            }

            .btn-outline-custom:hover {
                background-color: black;
                /* Warna latar belakang hitam saat hover */
                color: white;
                /* Warna teks putih saat hover */
            }

            .book-card-container {
                margin-top: 30px;
                /* Menambahkan jarak antara kategori dan kartu buku */
            }

            /* Media Query untuk tampilan ponsel */
            @media (max-width: 576px) {
                .layout-card-custom .btn {
                    margin-bottom: 5px;
                    /* Menambahkan jarak bawah antara tombol */
                    flex: 1 1 45%;
                    /* Tombol akan mengambil 45% dari lebar kontainer */
                }
            }
        </style>

        <div class="d-flex gap-2 mt-5 justify-content-center">
            <form action="" method="post">
                <div class="layout-card-custom">
                    <button class="btn btn-custom" type="submit" name="show_all">Semua</button>
                    <button type="submit" name="Sastra" class="btn btn-custom">Sastra</button>
                    <button type="submit" name="bisnis" class="btn btn-custom">Matematika</button>
                    <button type="submit" name="filsafat" class="btn btn-custom">Sains dan Teknologi</button>
                    <button type="submit" name="novel" class="btn btn-custom">Novel</button>
                    <button type="submit" name="Ensiklopedia" class="btn btn-custom">Ensiklopedia</button>
                </div>
            </form>
        </div>

        <div class="row book-card-container">
            <?php foreach ($books as $book) : ?>
                <div class="col-6 col-sm-4 col-md-2 mb-4">
                    <!-- Bungkus seluruh card dalam tag <a> -->
                    <a href="detail.php?id=<?= htmlspecialchars($book["id"]) ?>" style="text-decoration: none; color: inherit;">
                        <div class="card h-100 d-flex flex-column shadow-sm card-hover" style="cursor: pointer;">
                            <?php
                            $imagePath = !empty($book["photo_filename"])
                                ? "assets/images/book/" . $book["photo_filename"]
                                : "assets/images/book/default.png";
                            ?>
                            <div class="image-container">
                                <img src="<?= $imagePath ?>" class="card-img-top book-cover" alt="Book Cover">
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= htmlspecialchars($book["title"] ?? '') ?></h5>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

    <style>
        .image-container {
            display: flex;
            /* Menggunakan flexbox */
            justify-content: center;
            /* Memusatkan gambar secara horizontal */
            align-items: center;
            /* Memusatkan gambar secara vertikal */
            height: 200px;
            /* Tinggi kontainer gambar */
        }

        .book-cover {
            width: auto;
            /* Biarkan lebar otomatis */
            max-width: 100%;
            /* Memastikan gambar tidak lebih lebar dari kontainer */
            max-height: 100%;
            /* Memastikan gambar tidak lebih tinggi dari kontainer */
            object-fit: cover;
            /* Memastikan gambar mengisi area dengan mempertahankan aspek rasio */
            border-radius: 0.25rem;
            /* Sudut melengkung untuk gambar */
        }

        @media (max-width: 576px) {
            .image-container {
                height: 150px;
                /* Sesuaikan tinggi kontainer gambar untuk ponsel */
            }

            .card-body {
                padding: 0.5rem;
                /* Atur padding untuk tampilan ponsel */
            }

            .card-title {
                font-size: 1.25rem;
                /* Ukuran font judul lebih kecil untuk ponsel */
            }

            .card-text {
                font-size: 0.9rem;
                /* Ukuran font teks lebih kecil untuk ponsel */
            }
        }
    </style>

    <?php include 'footer.php'; ?>
