<?php
session_start();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SaMarFa Library</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/elib.png" />
  <link rel="stylesheet" href="assets/css/styles.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" crossorigin="anonymous" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.6)), url('assets/images/logos/samarfa.png') no-repeat center center fixed;
      background-size: cover;
      margin: 0;
      padding: 0;
    }

    .card-modern {
      backdrop-filter: blur(15px);
      background-color: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      color: #fff;
      transition: all 0.3s ease-in-out;
    }

    .card-modern input, .card-modern select {
      background-color: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #fff;
    }

    .card-modern input::placeholder {
      color: rgba(255, 255, 255, 0.6);
    }

    .card-modern label {
      font-weight: 500;
    }

    .btn-custom {
      background-color: #0d6efd;
      border: none;
      transition: all 0.3s ease-in-out;
      color: white;
    }

    .btn-custom:hover {
      background-color: #0a58ca;
      transform: scale(1.02);
    }

    .text-white-translucent {
      color: rgba(255, 255, 255, 0.85);
    }

    .logo-login {
      width: 80px; /* perbesar supaya lebih kelihatan */
      height: auto;
      margin-bottom: 10px;
      transition: transform 0.3s ease;
    }

    .logo-login:hover {
      transform: rotate(5deg) scale(1.05);
    }

    .alert {
      font-size: 0.9rem;
    }

    .input-group-text {
      background: transparent;
      border: none;
    }
  </style>
</head>

<body>
  <div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="col-md-6 col-lg-4">
      <div class="card card-modern text-center">
        <img src="assets/images/logos/logoNav.png" class="logo-login mx-auto d-block" alt="logo">
        <h4 class="fw-bold text-white-translucent mt-2">SaMarFa Library</h4>
        <p class="text-white-translucent mb-4">Silahkan Login Terlebih Dahulu</p>

        <?php
        if (isset($_SESSION['error'])) {
          echo '<div class="alert alert-danger text-center py-2">' . $_SESSION['error'] . '</div>';
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['warning'])) {
          echo '<div class="alert alert-warning text-center py-2">' . $_SESSION['warning'] . '</div>';
          unset($_SESSION['warning']);
        }
        if (isset($_SESSION['success'])) {
          echo '<div class="alert alert-success text-center py-2">' . $_SESSION['success'] . '</div>';
          unset($_SESSION['success']);
        }
        if (isset($_GET['register']) && isset($_SESSION['errorr'])) {
          echo '<div class="alert alert-danger text-center py-2">' . $_SESSION['errorr'] . '</div>';
          unset($_SESSION['errorr']);
        }
        ?>

        <form method="post" action="login_aksi.php">
          <div class="mb-3 text-start">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="user" required placeholder="Masukkan username">
          </div>

          <div class="mb-3 text-start">
            <label for="exampleInputPassword1">Password</label>
            <div class="input-group">
              <input type="password" class="form-control" id="exampleInputPassword1" name="pass" required placeholder="Masukkan password">
              <button type="button" class="btn btn-outline-light" id="show-password-button">
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="mb-3 text-start">
            <label for="level">Level Akses</label>
            <select class="form-control" id="level" name="level" required>
              <option value="">-- Pilih --</option>
              <option value="Admin">Admin</option>
              <option value="Siswa">Siswa</option>
            </select>
          </div>

          <button name="login" class="btn btn-custom w-100 mt-3">Login</button>
          <a href="home.php" class="btn btn-danger w-100 mt-3">Batal</a>
        </form>
      </div>
    </div>
  </div>

  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const passwordInput = document.getElementById('exampleInputPassword1');
    const showPasswordButton = document.getElementById('show-password-button');

    showPasswordButton.addEventListener('click', () => {
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        showPasswordButton.innerHTML = '<i class="fas fa-eye-slash"></i>';
      } else {
        passwordInput.type = 'password';
        showPasswordButton.innerHTML = '<i class="fas fa-eye"></i>';
      }
    });
  </script>
</body>
</html>
