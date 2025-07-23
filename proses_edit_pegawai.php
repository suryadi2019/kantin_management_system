<?php
session_start();
require_once 'db_connect.php';

// Keamanan: Validasi bahwa request datang dari method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  die("Akses tidak valid.");
}

// Keamanan: Pastikan user yang login adalah admin atau supervisor
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['administrator', 'supervisor'])) {
  die("Akses Ditolak.");
}

// Ambil data dari form
$id_user_target = $_POST['id_user'];
$nama_user = trim($_POST['nama_user']);
$username = trim($_POST['username']);
$role = $_POST['role'];
$password = $_POST['password']; // Bisa kosong

// --- Validasi Tambahan ---
if (empty($id_user_target) || empty($nama_user) || empty($username) || empty($role)) {
  die("Semua data wajib diisi (kecuali password).");
}

// Ambil role target dari DB untuk validasi keamanan
$sql_get_role = "SELECT role FROM user WHERE id_user = ?";
$stmt_get_role = mysqli_prepare($conn, $sql_get_role);
mysqli_stmt_bind_param($stmt_get_role, 'i', $id_user_target);
mysqli_stmt_execute($stmt_get_role);
$result_role = mysqli_stmt_get_result($stmt_get_role);
$target_user = mysqli_fetch_assoc($result_role);

if (!$target_user) {
  die("Pegawai target tidak ditemukan.");
}

// Keamanan: Mencegah Supervisor mengedit Administrator
if ($_SESSION['user_role'] == 'supervisor' && $target_user['role'] == 'administrator') {
  header('Location: daftar_pegawai.php?status=error');
  exit();
}
// Keamanan: Mencegah Supervisor mengubah role seseorang menjadi administrator
if ($_SESSION['user_role'] == 'supervisor' && $role == 'administrator') {
  header('Location: daftar_pegawai.php?status=error');
  exit();
}


// --- Logika Update Data ---
if (!empty($password)) {
  // Jika password diisi, update semua termasuk password
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);
  $sql_update = "UPDATE user SET nama_user = ?, username = ?, role = ?, password = ? WHERE id_user = ?";
  $stmt_update = mysqli_prepare($conn, $sql_update);
  mysqli_stmt_bind_param($stmt_update, 'ssssi', $nama_user, $username, $role, $hashed_password, $id_user_target);
} else {
  // Jika password kosong, update data selain password
  $sql_update = "UPDATE user SET nama_user = ?, username = ?, role = ? WHERE id_user = ?";
  $stmt_update = mysqli_prepare($conn, $sql_update);
  mysqli_stmt_bind_param($stmt_update, 'sssi', $nama_user, $username, $role, $id_user_target);
}

// Eksekusi query update
if (mysqli_stmt_execute($stmt_update)) {
  createLog($conn, "Memperbarui data pegawai #" . $id_user_target . " (" . $username . ")");
  header('Location: daftar_pegawai.php?status=edit_sukses');
} else {
  // Kemungkinan error karena username duplikat
  header('Location: edit_pegawai.php?id=' . $id_user_target . '&error=username_duplikat');
}

exit();
