<?php
// Panggil komponen utama
include 'navbar.php';
require_once 'db_connect.php';

// Proteksi Halaman: Pengguna harus login
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

// Validasi jika ID transaksi ada di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header('Location: index.php'); // Arahkan ke index jika tidak ada ID
  exit();
}

$id_transaksi = $_GET['id'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Pembayaran QRIS</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .qris-container {
      text-align: center;
      padding: 20px;
    }

    .qris-container img {
      max-width: 400px;
      /* Ukuran bisa disesuaikan */
      height: auto;
      border: 1px solid #ddd;
      padding: 10px;
      border-radius: 8px;
      background-color: white;
      margin-bottom: 25px;
    }

    .btn-lanjut {
      background-color: #28a745;
      /* Warna hijau */
      font-size: 1.2em;
      padding: 12px 25px;
    }
  </style>
</head>

<body>
  <header>
    <h1>📱 Pembayaran via QRIS</h1>
    <p>Silakan scan kode QR di bawah ini menggunakan aplikasi pembayaran Anda.</p>
  </header>
  <main>
    <div class="qris-container">
      <img src="placeholder_qris.jpeg" alt="Kode QRIS untuk Pembayaran">
      <br>
      <a href="bukti.php?id=<?= htmlspecialchars($id_transaksi); ?>" class="btn-pesan btn-lanjut">
        Selesai Bayar & Lihat Struk
      </a>
    </div>
  </main>
</body>

</html>