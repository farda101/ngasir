<?php
// Memanggil koneksi database
include 'includes/db.php';

// Data pengguna yang ingin ditambahkan
$username = 'admin1';  // Ganti dengan username yang diinginkan
$password = password_hash('admin1', PASSWORD_DEFAULT); // Hash password baru
$role = 'admin';           // Role, misalnya 'kasir' atau 'admin'

// Query untuk menambahkan pengguna ke database
$stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");

// Eksekusi query dengan data yang sesuai
$stmt->execute(['username' => $username, 'password' => $password, 'role' => $role]);

echo "Pengguna berhasil ditambahkan!";
