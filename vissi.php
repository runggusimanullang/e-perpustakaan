<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Visi Perpustakaan</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Orbitron', sans-serif;
      background: radial-gradient(circle at top left, #0f0c29, #302b63, #24243e);
      color: #00ffff;
      padding: 20px;
    }

    .container {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: center;
      gap: 40px;
    }

    .image {
      flex: 1 1 45%;
      text-align: center;
    }

    .image img {
      width: 100%;
      max-width: 600px;
      border: 3px solid #00f0ff;
      box-shadow: 0 0 20px #0ff, 0 0 40px #00f0ff;
      border-radius: 10px;
    }

    .visi-box {
      flex: 1 1 45%;
      background: rgba(0, 0, 0, 0.6);
      padding: 30px;
      border-radius: 15px;
      border: 2px solid #ff00ff;
      box-shadow: 0 0 15px #ff00ff;
    }

    .visi-title {
      font-size: 36px;
      color: #ff00ff;
      margin-bottom: 20px;
      border-bottom: 2px solid #ff00ff;
      display: inline-block;
    }

    .visi-text {
      font-size: 18px;
      line-height: 1.8;
      color: #ffffff;
      text-shadow: 0 0 10px #00ffff;
    }

    @media screen and (max-width: 768px) {
      .container {
        flex-direction: column;
      }

      .visi-title {
        font-size: 28px;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="image">
      <img src="vissi.png" alt="Foto Sekolah Santa Maria Fatima">
    </div>
    <div class="visi-box">
      <div class="visi-title">Visi</div>
      <p class="visi-text">
        "Berperan sebagai pusat sumber informasi untuk mewujudkan dan menciptakan pribadi terampil dan berprestasi,
        serta menjadikan perpustakaan sebagai tempat sumber belajar."
      </p>
    </div>
  </div>

</body>
</html>
