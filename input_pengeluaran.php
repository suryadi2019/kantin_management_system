<?php
include 'navbar.php';
require_once 'db_connect.php';

// Proteksi Halaman: Pengguna harus login
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $keterangan = $_POST['keterangan'];
  $jumlah = $_POST['jumlah'];
  $tanggal = $_POST['tanggal'];
  $id_user = $_SESSION['user_id'];

  if (!empty($keterangan) && !empty($jumlah) && !empty($tanggal)) {
    $sql = "INSERT INTO expense (keterangan, jumlah, tanggal, id_user) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sdsi', $keterangan, $jumlah, $tanggal, $id_user);

    if (mysqli_stmt_execute($stmt)) {
      $pesan = "<p style='color:green; text-align:center;'>Data pengeluaran berhasil disimpan!</p>";
    } else {
      $pesan = "<p style='color:red; text-align:center;'>Gagal menyimpan data pengeluaran.</p>";
    }
  } else {
    $pesan = "<p style='color:red; text-align:center;'>Semua kolom wajib diisi!</p>";
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Input Pengeluaran Dana</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <main>
    <h2>💸 Input Pengeluaran Dana</h2>
    <?= $pesan ?>
    <form action="input_pengeluaran.php" method="POST">
      <div class="form-group">
        <label for="keterangan">Keterangan</label>
        <input type="text" id="keterangan" name="keterangan" required>
      </div>
      <div class="form-group">
        <label for="jumlah">Jumlah (Rp)</label>
        <input type="number" id="jumlah" name="jumlah" required>
      </div>
      <div class="form-group">
        <label for="tanggal">Tanggal</label>
        <input type="date" id="tanggal" name="tanggal" required>
      </div>
      <button type="submit" class="btn-pesan">Simpan</button>
    </form>
  </main>
</body>

</html>