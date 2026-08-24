<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>E-Perpustakaan SD SaMarFa</title>
  <!-- Font lebih ramah anak -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&family=Baloo+2:wght@600&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    body {
      background-color: #E3F2FD;
      font-family: 'Poppins', sans-serif;
      color: #333;
      padding-top: 80px;
    }

    /* Navbar anak-anak */
    .navbar {
      background: #0288D1;
      box-shadow: 0 2px 8px rgba(2,136,209,0.3);
    }

    .navbar-brand img {
      max-height: 50px;
      transition: transform 0.3s;
    }

    .navbar-brand img:hover {
      transform: scale(1.1) rotate(-5deg);
    }

    .nav-link {
      color: #ffffff !important;
      font-weight: 600;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      transition: all 0.2s ease-in-out;
    }

    .nav-link:hover {
      background-color: #81D4FA;
      color: #0288D1 !important;
      box-shadow: 0 0 8px #81D4FA;
    }

    .dropdown-menu {
      background-color: #E1F5FE;
      border: none;
      border-radius: 8px;
    }

    .dropdown-item {
      color: #0288D1;
      padding: 0.5rem 1rem;
      border-radius: 5px;
    }

    .dropdown-item:hover {
      background-color: #B3E5FC;
      color: #01579B;
    }

    .navbar-toggler {
      border-color: #ffffff;
    }

    .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%23fff' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M4 6h16M4 12h16M4 18h16'/%3E%3C/svg%3E");
    }

    /* Marquee anak-anak */
    .marquee {
      background: #B3E5FC;
      color: #01579B;
      padding: 10px;
      border-top: 3px solid #0288D1;
      border-bottom: 3px solid #0288D1;
      font-family: 'Baloo 2', cursive;
      font-size: 1.1rem;
      overflow: hidden;
      box-shadow: inset 0 0 10px #81D4FA;
    }

    .marquee p {
      white-space: nowrap;
      display: inline-block;
      animation: marquee 18s linear infinite;
      padding: 0 50px;
    }

    @keyframes marquee {
      0% { transform: translateX(100%); }
      100% { transform: translateX(-100%); }
    }

    @media (max-width: 768px) {
      .marquee p {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="home.php">
        <img src="assets/images/logos/logoNav.png" alt="Logo E-Perpustakaan" />
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link active" href="#" onclick="scrollToTop(event)">Home</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">Tentang Kami</a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
              <li><a class="dropdown-item" href="#visimisi" onclick="scrollToSection(event, 'visimisi')">Visi dan Misi</a></li>
              <li><a class="dropdown-item" href="#tatatertib" onclick="scrollToSection(event, 'tatatertib')">Tata Tertib</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link" href="#layanan" onclick="scrollTolayanan(event)">Layanan</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownKoleksi" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">Koleksi</a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdownKoleksi">
              <li><a class="dropdown-item" href="koleksi.php">Daftar Buku</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link" href="#takawan" onclick="scrollTotakawan(event)">Pustakawan</a></li>
          <li class="nav-item"><a class="nav-link" href="#about" onclick="scrollToAbout(event)">Kontak</a></li>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="marquee">
    <p>📚 Selamat Datang di E-Perpustakaan SD SaMarFa! Temukan buku-buku menarik untukmu dan jadilah pembaca hebat! 🌟. JADWAL OPERASIONAL : PADA HARI SEKOLAH (PUKUL 08.00-13.00 WIB). PERPUSTAKAAN TUTUP PADA HARI LIBUR DAN CUTI BERSAMA</p>
  </div>

  <script>
    // ... scrollToSection functions (tidak berubah) ...
    function scrollToSection(event, sectionId) {
      event.preventDefault();
      document.getElementById(sectionId).scrollIntoView({ behavior: "smooth" });
      closeNavbar();
    }
    function scrollToTop(event) {
      event.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
      closeNavbar();
    }
    function scrollToAbout(event) {
      event.preventDefault();
      document.querySelector('#about').scrollIntoView({ behavior: 'smooth' });
      closeNavbar();
    }
    function scrollTolayanan(event) {
      event.preventDefault();
      document.getElementById('layanan').scrollIntoView({ behavior: "smooth" });
      closeNavbar();
    }
    function scrollTotakawan(event) {
      event.preventDefault();
      document.getElementById('takawan').scrollIntoView({ behavior: "smooth" });
      closeNavbar();
    }
    function closeNavbar() {
      const navbarCollapse = document.getElementById('navbarNav');
      const navbarToggler = document.querySelector('.navbar-toggler');
      if (navbarCollapse.classList.contains('show')) {
        navbarCollapse.classList.remove('show');
        navbarToggler.setAttribute('aria-expanded', 'false');
      }
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
