<?php
require_once 'db_connect.php';

// Validasi jika ID transaksi ada di URL
if (isset($_GET['id'])) {
  $id_transaksi = $_GET['id'];

  // Update status pesanan menjadi 'selesai'
  $sql = "UPDATE transaction SET status_pesanan = 'selesai' WHERE id_transaction = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, 'i', $id_transaksi);

  if (mysqli_stmt_execute($stmt)) {
    // Jika berhasil, arahkan ke halaman bukti/cetak struk
    header('Location: bukti.php?id=' . $id_transaksi);
    exit();
  } else {
    echo "Gagal memperbarui status pesanan.";
  }
} else {
  // Jika tidak ada ID, kembali ke halaman pesanan pending
  header('Location: pesanan_pending.php');
  exit();
}
