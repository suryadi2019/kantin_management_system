<?php
require_once 'db_connect.php';
session_start();

// Proteksi Halaman: Hanya untuk administrator dan supervisor
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['administrator', 'supervisor'])) {
  die("Akses ditolak.");
}

// Validasi ID Transaksi dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header('Location: daftar_piutang.php');
  exit();
}

$id_transaksi = $_GET['id'];

// Update status pesanan menjadi 'selesai'. 
// Ini akan secara otomatis membuatnya dihitung sebagai 'kas' dan bukan 'piutang' lagi di laporan keuangan.
$sql = "UPDATE transaction SET status_pesanan = 'selesai' WHERE id_transaction = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_transaksi);

if (mysqli_stmt_execute($stmt)) {
  // Catat log aktivitas
  createLog($conn, "Melunasi piutang untuk transaksi #" . $id_transaksi);
  // Jika berhasil, kembalikan ke halaman daftar piutang dengan pesan sukses
  header('Location: daftar_piutang.php?status=lunas_sukses');
  exit();
} else {
  die("Gagal memperbarui status pelunasan.");
}
