<?php
// Panggil komponen utama
include 'navbar.php';
require_once 'db_connect.php';

// --- Proteksi Halaman: Hanya untuk administrator ---
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'administrator') {
  die("<div style='text-align:center; margin-top:50px;'><h1>Akses Ditolak!</h1><p>Halaman ini hanya untuk Administrator.</p><a href='index.php'>Kembali</a></div>");
}

// --- Query untuk mengambil semua data log, diurutkan dari yang terbaru ---
$sql_logs = "SELECT id_log, username, role, waktu, aktivitas FROM syslog ORDER BY waktu DESC";
$result_logs = mysqli_query($conn, $sql_logs);

?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>System Log Aktivitas</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>📜 System Log Aktivitas Pengguna</h1>
    <p>Menampilkan semua catatan aktivitas yang terekam dalam sistem.</p>
  </header>
  <main>
    <table>
      <thead>
        <tr>
          <th>ID Log</th>
          <th>Waktu</th>
          <th>Username</th>
          <th>Role</th>
          <th>Aktivitas</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result_logs && mysqli_num_rows($result_logs) > 0): ?>
          <?php while ($log = mysqli_fetch_assoc($result_logs)): ?>
            <tr>
              <td><?= $log['id_log']; ?></td>
              <td><?= date('d M Y, H:i:s', strtotime($log['waktu'])); ?></td>
              <td><?= htmlspecialchars($log['username']); ?></td>
              <td><?= htmlspecialchars($log['role']); ?></td>
              <td><?= htmlspecialchars($log['aktivitas']); ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" style="text-align:center;">Belum ada aktivitas yang tercatat.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </main>
</body>

</html>