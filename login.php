<?php
// login.php
include 'includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (login($username, $password)) {
        if ($_SESSION['role'] === 'kasir') {
            header("Location: kasir.php");
        } elseif ($_SESSION['role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: index.php"); // Halaman default jika role tidak dikenali
        }
        exit;
    } else {
        $error = 'Username atau password salah';
    }    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ngasir</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400&family=Lilita+One&family=Poppins:wght@600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f5f5f7;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .login-container {
            display: flex;
            max-width: 700px;
            width: 100%;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .login-icon {
            background-color: #31356E;
            flex: 0.7; /* Mengurangi lebar bagian ikon */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px; /* Mengurangi padding di dalam kotak biru */
        }
        .login-icon img {
            width: 250px; /* Ukuran ikon tetap */
            height: auto;
        }
        .login-form {
            flex: 1.3; /* Menambah lebar bagian form */
            padding: 40px;
        }
        .login-title {
            font-family: 'Lilita One', cursive;
            font-size: 2.5em;
            color: #31356E;
            margin-bottom: 10px;
        }
        .login-subtitle {
            font-size: 1em;
            color: #7f8c8d;
            margin-bottom: 30px;
        }
        .form-label {
            font-weight: bold;
            color: #31356E;
        }
        .form-control {
            background-color: #f0f0f5;
            border: none;
            border-radius: 30px;
            padding: 10px 15px;
            font-size: 1em;
            color: #6c757d;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #31356E;
        }
        .btn-primary {
            background-color: #31356E;
            border: none;
            border-radius: 30px;
            padding: 12px 0;
            font-weight: bold;
            font-size: 1em;
            width: 100%;
            margin-top: 20px;
        }
        .btn-primary:hover {
            background-color: #272a5a;
        }
        /* Responsif untuk mobile */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                box-shadow: none;
            }
            .login-icon {
                padding: 20px;
            }
            .login-icon img {
                width: 150px;
                height: auto;
            }
            .login-form {
                padding: 20px;
            }
            .login-title {
                font-size: 2em;
            }
            .login-subtitle {
                font-size: 0.9em;
            }
            .form-control {
                font-size: 0.9em;
            }
            .btn-primary {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- Bagian Kiri: Icon -->
    <div class="login-icon">
        <img src="assets/images/vektor2.png" alt="Login Icon">
    </div>
    
    <!-- Bagian Kanan: Form Login -->
    <div class="login-form">
        <h2 class="login-title">Login</h2>
        <p class="login-subtitle">Login to continue using the app</p>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn btn-primary">LOGIN</button>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
