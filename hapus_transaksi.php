<?php
session_start();
require_once 'db_connect.php';

// --- Keamanan: Pastikan hanya administrator yang bisa mengakses ---
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'administrator') {
  die("Akses ditolak. Anda bukan administrator.");
}

// --- Validasi ID Transaksi dari URL ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("ID transaksi tidak valid.");
}

$id_transaksi = $_GET['id'];

// Mulai transaksi database untuk memastikan semua query berjalan atau tidak sama sekali
mysqli_begin_transaction($conn);

try {
  // 1. Hapus terlebih dahulu semua item dari `transaction_detail` yang terkait
  $sql_delete_detail = "DELETE FROM transaction_detail WHERE id_transaction = ?";
  $stmt_detail = mysqli_prepare($conn, $sql_delete_detail);
  mysqli_stmt_bind_param($stmt_detail, 'i', $id_transaksi);
  mysqli_stmt_execute($stmt_detail);

  // 2. Setelah detail dihapus, hapus transaksi utama dari tabel `transaction`
  $sql_delete_trans = "DELETE FROM transaction WHERE id_transaction = ?";
  $stmt_trans = mysqli_prepare($conn, $sql_delete_trans);
  mysqli_stmt_bind_param($stmt_trans, 'i', $id_transaksi);
  mysqli_stmt_execute($stmt_trans);

  // Catat log sebelum redirect
  createLog($conn, "Menghapus transaksi #" . $id_transaksi);

  // Jika semua query berhasil, commit perubahan
  mysqli_commit($conn);

  // Redirect kembali ke halaman laporan dengan pesan sukses (opsional)
  header('Location: laporan_keuangan.php?status=hapus_sukses');
  exit();
} catch (Exception $e) {
  // Jika terjadi kesalahan, batalkan semua perubahan (rollback)
  mysqli_rollback($conn);

  // Tampilkan pesan error
  die("Gagal menghapus transaksi: " . $e->getMessage());
}
