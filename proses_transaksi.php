<?php
session_start();
require_once 'db_connect.php';

// 1. Validasi
if (empty($_SESSION['keranjang']) || !isset($_POST['metode_pembayaran'])) {
  header('Location: index.php');
  exit();
}

// Mulai transaksi database untuk memastikan semua query berhasil
mysqli_begin_transaction($conn);

try {
  // 2. Hitung total belanja & ambil detail produk dari DB (lebih aman)
  $total_bayar = 0;
  $detail_pesanan = [];
  $ids_in_cart = array_keys($_SESSION['keranjang']);
  $ids_string = implode(',', $ids_in_cart);
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
  $id_user = $_SESSION['user_id']; // Menggunakan ID user yang sedang login
  $metode_pembayaran = $_POST['metode_pembayaran'];
  $waktu_transaksi = date('Y-m-d H:i:s');

  // Logika status pesanan berdasarkan metode pembayaran
  // Jika Hutang, status 'diproses' (masuk piutang). Jika Tunai/QRIS, status 'selesai'.
  // Status untuk semua transaksi baru selalu 'pending' agar muncul di daftar pesanan
  $status_pesanan = 'pending';

  $sql_trans = "INSERT INTO transaction (id_user, waktu_transaksi, total_bayar, metode_pembayaran, status_pesanan) VALUES (?, ?, ?, ?, ?)";
  $stmt_trans = mysqli_prepare($conn, $sql_trans);
  mysqli_stmt_bind_param($stmt_trans, 'isdss', $id_user, $waktu_transaksi, $total_bayar, $metode_pembayaran, $status_pesanan);
  mysqli_stmt_execute($stmt_trans);

  // Ambil ID transaksi baru yang baru saja dibuat
  $id_transaksi_baru = mysqli_insert_id($conn);
  createLog($conn, "Membuat transaksi baru #" . $id_transaksi_baru . " dengan total Rp " . number_format($total_bayar));

  // 4. Masukkan data ke tabel 'transaction_detail'
  $sql_detail = "INSERT INTO transaction_detail (id_transaction, id_menu, jumlah, harga_saat_transaksi) VALUES (?, ?, ?, ?)";
  $stmt_detail = mysqli_prepare($conn, $sql_detail);

  foreach ($detail_pesanan as $item) {
    mysqli_stmt_bind_param($stmt_detail, 'iiid', $id_transaksi_baru, $item['id_menu'], $item['jumlah'], $item['harga_saat_transaksi']);
    mysqli_stmt_execute($stmt_detail);
  }

  // (Opsional) Di sini Anda bisa menambahkan logika untuk mengurangi stok di tabel 'inventory'

  // 5. Jika semua berhasil, commit transaksi
  mysqli_commit($conn);

  // 6. Kosongkan keranjang dan arahkan ke halaman bukti
  unset($_SESSION['keranjang']);
  header('Location: bukti.php?id=' . $id_transaksi_baru);
  exit();
} catch (Exception $e) {
  // Jika ada error, batalkan semua perubahan
  mysqli_rollback($conn);
  echo "Terjadi kesalahan saat memproses pesanan: " . $e->getMessage();
  exit();
}
