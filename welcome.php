<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Ngasir</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Inter:wght@400&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f5f5f7;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 40px 0;
        }
        .container {
            max-width: 600px;
            text-align: left;
            padding: 0 20px;
        }
        .icon {
            display: block;
            margin: 0 auto 20px auto;
            width: 300px;
            height: auto;
        }
        .welcome-logo {
            font-family: 'Lilita One', cursive;
            font-size: 2.5em;
            color: #31356E;
            margin-bottom: 10px;
        }
        .description-title {
            font-family: 'Lilita One', cursive;
            font-weight: bold;
            color: #31356E;
            font-size: 1.5em;
            margin-top: 10px;
        }
        .description-text {
            font-family: 'Inter', sans-serif;
            font-size: 1em;
            color: #7f8c8d;
            margin: 20px 0 30px 0;
        }
        .btn-primary {
            background-color: #31356E;
            border: none;
            border-radius: 30px;
            padding: 14px 0;
            font-weight: bold;
            width: 100%;
            font-size: 1.1em;
        }
        .btn-primary:hover {
            background-color: #272a5a;
        }
        /* Responsif untuk mobile */
        @media (max-width: 576px) {
            .container {
                max-width: 400px;
                padding: 0 20px;
            }
            .icon {
                width: 300px;
            }
            .welcome-logo {
                font-size: 2em;
            }
            .description-title {
                font-size: 1.2em;
            }
            .description-text {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Icon atau Logo -->
    <img src="assets/images/vektor1.png" alt="Ngasir Logo" class="icon">
    <!-- Teks Judul -->
    <h1 class="welcome-logo">NGASIR.</h1>
    <p class="description-title">Kasir cerdas gak pake ribet, cuan makin cepet!</p>
    <!-- Deskripsi -->
    <p class="description-text">
        Semua transaksi, stok, dan laporan keuangan jadi lebih simpel dan cepat. Bisnis jadi makin lancar, cuan makin ngebut! 
        Pokoknya, semua urusan kasir jadi gampang tanpa harus pusing.
    </p>
    <!-- Tombol Lanjutkan ke Halaman Login -->
    <a href="login.php" class="btn btn-primary">CONTINUE</a>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
