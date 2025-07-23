<?php
include 'navbar.php';
require_once 'db_connect.php';

// Proteksi Halaman: Hanya untuk administrator dan supervisor
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['administrator', 'supervisor'])) {
  die("<div style='text-align:center; margin-top:50px;'><h1>Akses Ditolak!</h1><p>Halaman ini hanya untuk Administrator atau Supervisor.</p><a href='index.php'>Kembali</a></div>");
}

$current_user_role = $_SESSION['user_role'];

// Logika untuk menentukan pegawai mana yang bisa dilihat
$sql = "SELECT id_user, nama_user, username, role FROM user";
$where_clauses = [];

if ($current_user_role == 'supervisor') {
  // Supervisor tidak bisa melihat atau mengedit administrator
  $where_clauses[] = "role != 'administrator'";
}
// Administrator bisa melihat semua, jadi tidak perlu klausa WHERE tambahan

if (!empty($where_clauses)) {
  $sql .= " WHERE " . implode(' AND ', $where_clauses);
}

$sql .= " ORDER BY CASE role WHEN 'administrator' THEN 1 WHEN 'supervisor' THEN 2 WHEN 'kasir' THEN 3 WHEN 'dapur' THEN 4 ELSE 5 END, nama_user ASC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Daftar Pegawai</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>🔧 Daftar & Modifikasi Data Pegawai</h1>
    <p>Kelola informasi dan hak akses akun pegawai.</p>
  </header>
  <main>
    <?php if (isset($_GET['status']) && $_GET['status'] == 'edit_sukses'): ?>
      <p style='color:green; text-align:center; background-color:#d4edda; padding:10px; border-radius:5px;'>Data pegawai berhasil diperbarui!</p>
    <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
      <p style='color:red; text-align:center; background-color:#f8d7da; padding:10px; border-radius:5px;'>Terjadi kesalahan atau akses tidak diizinkan.</p>
    <?php endif; ?>
    <table>
      <thead>
        <tr>
          <th>Nama Lengkap</th>
          <th>Username</th>
          <th>Role</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?= htmlspecialchars($row['nama_user']); ?></td>
              <td><?= htmlspecialchars($row['username']); ?></td>
              <td><?= ucfirst(htmlspecialchars($row['role'])); ?></td>
              <td>
                <?php
                // Tombol edit hanya muncul jika user saat ini adalah admin,
                // atau jika user saat ini adalah supervisor DAN target bukan admin.
                if ($current_user_role == 'administrator' || ($current_user_role == 'supervisor' && $row['role'] != 'administrator')) {
                  echo '<a href="edit_pegawai.php?id=' . $row['id_user'] . '" class="btn-pesan" style="background-color: #ffc107; color: black;">Edit</a>';
                } else {
                  echo '<i>Tidak ada aksi</i>';
                }
                ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" style="text-align:center;">Tidak ada data pegawai yang bisa ditampilkan.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </main>
</body>

</html>