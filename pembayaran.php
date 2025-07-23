<?php
session_start();

// --- Proteksi Halaman: Pengguna harus login ---
if (!isset($_SESSION['user_id'])) {
  // Hentikan eksekusi jika tidak ada sesi login pegawai
  die("Akses ditolak. Anda harus login sebagai pegawai untuk memproses transaksi.");
}

?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Konfirmasi Pembayaran</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>💳 Konfirmasi Pesanan & Pembayaran</h1>
  </header>
  <main>
    <p>Pesanan Anda akan segera diproses. Silakan pilih metode pembayaran.</p>
    <form action="proses_transaksi.php" method="POST" style="margin-top:20px; padding:20px; border:1px solid #ddd; border-radius:8px;">
      <div class="form-group">
        <label for="metode_pembayaran">Pilih Metode Pembayaran:</label>
        <select name="metode_pembayaran" id="metode_pembayaran" required style="width:100%; padding:8px; font-size:1em;">
          <option value="Tunai">Tunai</option>
          <option value="Hutang">Hutang</option>
          <option value="QRIS">QRIS</option>
        </select>
      </div>
      <div style="text-align: right; margin-top: 20px;">
        <a href="keranjang.php" class="btn-pesan" style="background-color: #6c757d;">Kembali ke Keranjang</a>
        <button type="submit" class="btn-pesan">Buat Pesanan Sekarang</button>
      </div>
    </form>
  </main>
</body>

</html>