<?php
include 'navbar.php';
require_once 'db_connect.php';

// Proteksi halaman
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

if (!isset($_GET['id'])) {
  header('Location: pesanan_pending.php');
  exit();
}

$id_transaksi = $_GET['id'];

// Ambil data transaksi utama
$sql_trans = "SELECT t.*, u.nama_user as kasir FROM transaction t JOIN user u ON t.id_user = u.id_user WHERE t.id_transaction = ?";
$stmt_trans = mysqli_prepare($conn, $sql_trans);
mysqli_stmt_bind_param($stmt_trans, 'i', $id_transaksi);
mysqli_stmt_execute($stmt_trans);
$result_trans = mysqli_stmt_get_result($stmt_trans);
$transaksi = mysqli_fetch_assoc($result_trans);

if (!$transaksi) {
  echo "Transaksi tidak ditemukan.";
  exit();
}

// Ambil data detail item pesanan
$sql_detail = "SELECT td.*, m.nama_menu FROM transaction_detail td JOIN menu m ON td.id_menu = m.id_menu WHERE td.id_transaction = ?";
$stmt_detail = mysqli_prepare($conn, $sql_detail);
mysqli_stmt_bind_param($stmt_detail, 'i', $id_transaksi);
mysqli_stmt_execute($stmt_detail);
$result_detail = mysqli_stmt_get_result($stmt_detail);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Detail Pesanan #<?= $id_transaksi; ?></title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>Detail Pesanan #<?= htmlspecialchars($id_transaksi); ?></h1>
  </header>
  <main style="max-width: 700px;">
    <div class="receipt" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
      <p><strong>Waktu Pesanan:</strong> <?= date('d M Y, H:i', strtotime($transaksi['waktu_transaksi'])); ?></p>
      <p><strong>Diproses oleh Kasir:</strong> <?= htmlspecialchars($transaksi['kasir']); ?></p>

      <h4 style="margin-top:20px; border-top:1px solid #eee; padding-top:15px;">Item yang harus disiapkan:</h4>
      <table style="font-size: 1.1em;">
        <tbody>
          <?php while ($item = mysqli_fetch_assoc($result_detail)): ?>
            <tr>
              <td><strong><?= $item['jumlah']; ?>x</strong></td>
              <td><?= htmlspecialchars($item['nama_menu']); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-top:30px;">
      <a href="pesanan_pending.php" class="btn-pesan" style="background-color:#6c757d;">Kembali ke Daftar Pesanan</a>

      <div>
        <a href="proses_telah_dilayani.php?id=<?= $id_transaksi; ?>" class="btn-pesan" style="background-color: #28a745; margin-right: 10px;"
          onclick="return confirm('Anda yakin pesanan ini Telah Dilayani? Status akan diubah menjadi `selesai`.');">
          Telah Dilayani
        </a>
        <a href="bukti.php?id=<?= $id_transaksi; ?>" target="_blank" class="btn-pesan">
          Cetak Struk
        </a>
      </div>
    </div>
  </main>
</body>

</html>