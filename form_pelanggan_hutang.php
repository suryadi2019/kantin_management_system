<?php
include 'navbar.php';

// Proteksi Halaman: Pengguna harus login
if (!isset($_SESSION['user_id'])) {
  die("Akses ditolak. Anda harus login sebagai pegawai untuk memproses transaksi.");
}

// Proteksi Halaman: Pastikan keranjang tidak kosong
if (empty($_SESSION['keranjang'])) {
  echo "<script>alert('Keranjang kosong, tidak bisa melanjutkan transaksi hutang.'); window.location.href='index.php';</script>";
  exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Identitas Pelanggan Hutang</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>📝 Form Identitas Pelanggan (Hutang)</h1>
  </header>
  <main>
    <p>Silakan isi data pelanggan yang melakukan transaksi hutang.</p>
    <form action="proses_hutang.php" method="POST" style="margin-top:20px; padding:20px; border:1px solid #ddd; border-radius:8px;">
      <div class="form-group">
        <label for="nama_pelanggan">Nama Lengkap:</label>
        <input type="text" id="nama_pelanggan" name="nama_pelanggan" required style="width:100%; padding:8px;">
      </div>
      <div class="form-group">
        <label for="kontak">Nomor Telepon:</label>
        <input type="text" id="kontak" name="kontak" required style="width:100%; padding:8px;">
      </div>
      <div class="form-group">
        <label>Tanggal & Waktu:</label>
        <input type="text" value="<?= date('d M Y, H:i:s'); ?>" disabled style="width:100%; padding:8px; background-color:#e9ecef;">
      </div>
      <div style="text-align: right; margin-top: 20px;">
        <a href="pembayaran.php" class="btn-pesan" style="background-color: #6c757d;">Batal</a>
        <button type="submit" class="btn-pesan">Simpan Transaksi Hutang</button>
      </div>
    </form>
  </main>
</body>

</html>