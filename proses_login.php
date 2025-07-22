<?php
session_start();
require_once 'db_connect.php';

// Ambil data dari form
$username = $_POST['username'];
$password = $_POST['password']; // Di aplikasi nyata, password dari form tidak boleh langsung dipakai

// Query untuk mencari user berdasarkan username (Gunakan prepared statement!)
$sql = "SELECT id_user, nama_user, username, password, role FROM user WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {
  // Verifikasi password
  // Catatan: Karena kita pakai data dummy 'hash_password_contoh', kita bandingkan langsung.
  // Di aplikasi nyata, gunakan password_verify($password, $user['password']);
  // GANTI baris lama dengan baris ini:
  if (password_verify($password, $user['password'])) { // GANTI INI DENGAN LOGIKA HASHING
    // Login berhasil, simpan data ke session
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['user_name'] = $user['nama_user'];
    $_SESSION['user_role'] = $user['role'];
    createLog($conn, "User berhasil login.");
    // Arahkan ke halaman utama setelah login
    header("Location: index.php");
    exit();
  }
}

// Jika username tidak ditemukan atau password salah
header("Location: login.php?error=1");
exit();
