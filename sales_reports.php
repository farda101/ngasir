<?php
include 'includes/auth.php';
include 'includes/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Mengambil semua riwayat transaksi dari tabel `sales_reports`
$stmt = $pdo->query("SELECT * FROM sales_reports ORDER BY id DESC");
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Menghitung total penjualan
$totalSalesStmt = $pdo->query("SELECT SUM(sub_total) AS total_sales FROM sales_reports");
$totalSales = $totalSalesStmt->fetch(PDO::FETCH_ASSOC)['total_sales'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Ngasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .report-wrapper {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin: 20px auto;
            max-width: 800px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        h4 {
            color: #31356E;
        }

        .total-sales {
            font-weight: bold;
            font-size: 1.2em;
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="report-wrapper">
    <h4 class="text-center">Laporan Penjualan</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Sub Total</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?= htmlspecialchars($sale['id']) ?></td>
                    <td><?= htmlspecialchars($sale['product_name']) ?></td>
                    <td><?= $sale['quantity'] ?></td>
                    <td>Rp<?= number_format($sale['price'], 0, ',', '.') ?></td>
                    <td>Rp<?= number_format($sale['sub_total'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($sale['created_at']) ?></td> <!-- Pastikan kolom created_at ada di tabel sales_reports -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Menampilkan Total Penjualan -->
    <div class="total-sales">
        Total Penjualan: Rp<?= number_format($totalSales, 0, ',', '.') ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
