<?php
include 'navbar.php';
require_once 'db_connect.php';

// Proteksi Halaman: Hanya untuk administrator dan supervisor
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['administrator', 'supervisor'])) {
  die("<div style='text-align:center; margin-top:50px;'><h1>Akses Ditolak!</h1><p>Halaman ini hanya untuk Administrator atau Supervisor.</p><a href='index.php'>Kembali</a></div>");
}

// Query untuk mengambil data piutang (transaksi hutang yang belum selesai)
$sql = "SELECT 
            t.id_transaction, 
            t.waktu_transaksi, 
            t.total_bayar,
            c.nama_pelanggan,
            c.kontak,
            u.nama_user AS kasir
        FROM 
            transaction t
        JOIN 
            user u ON t.id_user = u.id_user
        JOIN 
            customer c ON t.id_customer = c.id_customer
        WHERE 
            t.metode_pembayaran = 'Hutang' AND t.status_pesanan = 'diproses'
        ORDER BY 
            t.waktu_transaksi ASC"; // Urutkan dari yang paling lama

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Daftar Piutang</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>💰 Daftar Piutang Belum Lunas</h1>
    <p>Menampilkan semua transaksi hutang yang masih aktif.</p>
  </header>
  <main>
    <?php if (isset($_GET['status']) && $_GET['status'] == 'lunas_sukses'): ?>
      <p style='color:green; text-align:center; background-color:#d4edda; padding:10px; border-radius:5px;'>Piutang berhasil ditandai lunas!</p>
    <?php endif; ?>
    <table>
      <thead>
        <tr>
          <th>No. Transaksi</th>
          <th>Waktu Hutang</th>
          <th>Nama Pelanggan</th>
          <th>Kontak</th>
          <th>Total Hutang</th>
          <th>Kasir</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td>#<?= $row['id_transaction']; ?></td>
              <td><?= date('d M Y, H:i', strtotime($row['waktu_transaksi'])); ?></td>
              <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
              <td><?= htmlspecialchars($row['kontak']); ?></td>
              <td>Rp <?= number_format($row['total_bayar'], 0, ',', '.'); ?></td>
              <td><?= htmlspecialchars($row['kasir']); ?></td>
              <td>
                <a href="proses_pelunasan.php?id=<?= $row['id_transaction']; ?>" class="btn-pesan" style="background-color: #28a745;"
                  onclick="return confirm('Anda yakin piutang ini telah lunas? Tindakan ini akan memindahkan data ke laporan keuangan.');">
                  Tandai Telah Lunas
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" style="text-align:center;">Tidak ada data piutang yang belum lunas.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </main>
</body>

</html>