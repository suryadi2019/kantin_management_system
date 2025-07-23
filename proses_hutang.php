<?php
session_start();
require_once 'db_connect.php';

// 1. Validasi Input
if (empty($_SESSION['keranjang']) || !isset($_POST['nama_pelanggan']) || !isset($_POST['kontak'])) {
  header('Location: index.php');
  exit();
}

$nama_pelanggan = trim($_POST['nama_pelanggan']);
$kontak = trim($_POST['kontak']);

if (empty($nama_pelanggan) || empty($kontak)) {
  die("Nama dan Kontak pelanggan tidak boleh kosong.");
}

mysqli_begin_transaction($conn);

try {
  // 2. Cari atau Buat Pelanggan (Customer) Baru
  $id_customer = null;
  $sql_find_customer = "SELECT id_customer FROM customer WHERE nama_pelanggan = ? AND kontak = ?";
  $stmt_find = mysqli_prepare($conn, $sql_find_customer);
  mysqli_stmt_bind_param($stmt_find, 'ss', $nama_pelanggan, $kontak);
  mysqli_stmt_execute($stmt_find);
  $result_customer = mysqli_stmt_get_result($stmt_find);

  if ($customer = mysqli_fetch_assoc($result_customer)) {
    $id_customer = $customer['id_customer'];
  } else {
    $sql_insert_customer = "INSERT INTO customer (nama_pelanggan, kontak) VALUES (?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert_customer);
    mysqli_stmt_bind_param($stmt_insert, 'ss', $nama_pelanggan, $kontak);
    mysqli_stmt_execute($stmt_insert);
    $id_customer = mysqli_insert_id($conn);
  }

  // 3. Hitung total belanja dari keranjang (sama seperti proses_transaksi.php)
  $total_bayar = 0;
  $detail_pesanan = [];
  $ids_in_cart = array_keys($_SESSION['keranjang']);
  $ids_string = implode(',', array_map('intval', $ids_in_cart));
  $sql_menu = "SELECT id_menu, harga_jual FROM menu WHERE id_menu IN ($ids_string)";
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

  // 4. Masukkan data ke tabel 'transaction'
  $id_user = $_SESSION['user_id'];
  $metode_pembayaran = 'Hutang';
  $waktu_transaksi = date('Y-m-d H:i:s');
  // Status 'diproses' menandakan ini adalah piutang aktif
  $status_pesanan = 'diproses';

  $sql_trans = "INSERT INTO transaction (id_user, id_customer, waktu_transaksi, total_bayar, metode_pembayaran, status_pesanan) VALUES (?, ?, ?, ?, ?, ?)";
  $stmt_trans = mysqli_prepare($conn, $sql_trans);
  mysqli_stmt_bind_param($stmt_trans, 'iisdss', $id_user, $id_customer, $waktu_transaksi, $total_bayar, $metode_pembayaran, $status_pesanan);
  mysqli_stmt_execute($stmt_trans);

  $id_transaksi_baru = mysqli_insert_id($conn);
  createLog($conn, "Membuat transaksi hutang baru #" . $id_transaksi_baru . " oleh " . $nama_pelanggan);

  // 5. Masukkan data ke tabel 'transaction_detail'
  $sql_detail = "INSERT INTO transaction_detail (id_transaction, id_menu, jumlah, harga_saat_transaksi) VALUES (?, ?, ?, ?)";
  $stmt_detail = mysqli_prepare($conn, $sql_detail);

  foreach ($detail_pesanan as $item) {
    mysqli_stmt_bind_param($stmt_detail, 'iiid', $id_transaksi_baru, $item['id_menu'], $item['jumlah'], $item['harga_saat_transaksi']);
    mysqli_stmt_execute($stmt_detail);
  }

  // 6. Commit transaksi
  mysqli_commit($conn);

  // 7. Kosongkan keranjang
  unset($_SESSION['keranjang']);

  // 8. Arahkan ke daftar pesanan
  header('Location: pesanan_pending.php?status=hutang_sukses');
  exit();
} catch (Exception $e) {
  mysqli_rollback($conn);
  error_log("Gagal memproses hutang: " . $e->getMessage());
  die("Terjadi kesalahan saat menyimpan transaksi hutang.");
}
