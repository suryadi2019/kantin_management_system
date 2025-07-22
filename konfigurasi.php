<?php
include 'navbar.php';

// Proteksi Halaman: Hanya untuk administrator
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'administrator') {
  // Jika bukan admin, tampilkan pesan akses ditolak dan hentikan skrip
  die("<div style='text-align:center; margin-top:50px;'><h1>Akses Ditolak!</h1><p>Halaman ini hanya untuk Administrator.</p><a href='index.php'>Kembali</a></div>");
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Konfigurasi Sistem</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .danger-zone {
      border: 2px solid #d9534f;
      padding: 20px;
      margin-top: 40px;
      border-radius: 8px;
    }

    .danger-zone h3 {
      color: #d9534f;
      margin-top: 0;
    }

    .btn-reset {
      background-color: #d9534f;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 1em;
    }

    .btn-reset:hover {
      background-color: #c9302c;
    }
  </style>
</head>

<body>
  <main>
    <h2>⚙️ Konfigurasi Sistem</h2>
    <p>Pengaturan dan alat bantu untuk administrator.</p>

    <div class="danger-zone">
      <h3>🔴 Zona Berbahaya</h3>
      <p>Tindakan di bawah ini tidak dapat diurungkan. Pastikan Anda tahu apa yang Anda lakukan.</p>
      <form action="proses_reset.php" method="POST" onsubmit="return confirm('APAKAH ANDA YAKIN? Semua data penjualan, piutang, dan pengeluaran akan DIHAPUS PERMANEN. Tindakan ini tidak bisa dibatalkan!');">
        <button type="submit" class="btn-reset">Reset Semua Statistik Keuangan</button>
      </form>
    </div>
  </main>
</body>

</html>