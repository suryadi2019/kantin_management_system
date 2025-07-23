<?php
session_start();
require_once 'db_connect.php';

// 1. Validasi
if (empty($_SESSION['keranjang']) || !isset($_POST['metode_pembayaran'])) {
  header('Location: index.php');
  exit();
}

$metode_pembayaran = $_POST['metode_pembayaran'];

// --- PERUBAHAN UTAMA: CEK METODE PEMBAYARAN ---
// Jika metode adalah Hutang, arahkan ke form pengisian data pelanggan
if ($metode_pembayaran === 'Hutang') {
  header('Location: form_pelanggan_hutang.php');
  exit();
}

// --- Logika di bawah ini hanya berjalan untuk TUNAI dan QRIS ---

mysqli_begin_transaction($conn);

try {
  // 2. Hitung total belanja
  $total_bayar = 0;
  $detail_pesanan = [];
  $ids_in_cart = array_keys($_SESSION['keranjang']);
  $ids_string = implode(',', array_map('intval', $ids_in_cart));
  $sql_menu = "SELECT * FROM menu WHERE id_menu IN ($ids_string)";
  $result_menu = mysqli_query($conn, $sql_menu);

  while ($row = mysqli_fetch_assoc($result_menu)) {
    $jumlah = $_SESSION['keranjang'][$row['id_menu']];
    $subtotal = $row['harga_jual'] * $jumlah;
    $total_bayar += $subtotal;
    $detail_pesanan[] = [
      'id_menu' => $row['id_menu'],
      'jumlah' => $jumlah,
      'harga_saat_transaksi' => $row['harga_jual']
    ];
  }

  // 3. Masukkan data ke tabel 'transaction'
  $id_user = $_SESSION['user_id'];
  $waktu_transaksi = date('Y-m-d H:i:s');
  $status_pesanan = 'pending'; // Status awal untuk pesanan non-hutang

  $sql_trans = "INSERT INTO transaction (id_user, waktu_transaksi, total_bayar, metode_pembayaran, status_pesanan) VALUES (?, ?, ?, ?, ?)";
  $stmt_trans = mysqli_prepare($conn, $sql_trans);
  mysqli_stmt_bind_param($stmt_trans, 'isdss', $id_user, $waktu_transaksi, $total_bayar, $metode_pembayaran, $status_pesanan);
  mysqli_stmt_execute($stmt_trans);

  $id_transaksi_baru = mysqli_insert_id($conn);
  createLog($conn, "Membuat transaksi baru #" . $id_transaksi_baru);

  // 4. Masukkan data ke 'transaction_detail'
  $sql_detail = "INSERT INTO transaction_detail (id_transaction, id_menu, jumlah, harga_saat_transaksi) VALUES (?, ?, ?, ?)";
  $stmt_detail = mysqli_prepare($conn, $sql_detail);
  foreach ($detail_pesanan as $item) {
    mysqli_stmt_bind_param($stmt_detail, 'iiid', $id_transaksi_baru, $item['id_menu'], $item['jumlah'], $item['harga_saat_transaksi']);
    mysqli_stmt_execute($stmt_detail);
  }

  mysqli_commit($conn);
  unset($_SESSION['keranjang']);

  // Jika bayar pakai QRIS, arahkan ke halaman QRIS. Jika tunai, langsung ke pesanan pending.
  if ($metode_pembayaran === 'QRIS') {
    header('Location: tampilkan_qris.php?id=' . $id_transaksi_baru);
  } else {
    header('Location: pesanan_pending.php?status=pesanan_baru_sukses');
  }
  exit();
} catch (Exception $e) {
  mysqli_rollback($conn);
  error_log("Gagal memproses pesanan: " . $e->getMessage());
  die("Terjadi kesalahan saat memproses pesanan Anda.");
}
