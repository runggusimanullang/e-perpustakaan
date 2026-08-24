<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>E-Perpustakaan SD Santa Maria Fatima</title>
    <link rel="shortcut icon" href="assets/images/logos/logoNav.png" />

    <!-- CSS -->
    <link rel="stylesheet" href="../../assets/css/styles.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.0.0/remixicon.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #cfe9ff, #e0f7ff);
            /* warna biru lembut */
            background-attachment: fixed;
            font-family: Arial, sans-serif;
        }

        h2 {
            color: #0d6efd;
        }

        h4,
        h5 {
            color: #0056b3;
        }

        p,
        li {
            color: #333;
        }

        .content-margin {
            margin-top: 100px;
        }

        #visimisi,
        #layanan,
        #takawan,
        #tatatertib,
        #takawan {
            scroll-margin-top: 100px;
        }

        .slideshow-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 20px;
        }

        .image-box {
            flex: 1;
            max-height: 400px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .mySlides {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .description {
            flex: 1;
            padding: 20px;
            background-color: rgba(173, 216, 230, 0.3);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .fade {
            animation: fade 1.5s;
        }

        @keyframes fade {
            from {
                opacity: 0.4
            }

            to {
                opacity: 1
            }
        }

        @media (max-width: 500px) {

            .image-box,
            .description {
                width: 100%;
            }

            .image-box {
                height: 250px;
            }

            .description {
                padding: 10px;
                font-size: 14px;
            }
        }

        .img-cover {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 12px;
        }

        @media (max-width: 768px) {
            .img-cover {
                height: 250px;
            }
        }

        /* Card Style */
        .card {
            background: #ffffff;
            border: 2px solid #bee3ff;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        /* Service Boxes */
        .service-box {
            background: #e8f7ff;
            padding: 24px;
            border-radius: 12px;
            border: 2px solid #bee3ff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.2s ease;
        }

        .service-box:hover {
            background: #bee3ff;
            transform: scale(1.03);
        }

        .service-box .icon {
            font-size: 42px;
            color: #0d6efd;
        }


        /* Info Box Footer */
        #about {
            background: linear-gradient(135deg, #0d6efd, #0056b3);
            color: #fff;
        }

        .info-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px);
        }

        .info-box h4 {
            color: #ffeb3b;
        }

        .social-icons a {
            color: white;
            margin: 0 8px;
        }

        .social-icons a:hover {
            transform: scale(1.2);
            color: #ffeb3b;
        }

        .hover-card {
            background-color: #f8f9fa;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-card:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.3);
        }
    </style>
</head>

<body>
    <?php include 'navbars.php'; ?>

    <!-- Slideshow -->
    <div class="container py-5 content-margin">
        <div class="row align-items-center g-4">

            <!-- Slideshow -->
            <div class="col-md-6">
                <div id="homeCarousel" class="carousel slide shadow-lg rounded-4 overflow-hidden" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="assets/images/potohom/pe1.jpeg" class="d-block w-100 img-cover" alt="Gambar 1">
                        </div>
                        <div class="carousel-item">
                            <img src="assets/images/potohom/pe2.jpeg" class="d-block w-100 img-cover" alt="Gambar 2">
                        </div>
                        <div class="carousel-item">
                            <img src="assets/images/potohom/pe4.jpeg" class="d-block w-100 img-cover" alt="Gambar 3">
                        </div>
                    </div>
                    <!-- Controls -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>

            <!-- Description -->
            <div class="col-md-6">
                <div class="bg-light p-4 rounded-4 shadow-sm">
                    <h2 class="text-primary mb-3 fw-bold">Perpustakaan Online Sekolah</h2>
                    <p class="text-muted mb-2">
                        Website perpustakaan online di sekolah adalah platform digital untuk menelusuri koleksi perpustakaan secara daring.
                    </p>
                    <p class="text-muted mb-2">
                        Dengan adanya katalog online, pengguna bisa mencari buku berdasarkan judul, pengarang, atau kategori, memudahkan mereka dalam menemukan materi yang dibutuhkan.
                    </p>
                    <p class="text-muted mb-0">
                        Fungsinya untuk mempermudah akses informasi, mendukung literasi, dan mempromosikan budaya membaca di lingkungan sekolah.
                    </p>
                </div>
            </div>

        </div>
    </div>

    <!-- Slideshow JS -->
    <script>
        let slideIndex = 0;
        showSlides();

        function showSlides() {
            let slides = document.getElementsByClassName("mySlides");
            for (let i = 0; i < slides.length; i++) slides[i].style.display = "none";
            slideIndex++;
            if (slideIndex > slides.length) slideIndex = 1;
            slides[slideIndex - 1].style.display = "block";
            setTimeout(showSlides, 3000);
        }
    </script>
    <?php
    $visi = "Berperan sebagai pusat sumber informasi untuk mewujudkan dan menciptakan pribadi terampil dan berprestasi, serta menjadikan perpustakaan sebagai tempat sumber belajar.";
    $misi = [
        "Mengembangkan minat, kemampuan, dan kebiasaan membaca.",
        "Mengembangkan kemampuan mencari dan mengolah serta memanfaatkan informasi.",
        "Mendidik siswa agar memelihara dan memanfaatkan bahan pustaka secara tepat.",
        "Mengembangkan kemampuan untuk memecahkan masalah yang di hadapi atas tanggung jawab dan usaha sendiri."
    ];

    $tata_tertib = [
        "Pengunjung wajib mengisi buku tamu setiap kali berkunjung ke perpustakaan.",
        "Pengunjung harus menjaga ketenangan dan ketertiban selama berada di dalam ruangan.",
        "Dilarang makan dan minum di dalam perpustakaan.",
        "Pengunjung wajib mengembalikan buku sesuai waktu yang telah ditentukan.",
        "Pengunjung harus menjaga kebersihan dan kerapihan ruangan dan buku.",
        "Dilarang mencorat-coret, merusak, atau menghilangkan buku perpustakaan.",
        "Pengunjung wajib mematuhi arahan dan instruksi petugas perpustakaan."
    ];

    $pustakawan = [
        [
            "nama" => "FRANSISKA ERMINA WELU",
            "foto" => "#"
        ],
    ];
    ?>

    <!-- Visi Misi -->
    <div id="visimisi" class="container py-5">
        <h2 class="text-center mb-5 fw-bold">Visi & Misi</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100 p-4 text-center">
                    <div class="icon mb-3">🎯</div>
                    <h4 class="fw-semibold mb-3">Visi</h4>
                    <p><?= $visi ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 p-4">
                    <div class="icon mb-3 text-center">📜</div>
                    <h4 class="fw-semibold mb-3 text-center">Misi</h4>
                    <ul class="list-unstyled ps-0">
                        <?php foreach ($misi as $m): ?><li>✅ <?= $m ?></li><?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Tata Tertib -->
    <div id="tatatertib" class="container py-5">
        <h2 class="text-center mb-4 fw-bold">Tata Tertib Perpustakaan</h2>
        <div class="card p-4">
            <ul class="list-unstyled"><?php foreach ($tata_tertib as $tertib): ?><li>📖 <?= $tertib ?></li><?php endforeach; ?></ul>
        </div>
    </div>

    <!-- Layanan -->
    <div id="layanan">
        <div class="text-center visi-misi-container" style="width: 80%; max-width: 600px; margin: 50px auto;">
            <h2 style="margin-top: 10px;">Layanan</h2>
        </div>
        <div class="container mt-2" style="display: flex; justify-content: space-between; flex-wrap: wrap; width: 80%; max-width: 1200px; margin: auto;">
            <div class="col-lg-6 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="400" style="padding: 10px;">
                <div class="service-box blue" style="width: 100%; min-height: 300px; display: flex; flex-direction: column; justify-content: space-between;">
                    <i class="ri-book-open-line icon"></i>
                    <h2>Perpustakaan Digital</h2>
                    <p>Masuk ke laman Perpustakaan Digital SD Santa Maria Fatima untuk melakukan pencarian koleksi - koleksi buku perpustakaan</p>
                    <a href="koleksi.php" class="read-more"><span>👉Selengkapnya👈</span> <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="400" style="padding: 10px;">
                <div class="service-box blue" style="width: 100%; min-height: 300px; display: flex; flex-direction: column; justify-content: space-between;">
                    <i class="ri-discuss-line icon"></i>
                    <h2>Kotak Saran</h2>
                    <p>Kami dengan senang hati menerima segala bentuk masukan dan kritik yang konstruktif untuk pengembangan Perpustakaan SD Santa Maria Fatima agar dapat menjadi lebih baik lagi</p>
                    <a href="kotak-saran.php" class="read-more"><span>👉Berikan Masukan👈</span> <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Pustakawan -->
    <div id="takawan" class="container py-5">
        <h2 class="text-center mb-5 fw-bold text-dark">Pustakawan Kami</h2>
        <div class="row g-4 justify-content-center">
            <?php foreach ($pustakawan as $p): ?>
                <div class="col-md-3 col-sm-6 d-flex align-items-stretch">
                    <div class="card p-4 h-100 text-center shadow-lg border-0 rounded-4 d-flex flex-column align-items-center justify-content-center hover-card transition">
                        <img
                            src="<?= $p['foto'] ?>"
                            alt="<?= $p['nama'] ?>"
                            class="rounded-circle mb-3 border border-3 border-primary"
                            style="width: 150px; height: 150px; object-fit: cover;" />
                        <h5 class="card-title mt-2 fw-bold text-primary"><?= $p['nama'] ?></h5>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

  <?php include 'footer.php'; ?>
  
