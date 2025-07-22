<?php
session_start();

// Validasi jika id ada di URL
if (isset($_GET['id'])) {
  $id_menu = $_GET['id'];

  // Hapus item dari session keranjang jika ada
  if (isset($_SESSION['keranjang'][$id_menu])) {
    unset($_SESSION['keranjang'][$id_menu]);
  }
}

// Kembali ke halaman keranjang
header('Location: keranjang.php');
exit();
