<?php
// index.php
include 'includes/auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Ngasir</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Selamat datang, <?= $_SESSION['username'] ?>!</h1>
        <p>Anda login sebagai <?= $_SESSION['role'] ?>.</p>

        <?php if (isAdmin()): ?>
            <a href="admin.php">Kelola Produk</a>
        <?php else: ?>
            <a href="kasir.php">Mulai Transaksi</a>
        <?php endif; ?>

        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
