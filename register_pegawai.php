<?php
include 'navbar.php';
require_once 'db_connect.php';

// Proteksi Halaman: Diperbarui untuk menyertakan supervisor
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['administrator', 'supervisor'])) {
  die("<div style='text-align:center; margin-top:50px;'><h1>Akses Ditolak!</h1><p>Halaman ini hanya untuk Administrator atau Supervisor.</p><a href='index.php'>Kembali</a></div>");
}

$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_user = $_POST['nama_user'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $role = $_POST['role'];

  if (!empty($nama_user) && !empty($username) && !empty($password) && !empty($role)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO user (nama_user, username, password, role) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssss', $nama_user, $username, $hashed_password, $role);

    if (mysqli_stmt_execute($stmt)) {
      $pesan = "<p style='color:green; text-align:center;'>Pegawai baru berhasil diregistrasi!</p>";
    } else {
      $pesan = "<p style='color:red; text-align:center;'>Gagal meregistrasi pegawai. Username mungkin sudah ada.</p>";
    }
  } else {
    $pesan = "<p style='color:red; text-align:center;'>Semua kolom wajib diisi!</p>";
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Register Pegawai</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <main>
    <h2>👤 Registrasi Pegawai Baru</h2>
    <?= $pesan ?>
    <form action="register_pegawai.php" method="POST">
      <div class="form-group">
        <label for="nama_user">Nama Lengkap</label>
        <input type="text" id="nama_user" name="nama_user" required>
      </div>
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="form-group">
        <label for="role">Role</label>
        <select id="role" name="role" required>
          <option value="">-- Pilih Role --</option>
          <option value="administrator">Administrator</option>
          <option value="supervisor">Supervisor</option>
          <option value="kasir">Kasir</option>
          <option value="dapur">Dapur</option>
        </select>
      </div>
      <button type="submit" class="btn-pesan">Register</button>
    </form>
  </main>
</body>

</html>