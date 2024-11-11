<?php
include 'includes/auth.php';
include 'includes/db.php';

// Cek apakah user adalah admin
if (!isAdmin()) {
    header("Location: login.php");
    exit;
}

// Query untuk mendapatkan data produk
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fungsi untuk mencatat perubahan stok
function recordStockChange($pdo, $product_id, $change_type, $quantity) {
    $stmt = $pdo->prepare("INSERT INTO stock_reports (product_id, change_type, quantity) VALUES (:product_id, :change_type, :quantity)");
    $stmt->execute([
        'product_id' => $product_id,
        'change_type' => $change_type,
        'quantity' => $quantity
    ]);
}

// Proses penambahan produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $imageName = $_FILES['image']['name'];
    $imageTmpName = $_FILES['image']['tmp_name'];
    $uploadDir = 'uploads/';
    $uploadFilePath = $uploadDir . basename($imageName);

    // Periksa jika folder "uploads" ada
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($imageTmpName, $uploadFilePath)) {
        $stmt = $pdo->prepare("INSERT INTO products (name, price, quantity, image) VALUES (:name, :price, :quantity, :image)");
        $stmt->execute(['name' => $name, 'price' => $price, 'quantity' => $quantity, 'image' => $imageName]);
        
        // Dapatkan ID produk terakhir yang dimasukkan
        $product_id = $pdo->lastInsertId();
        
        // Catat perubahan stok sebagai "masuk"
        recordStockChange($pdo, $product_id, 'in', $quantity);

        header("Location: admin.php");
        exit;
    } else {
        echo "Gagal mengunggah gambar. Pastikan folder 'uploads' memiliki izin yang cukup.";
    }
}

// Proses penghapusan produk
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400&family=Lilita+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Inter', sans-serif;
        }
        .navbar {
            background-color: #31356E;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            color: #ffffff;
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
        .container {
            margin-top: 20px;
        }
        .table-wrapper {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .btn-primary, .btn-danger {
            border-radius: 20px;
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .preview-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="navbar-container">
        <button class="navbar-toggler" type="button" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        <span class="navbar-brand">NGASIR</span>
    </div>
</nav>

<div class="container">
    <!-- Tombol Tambah Barang -->
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">Tambah Barang</button>
    </div>

    <!-- Daftar Barang -->
    <div class="table-wrapper">
        <h3>Daftar Barang</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <?php if (!empty($product['image'])): ?>
                            <img src="uploads/<?= htmlspecialchars($product['image']) ?>" class="product-image" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <span>Tidak ada gambar</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td>Rp<?= number_format($product['price'], 0, ',', '.') ?></td>
                    <td><?= isset($product['quantity']) ? htmlspecialchars($product['quantity']) : 'N/A' ?></td>
                    <td>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProductModal" onclick="loadProduct(<?= htmlspecialchars(json_encode($product)) ?>)">Edit</button>
                        <a href="admin.php?delete=<?= $product['id'] ?>" onclick="return confirm('Yakin ingin menghapus barang ini?')" class="btn btn-danger btn-sm">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Barang -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="admin.php" method="POST" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Tambah Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Harga</label>
                    <input type="number" class="form-control" id="price" name="price" required>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Stok</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" required>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Gambar Produk</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                    <img id="preview" class="preview-img" alt="Preview Image">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" name="add_product" class="btn btn-primary">Tambah Barang</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Barang -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="edit_product.php" method="POST" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Edit Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit-id">
                <div class="mb-3">
                    <label for="edit-name" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" id="edit-name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="edit-price" class="form-label">Harga</label>
                    <input type="number" class="form-control" id="edit-price" name="price" required>
                </div>
                <div class="mb-3">
                    <label for="edit-quantity" class="form-label">Stok</label>
                    <input type="number" class="form-control" id="edit-quantity" name="quantity" required>
                </div>
                <div class="mb-3">
                    <label for="edit-image" class="form-label">Gambar Produk</label>
                    <input type="file" class="form-control" id="edit-image" name="image" accept="image/*" onchange="previewImage(event, 'edit-preview')">
                    <img id="edit-preview" class="preview-img" alt="Preview Image">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" name="edit_product" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function previewImage(event) {
        var preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(event.target.files[0]);
        preview.style.display = 'block';
    }

    function loadProduct(product) {
        document.getElementById('edit-id').value = product.id;
        document.getElementById('edit-name').value = product.name;
        document.getElementById('edit-price').value = product.price;
        document.getElementById('edit-quantity').value = product.quantity;
        var editPreview = document.getElementById('edit-preview');
        if (product.image) {
            editPreview.src = 'uploads/' + product.image;
            editPreview.style.display = 'block';
        } else {
            editPreview.style.display = 'none';
        }
    }
</script>
<script>
    function loadProduct(product) {
        // Set nilai untuk form edit produk di modal
        document.getElementById('edit-id').value = product.id;
        document.getElementById('edit-name').value = product.name;
        document.getElementById('edit-price').value = product.price;
        document.getElementById('edit-quantity').value = product.quantity;

        // Jika produk memiliki gambar, tampilkan preview gambar
        const previewImage = document.getElementById('edit-preview');
        if (product.image) {
            previewImage.src = 'uploads/' + product.image;
            previewImage.style.display = 'block';
        } else {
            previewImage.style.display = 'none';
        }
    }
</script>

</body>
</html>
