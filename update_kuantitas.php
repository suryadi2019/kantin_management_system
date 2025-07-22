<?php
session_start();

// Validasi jika id dan action ada di URL
if (isset($_GET['id']) && isset($_GET['action'])) {
  $id_menu = $_GET['id'];
  $action = $_GET['action'];

  // Pastikan item ada di keranjang
  if (isset($_SESSION['keranjang'][$id_menu])) {
    if ($action == 'tambah') {
      $_SESSION['keranjang'][$id_menu]++;
    } elseif ($action == 'kurang') {
      // Kurangi jumlah
      $_SESSION['keranjang'][$id_menu]--;
      // Jika jumlah menjadi 0 atau kurang, hapus item dari keranjang
      if ($_SESSION['keranjang'][$id_menu] <= 0) {
        unset($_SESSION['keranjang'][$id_menu]);
      }
    }
  }
}

// Kembali ke halaman keranjang
header('Location: keranjang.php');
exit();
