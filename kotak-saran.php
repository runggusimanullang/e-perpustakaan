<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kotak Saran</title>
    <link rel="shortcut icon" type="image/png" href="assets/images/logos/logoNav.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
       <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }

        h1 {
            text-align: center;
            margin-top: 30px;
            font-size: 2rem;
            color: #333;
        }

        /* Navbar Styles */
        .navbars {
            background-color: #222;
            display: flex;
            justify-content: center;
            padding: 0.5rem 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbars a {
            color: #f8f8f8;
            padding: 14px 20px;
            text-decoration: none;
            transition: 0.3s ease;
            font-weight: 500;
            position: relative;
        }

        .navbars a:hover {
            background-color: #e0e0e0;
            color: #111;
        }

        .navbars a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 8px;
            width: 100%;
            height: 2px;
            background-color: #00bcd4;
            transform: scaleX(0);
            transform-origin: center;
            transition: transform 0.3s ease;
        }

        .navbars a:hover::after {
            transform: scaleX(1);
        }

        /* Form container */
        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }

        /* Responsive iframe */
        iframe {
            width: 100%;
            max-width: 700px;
            height: 1500px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        @media (max-width: 768px) {
            iframe {
                height: 1200px;
            }
        }

        @media (max-width: 480px) {
            .navbars {
                flex-direction: column;
                align-items: center;
            }

            .navbars a {
                padding: 10px 15px;
            }

            iframe {
                height: 1000px;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbars">
        <a href="home.php">Beranda</a>
        <a href="#about">Kontak</a>
    </div>

    <h1>Kotak Saran</h1>
    <div class="form-container">
        <iframe src="https://docs.google.com/forms/d/e/1FAIpQLSf7Ej1A3o2ROjQR8dswg6LtywUlg_VkGzmR8QI9gn48DpLEsw/viewform?usp=sf_link" 
                frameborder="0" 
                marginheight="0" 
                marginwidth="0">Loading…</iframe>
    </div>


    
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div id="about" class="p-5">
  <div class="container">
    <div class="row text-center text-md-start">
      <div class="col-md-6 mb-4">
        <div class="info-box">
          <h4><i class="fas fa-map-marker-alt me-2 text-warning"></i>Alamat</h4>
          <p>Jalan Jatinegara Barat No.122 Bidara Cina, Jatinegara, RT.7/RW.1, Kp. Melayu, Kota Jakarta Timur, DKI Jakarta 13320</p>
        </div>
      </div>
      <div class="col-md-6 mb-4">
        <div class="info-box">
          <h4><i class="fas fa-phone me-2 text-warning"></i>Telepon</h4>
          <p>(021) 85902383</p>
        </div>
      </div>
    </div>
    <hr class="border-light">
    <div class="d-flex justify-content-center gap-4 mt-4 social-icons">
      <a href="#" class="text-light fs-3"><i class="fab fa-telegram"></i></a>
      <a href="#" class="text-light fs-3"><i class="fab fa-instagram"></i></a>
      <a href="#" class="text-light fs-3"><i class="fab fa-facebook"></i></a>
    </div>
    <div class="text-center mt-4 text-light">
      <small>mahasiswa-pkl-ubsi: 
        <a href="https://www.instagram.com/della_novtrisia?igsh=YXlqMjNoYTU4ZTd5&utm_source=qr" class="text-warning text-decoration-none">
          runggu, jualdi, della
        </a> 2024
      </small>
    </div>
  </div>
</div>

<style>
  #about {
    background: linear-gradient(135deg, #1d4fcf, #0f172a);
    color: #fff;
    position: relative;
    width: 100%;
    z-index: 1;
  }

  .info-box {
    background-color: rgba(255, 255, 255, 0.05);
    padding: 20px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(5px);
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
    transition: all 0.3s ease;
  }

  .info-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(255, 215, 0, 0.5);
  }

  .social-icons a {
    transition: transform 0.3s ease;
  }

  .social-icons a:hover {
    transform: scale(1.2);
    color: gold;
  }

  a.text-warning:hover {
    text-decoration: underline;
  }

    @media (max-width: 576px) {
        .info-box {
            padding: 10px; /* Mengurangi padding pada layar kecil */
        }
        .fs-5 {
            font-size: 1.25rem; /* Ukuran font lebih kecil untuk judul */
        }
        .fs-6 {
            font-size: 1rem; /* Ukuran font lebih kecil untuk isi */
        }
    }
</style>
</body>
</html>