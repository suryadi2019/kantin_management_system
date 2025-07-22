<?php
// Mulai session untuk menggunakan variabel $_SESSION
session_start();

// Ambil id menu dari URL
$id_menu = $_GET['id'];

// Jika id menu ada
if (isset($id_menu)) {
  // Jika keranjang belum ada, buat sebagai array kosong
  if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
  }

  // Jika menu tersebut sudah ada di keranjang, tambah jumlahnya
  if (isset($_SESSION['keranjang'][$id_menu])) {
    $_SESSION['keranjang'][$id_menu]++;
  } else {
    // Jika belum ada, tambahkan ke keranjang dengan jumlah 1
    $_SESSION['keranjang'][$id_menu] = 1;
  }
}

// Arahkan pengguna ke halaman keranjang belanja
header('Location: keranjang.php');
exit();
