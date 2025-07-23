<?php
include 'navbar.php';
require_once 'db_connect.php';

// Proteksi Halaman: Hanya untuk administrator dan supervisor
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['administrator', 'supervisor'])) {
  die("Akses Ditolak.");
}

// Validasi ID pegawai dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header('Location: daftar_pegawai.php');
  exit();
}

$id_pegawai_target = $_GET['id'];
$current_user_role = $_SESSION['user_role'];

// Ambil data pegawai yang akan diedit
$sql = "SELECT id_user, nama_user, username, role FROM user WHERE id_user = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_pegawai_target);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pegawai = mysqli_fetch_assoc($result);

if (!$pegawai) {
  die("Pegawai tidak ditemukan.");
}

// Keamanan: Supervisor tidak boleh mengedit Administrator
if ($current_user_role == 'supervisor' && $pegawai['role'] == 'administrator') {
  header('Location: daftar_pegawai.php?status=error');
  exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Edit Pegawai: <?= htmlspecialchars($pegawai['nama_user']); ?></title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>Edit Pegawai: <?= htmlspecialchars($pegawai['nama_user']); ?></h1>
  </header>
  <main>
    <form action="proses_edit_pegawai.php" method="POST">
      <input type="hidden" name="id_user" value="<?= $pegawai['id_user']; ?>">

      <div class="form-group">
        <label for="nama_user">Nama Lengkap</label>
        <input type="text" id="nama_user" name="nama_user" value="<?= htmlspecialchars($pegawai['nama_user']); ?>" required>
      </div>
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($pegawai['username']); ?>" required>
      </div>
      <div class="form-group">
        <label for="role">Role</label>
        <select id="role" name="role" required>
          <?php if ($current_user_role == 'administrator'): ?>
            <option value="administrator" <?= $pegawai['role'] == 'administrator' ? 'selected' : ''; ?>>Administrator</option>
          <?php endif; ?>
          <option value="supervisor" <?= $pegawai['role'] == 'supervisor' ? 'selected' : ''; ?>>Supervisor</option>
          <option value="kasir" <?= $pegawai['role'] == 'kasir' ? 'selected' : ''; ?>>Kasir</option>
          <option value="dapur" <?= $pegawai['role'] == 'dapur' ? 'selected' : ''; ?>>Dapur</option>
        </select>
      </div>
      <hr style="margin: 20px 0;">
      <div class="form-group">
        <label for="password">Password Baru (Opsional)</label>
        <input type="password" id="password" name="password" placeholder="Isi hanya jika ingin mengganti password">
        <small>Biarkan kosong jika tidak ingin mengubah password.</small>
      </div>

      <div style="margin-top: 30px; text-align: right;">
        <a href="daftar_pegawai.php" class="btn-pesan" style="background-color:#6c757d;">Batal</a>
        <button type="submit" class="btn-pesan">Simpan Perubahan</button>
      </div>
    </form>
  </main>
</body>

</html>