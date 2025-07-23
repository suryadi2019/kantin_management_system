<?php
// Panggil navbar, yang juga akan memulai session
include 'navbar.php';
// Panggil koneksi DB
require_once 'db_connect.php';

// Proteksi Halaman: Jika belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

// --- PERBAIKAN LOGIKA QUERY ---
// Query diubah agar HANYA mengambil transaksi dengan status 'pending'.
// Status 'diproses' kini secara eksklusif digunakan untuk menandai piutang yang aktif
// dan tidak akan muncul lagi di daftar pesanan untuk dapur.
$sql = "SELECT 
            t.id_transaction, 
            t.waktu_transaksi, 
            t.total_bayar,
            t.status_pesanan,
            u.nama_user AS kasir, 
            c.nama_pelanggan 
        FROM 
            transaction t
        JOIN 
            user u ON t.id_user = u.id_user
        LEFT JOIN 
            customer c ON t.id_customer = c.id_customer
        WHERE 
            t.status_pesanan = 'pending' -- HANYA TAMPILKAN YANG PERLU DIMASAK
        ORDER BY 
            t.waktu_transaksi DESC
        LIMIT 50";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Pesanan Terbaru</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>📋 Daftar Pesanan Terbaru (Untuk Dapur)</h1>
    <p>Menampilkan transaksi terakhir yang perlu disiapkan dan dilayani.</p>
  </header>
  <main>
    <?php if (isset($_GET['status']) && $_GET['status'] == 'pesanan_baru_sukses'): ?>
      <p style='color:green; text-align:center; background-color:#d4edda; padding:10px; border-radius:5px;'>Pesanan baru berhasil dibuat dan ditambahkan ke daftar!</p>
    <?php elseif (isset($_GET['status']) && $_GET['status'] == 'hutang_sukses'): ?>
      <p style='color:green; text-align:center; background-color:#d4edda; padding:10px; border-radius:5px;'>Transaksi hutang berhasil dicatat!</p>
    <?php endif; ?>
    <table>
      <thead>
        <tr>
          <th>No. Transaksi</th>
          <th>Waktu</th>
          <th>Kasir</th>
          <th>Pelanggan</th>
          <th>Total</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td>#<?= $row['id_transaction']; ?></td>
              <td><?= date('d M Y, H:i', strtotime($row['waktu_transaksi'])); ?></td>
              <td><?= htmlspecialchars($row['kasir']); ?></td>
              <td><?= $row['nama_pelanggan'] ? htmlspecialchars($row['nama_pelanggan']) : '<i>Non-Member</i>'; ?></td>
              <td>Rp <?= number_format($row['total_bayar'], 0, ',', '.'); ?></td>
              <td>
                <a href="detail_pesanan.php?id=<?= $row['id_transaction']; ?>" class="btn-pesan" style="background-color:#17a2b8;">Lihat Detail</a>

                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'administrator'): ?>
                  <a href="hapus_transaksi.php?id=<?= $row['id_transaction']; ?>&source=pending" class="btn-hapus"
                    style="margin-left: 5px;"
                    onclick="return confirm('PERINGATAN: Anda akan menghapus transaksi ini secara permanen. Lanjutkan?');">
                    Hapus
                  </a>
                <?php endif; ?>

              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align:center;">Tidak ada pesanan baru yang perlu disiapkan saat ini.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </main>
</body>

</html>