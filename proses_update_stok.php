<?php
session_start();
require_once 'db_connect.php';

// Proteksi Halaman: Hanya untuk role 'dapur' dan 'administrator'
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['dapur', 'administrator'])) {
  die("Akses ditolak.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_inventory']) && isset($_POST['stok_baru'])) {
  $id_inventory = $_POST['id_inventory'];
  $stok_baru = $_POST['stok_baru'];

  // Validasi input
  if (is_numeric($id_inventory) && is_numeric($stok_baru) && $stok_baru >= 0) {
    $sql = "UPDATE inventory SET stok_saat_ini = ? WHERE id_inventory = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'di', $stok_baru, $id_inventory);
    mysqli_stmt_execute($stmt);
  }
}

// Kembali ke halaman inventory setelah proses selesai
header('Location: inventory.php');
exit();
