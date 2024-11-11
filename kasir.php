<?php
include 'includes/auth.php';
include 'includes/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kosongkan keranjang setiap kali halaman diakses
$_SESSION['cart'] = [];

// Fungsi untuk mengambil daftar produk yang tersedia di database
function getAvailableProducts() {
    global $pdo;
    // Hanya ambil produk yang memiliki stok lebih dari 0
    $stmt = $pdo->query("SELECT * FROM products WHERE quantity > 0");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi untuk menambahkan item ke keranjang
function addToCart($product) {
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] === $product['id']) {
            $item['quantity'] += $product['quantity'];
            return;
        }
    }
    $_SESSION['cart'][] = $product;
}

// Fungsi untuk menghitung total pesanan
function calculateTotal() {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Proses checkout untuk menyimpan data ke `sales_reports` dan mengurangi stok
if (isset($_POST['checkout'])) {
    $total = calculateTotal();

    // Simpan setiap item di keranjang ke tabel sales_reports
    foreach ($_SESSION['cart'] as $item) {
        $stmt = $pdo->prepare("INSERT INTO sales_reports (product_id, product_name, quantity, price, sub_total, total_sales) VALUES (:product_id, :product_name, :quantity, :price, :sub_total, :total_sales)");
        $stmt->execute([
            'product_id' => $item['id'],
            'product_name' => $item['name'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'sub_total' => $item['price'] * $item['quantity'],
            'total_sales' => $total
        ]);

        // Kurangi stok barang
        $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - :quantity WHERE id = :id");
        $stmt->execute(['quantity' => $item['quantity'], 'id' => $item['id']]);
    }

    // Simpan total ke sesi untuk digunakan di nota.php
    $_SESSION['cart_total'] = $total;

    // Hapus keranjang setelah checkout
    $_SESSION['cart'] = [];

    // Redirect ke nota.php untuk menampilkan nota
    header("Location: nota.php");
    exit;
}

// Mengelola aksi tombol tambah, kurang, dan hapus item di keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_order'])) {
        $product = [
            'id' => $_POST['product_id'],
            'name' => $_POST['product_name'],
            'price' => $_POST['product_price'],
            'quantity' => (int)$_POST['quantity']
        ];
        addToCart($product);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Ngasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400&family=Lilita+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #e0e0e6;
            font-family: 'Inter', sans-serif;
        }

        .navbar {
            background-color: #31356E;
            padding: 10px;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .navbar-toggler {
            background: none;
            border: none;
            color: #FFFFFF;
            font-size: 1.5em;
            margin-right: 10px;
        }

        .navbar-brand {
            font-family: 'Lilita One', cursive;
            font-size: 1.5em;
            color: #FFFFFF;
            margin: 0;
        }

        .order-summary {
            background-color: #c7c9d3;
            border-radius: 8px;
            padding: 15px;
            color: #31356E;
            text-align: center;
            margin-bottom: 15px;
        }

        .product-list {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 15px;
            max-height: 50vh;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .product-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .btn-primary {
            background-color: #31356E;
            border: none;
            border-radius: 30px;
            padding: 8px 16px;
        }

        .total-amount {
            font-size: 1.2em;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <button class="navbar-toggler" type="button" aria-label="Toggle navigation">
        <i class="fas fa-bars"></i>
    </button>
    <a class="navbar-brand" href="#">NGASIR</a>
</nav>

<div class="container my-4">
    <!-- Order Summary -->
    <div class="order-summary">
        <h4>New Order</h4>
        <table class="table table-borderless">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Sub Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($_SESSION['cart'])): ?>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                            <td>Rp<?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Belum ada pesanan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="total-amount">Total: Rp<?= number_format(calculateTotal(), 0, ',', '.') ?></div>
        <form action="kasir.php" method="post" class="mt-3">
            <button type="submit" name="checkout" class="btn btn-primary">Checkout</button>
        </form>
    </div>

    <!-- Daftar Produk Tersedia -->
    <div class="product-list">
        <h5>Produk Tersedia</h5>
        <?php foreach (getAvailableProducts() as $product): ?>
            <form action="kasir.php" method="post" class="product-item">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                <input type="hidden" name="product_price" value="<?= $product['price'] ?>">
                <div>
                    <strong><?= htmlspecialchars($product['name']) ?></strong> - Rp<?= number_format($product['price'], 0, ',', '.') ?>
                    <br>Stok: <?= $product['quantity'] ?>
                </div>
                <div>
                    <input type="number" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>" required>
                    <button type="submit" name="add_to_order" class="btn btn-primary btn-sm mt-2">Tambah ke Keranjang</button>
                </div>
            </form>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
