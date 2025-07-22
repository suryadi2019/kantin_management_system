<?php
// File: db_connect.php

$servername = "127.0.0.1"; // Biasanya "localhost" atau "127.0.0.1"
$username = "root";        // Username default XAMPP
$password = "";            // Password default XAMPP adalah kosong
$dbname = "kantin_management1.4";     // Nama database yang telah Anda buat

// Membuat koneksi
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Memeriksa koneksi
if (!$conn) {
  // Jika koneksi gagal, hentikan skrip dan tampilkan pesan error
  die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Set charset untuk memastikan data (seperti emoji) tampil dengan benar
mysqli_set_charset($conn, "utf8mb4");

function createLog($conn, $aktivitas)
{
  // Pastikan session sudah dimulai
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }

  $id_user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
  $username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'System/Guest';
  $role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'System';

  $sql = "INSERT INTO syslog (id_user, username, role, aktivitas) VALUES (?, ?, ?, ?)";
  $stmt = mysqli_prepare($conn, $sql);

  // Bind parameter, 'i' untuk integer, 's' untuk string
  mysqli_stmt_bind_param($stmt, 'isss', $id_user, $username, $role, $aktivitas);
  mysqli_stmt_execute($stmt);
}
