<?php
include 'includes/auth.php';
include 'includes/db.php';

// Cek apakah user adalah admin
if (!isAdmin()) {
    header("Location: login.php");
    exit;
}

// Query untuk mendapatkan data laporan stok
$stock_stmt = $pdo->query("SELECT * FROM stock_reports ORDER BY change_date DESC");
$stock_reports = $stock_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2>Laporan Stok</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Produk</th>
                    <th>Jenis Perubahan</th>
                    <th>Jumlah</th>
                    <th>Tanggal Perubahan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stock_reports as $report): ?>
                    <tr>
                        <td><?= htmlspecialchars($report['product_id']) ?></td>
                        <td><?= $report['change_type'] == 'in' ? 'Masuk' : 'Keluar' ?></td>
                        <td><?= htmlspecialchars($report['quantity']) ?></td>
                        <td><?= htmlspecialchars($report['change_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
