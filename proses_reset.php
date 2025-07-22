<?php
session_start();
require_once 'db_connect.php';

// --- Keamanan: Pastikan hanya administrator yang bisa mengakses ---
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'administrator') {
  die("Akses ditolak. Anda bukan administrator.");
}

// --- Mulai proses penghapusan data dengan transaksi ---
mysqli_begin_transaction($conn);

try {
  // Nonaktifkan sementara pemeriksaan foreign key untuk mengizinkan TRUNCATE
  mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

  // 1. Kosongkan tabel detail transaksi
  mysqli_query($conn, "TRUNCATE TABLE transaction_detail");

  // 2. Kosongkan tabel transaksi utama
  mysqli_query($conn, "TRUNCATE TABLE transaction");

  // 3. Kosongkan tabel pengeluaran
  mysqli_query($conn, "TRUNCATE TABLE expense");

  // Aktifkan kembali pemeriksaan foreign key
  mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

  // Catat log sebelum menampilkan alert
  createLog($conn, "Melakukan reset semua data statistik keuangan.");

  // Jika semua berhasil, commit perubahan
  mysqli_commit($conn);

  // Redirect kembali ke halaman konfigurasi dengan pesan sukses
  echo "<script>
            alert('Semua data keuangan berhasil direset ke nol.');
            window.location.href = 'konfigurasi.php';
          </script>";
  exit();
} catch (Exception $e) {
  // Jika terjadi kesalahan, batalkan semua perubahan
  mysqli_rollback($conn);

  // Aktifkan kembali foreign key checks jika terjadi error
  mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

  // Tampilkan pesan error
  die("Gagal mereset data: " . $e->getMessage());
}
