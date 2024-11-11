<?php
include 'includes/auth.php';
include 'includes/db.php';

// Cek apakah user adalah admin
if (!isAdmin()) {
    header("Location: login.php");
    exit;
}

// Periksa apakah data form dikirimkan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    
    // Proses upload gambar jika ada
    $imageName = $_POST['current_image'];
    if (!empty($_FILES['image']['name'])) {
        $imageName = $_FILES['image']['name'];
        $imageTmpName = $_FILES['image']['tmp_name'];
        $uploadDir = 'uploads/';
        $uploadFilePath = $uploadDir . basename($imageName);
        
        if (move_uploaded_file($imageTmpName, $uploadFilePath)) {
            $stmt = $pdo->prepare("UPDATE products SET image = :image WHERE id = :id");
            $stmt->execute(['image' => $imageName, 'id' => $id]);
        }
    }

    // Update produk
    $stmt = $pdo->prepare("UPDATE products SET name = :name, price = :price, quantity = :quantity WHERE id = :id");
    $stmt->execute(['name' => $name, 'price' => $price, 'quantity' => $quantity, 'id' => $id]);

    header("Location: admin.php");
    exit;
} else {
    header("Location: admin.php");
    exit;
}
