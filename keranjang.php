<?php
session_start();
require_once 'db_connect.php';

// Jika keranjang kosong setelah ada aksi hapus/update, arahkan ke menu utama
if (empty($_SESSION['keranjang'])) {
  echo "<script>alert('Keranjang pesanan kosong!'); window.location.href='index.php';</script>";
  exit();
}

$ids_in_cart = array_keys($_SESSION['keranjang']);
$ids_string = implode(',', $ids_in_cart);
$sql = "SELECT * FROM menu WHERE id_menu IN ($ids_string)";
$result = mysqli_query($conn, $sql);
$total_belanja = 0;

?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Keranjang Pesanan</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>🛒 Keranjang Pesanan Anda</h1>
  </header>
  <main>
    <form action="pembayaran.php" method="POST">
      <table>
        <thead>
          <tr>
            <th>Nama Menu</th>
            <th>Harga Satuan</th>
            <th style="width: 15%;">Jumlah</th>
            <th>Subtotal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <?php
            $jumlah = $_SESSION['keranjang'][$row['id_menu']];
            $subtotal = $row['harga_jual'] * $jumlah;
            $total_belanja += $subtotal;
            ?>
            <tr>
              <td><?= htmlspecialchars($row['nama_menu']); ?></td>
              <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.'); ?></td>
              <td>
                <a href="update_kuantitas.php?id=<?= $row['id_menu'] ?>&action=kurang" class="btn-qty">-</a>
                <span class="qty-text"><?= $jumlah; ?></span>
                <a href="update_kuantitas.php?id=<?= $row['id_menu'] ?>&action=tambah" class="btn-qty">+</a>
              </td>
              <td>Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
              <td>
                <a href="hapus_item.php?id=<?= $row['id_menu'] ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus item ini?')">Hapus</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="3">Total Belanja</th>
            <th colspan="2">Rp <?= number_format($total_belanja, 0, ',', '.'); ?></th>
          </tr>
        </tfoot>
      </table>
      <div style="text-align: right; margin-top: 20px;">
        <a href="index.php" class="btn-pesan" style="background-color: #6c757d;">Kembali ke Menu</a>
        <button type="submit" class="btn-pesan">Lanjutkan ke Pembayaran</button>
      </div>
    </form>
  </main>
</body>

</html>