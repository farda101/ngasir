<?php
include 'includes/auth.php';
include 'includes/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah total checkout dan detail keranjang tersedia di sesi
if (!isset($_SESSION['cart_total']) || empty($_SESSION['cart'])) {
    // Jika tidak ada data checkout, kembali ke kasir
    header("Location: kasir.php");
    exit;
}

// Total keseluruhan dari transaksi yang baru saja dilakukan
$total_checkout = $_SESSION['cart_total'];

// Ambil daftar barang yang telah di-checkout dari keranjang di sesi
$items = $_SESSION['cart'];

// Setelah ditampilkan di nota, kosongkan data keranjang dan total dari sesi
unset($_SESSION['cart'], $_SESSION['cart_total']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pesanan - Ngasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .nota-wrapper {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin: 20px auto;
            max-width: 500px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        h4, .total {
            color: #31356E;
        }

        .total {
            font-weight: bold;
            font-size: 1.2em;
        }
    </style>
</head>
<body>

<div class="nota-wrapper">
    <h4 class="text-center">Nota Pesanan</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Sub Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                    <td>Rp<?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p class="total text-center">Total: Rp<?= number_format($total_checkout, 0, ',', '.') ?></p>

    <!-- Tombol Unduh Nota dan Kembali ke Kasir -->
    <div class="d-flex justify-content-between mt-4">
        <button onclick="window.print()" class="btn btn-primary w-100 me-2">Cetak Nota</button>
        <a href="kasir.php" class="btn btn-secondary w-100">Pesanan Baru</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
