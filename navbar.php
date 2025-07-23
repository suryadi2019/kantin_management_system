<?php
// Selalu mulai session di file navigasi
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>
<link rel="stylesheet" href="style_navbar.css">
<nav class="navbar">
  <div class="nav-brand">
    <a href="index.php">P.O.S. (Point of Sale) System</a>
  </div>
  <div class="menu-toggle" onclick="toggleMenu()">
    <span></span>
    <span></span>
    <span></span>
  </div>
  <ul class="nav-links">
    <?php if (isset($_SESSION['user_id'])): ?>

      <?php if ($_SESSION['user_role'] == 'administrator'): ?>
        <li><a href="pesanan_pending.php">📋 Lihat Pesanan Terbaru</a></li>
        <li><a href="inventory.php">📦 Dapur Menu & Bahan Baku</a></li>
        <li><a href="laporan_keuangan.php">📊 Laporan Keuangan</a></li>
        <li><a href="daftar_piutang.php">💰 Daftar Piutang</a></li>
        <li><a href="register_pegawai.php">👤 Register Pegawai</a></li>
        <li><a href="daftar_pegawai.php">🔧 Modifikasi Pegawai</a></li>
        <li><a href="input_pengeluaran.php">💸 Input Pengeluaran</a></li>
        <li><a href="syslog.php">📜 System Log</a></li>
        <li><a href="konfigurasi.php">⚙️ Konfigurasi</a></li>

      <?php elseif ($_SESSION['user_role'] == 'supervisor'): ?>
        <li><a href="pesanan_pending.php">📋 Lihat Pesanan Terbaru</a></li>
        <li><a href="inventory.php">📦 Dapur Menu & Bahan Baku</a></li>
        <li><a href="laporan_keuangan.php">📊 Laporan Keuangan</a></li>
        <li><a href="daftar_piutang.php">💰 Daftar Piutang</a></li>
        <li><a href="register_pegawai.php">👤 Register Pegawai</a></li>
        <li><a href="daftar_pegawai.php">🔧 Modifikasi Pegawai</a></li>
        <li><a href="input_pengeluaran.php">💸 Input Pengeluaran</a></li>

      <?php elseif ($_SESSION['user_role'] == 'dapur'): ?>
        <li><a href="pesanan_pending.php">📋 Lihat Pesanan Terbaru</a></li>
        <li><a href="inventory.php">📦 Dapur Menu & Bahan Baku</a></li>
        <li><a href="input_pengeluaran.php">💸 Input Pengeluaran</a></li>

      <?php else: // Untuk role kasir 
      ?>
        <li><a href="pesanan_pending.php">📋 Lihat Pesanan Terbaru</a></li>
        <li><a href="input_pengeluaran.php">💸 Input Pengeluaran</a></li>
      <?php endif; ?>

      <li class="nav-user">
        Selamat datang, <strong><?= htmlspecialchars($_SESSION['user_name']); ?></strong>!
      </li>
      <li><a href="logout.php" class="btn-logout">🚪 Logout</a></li>

    <?php else: ?>
      <li><a href="index.php">🛒 Menu Makanan & Minuman</a></li>
      <li><a href="login.php" class="btn-login-nav">🔑 Login</a></li>
    <?php endif; ?>
  </ul>
</nav>
<script>
  function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('active');
  }
</script>