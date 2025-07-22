<?php
require_once 'db_connect.php';
session_start(); // Diperlukan untuk mengakses nama kasir jika ada

if (!isset($_GET['id'])) {
  header('Location: index.php');
  exit();
}

$id_transaksi = $_GET['id'];

// Ambil data transaksi utama, JOIN dengan user untuk mendapatkan nama kasir
$sql_trans = "SELECT t.*, u.nama_user 
              FROM transaction t 
              JOIN user u ON t.id_user = u.id_user
              WHERE t.id_transaction = ?";
$stmt_trans = mysqli_prepare($conn, $sql_trans);
mysqli_stmt_bind_param($stmt_trans, 'i', $id_transaksi);
mysqli_stmt_execute($stmt_trans);
$result_trans = mysqli_stmt_get_result($stmt_trans);
$transaksi = mysqli_fetch_assoc($result_trans);

// Ambil data detail transaksi
$sql_detail = "SELECT td.*, m.nama_menu 
               FROM transaction_detail td 
               JOIN menu m ON td.id_menu = m.id_menu 
               WHERE td.id_transaction = ?";
$stmt_detail = mysqli_prepare($conn, $sql_detail);
mysqli_stmt_bind_param($stmt_detail, 'i', $id_transaksi);
mysqli_stmt_execute($stmt_detail);
$result_detail = mysqli_fetch_all(mysqli_stmt_get_result($stmt_detail), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Struk Transaksi #<?= htmlspecialchars($transaksi['id_transaction']); ?></title>
  <style>
    /* CSS untuk tampilan cetak struk thermal */
    @media print {
      body {
        margin: 0;
        padding: 0;
        background-color: #fff;
      }

      .page-actions {
        display: none;
        /* Sembunyikan tombol saat mencetak */
      }

      .receipt-container {
        box-shadow: none;
        border: none;
        width: 100%;
        margin: 0;
        padding: 0;
      }
    }

    body {
      font-family: 'Courier New', 'Lucida Console', monospace;
      /* Font monospaced agar rapi */
      font-size: 12px;
      /* Ukuran font kecil untuk struk */
      color: #000;
      background-color: #f0f0f0;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px 0;
    }

    .receipt-container {
      width: 300px;
      /* Lebar umum untuk printer thermal 58mm atau 80mm */
      padding: 15px;
      background: #fff;
      border: 1px dashed #ccc;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .header-text {
      text-align: center;
      margin-bottom: 10px;
    }

    .header-text h3 {
      margin: 0;
      font-size: 16px;
    }

    .header-text p {
      margin: 2px 0;
    }

    .separator {
      border-top: 1px dashed #000;
      margin: 10px 0;
    }

    .meta-info {
      display: flex;
      justify-content: space-between;
    }

    table.items {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    table.items th,
    table.items td {
      padding: 3px 0;
    }

    table.items .item-name {
      text-align: left;
    }

    table.items .item-qty,
    table.items .item-price {
      text-align: right;
      white-space: nowrap;
      /* Mencegah harga pindah baris */
    }

    /* Normalisasi tampilan harga agar rapi */
    .price-col {
      display: flex;
      justify-content: space-between;
    }

    .footer-text {
      text-align: center;
      margin-top: 15px;
    }

    .page-actions {
      margin-top: 20px;
      text-align: center;
    }

    .btn {
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 5px;
      color: white;
      cursor: pointer;
      border: none;
      font-size: 14px;
    }

    .btn-print {
      background-color: #4CAF50;
      /* Hijau */
    }

    .btn-back {
      background-color: #6c757d;
      /* Abu-abu */
    }
  </style>
</head>

<body>

  <div class="receipt-container">
    <div class="header-text">
      <h3>KANTIN RRI BANJARMASIN</h3>
      <p>Jl. Jend. A. Yani KM. 3,5 Banjarmasin</p>
      <p>Tonton yang Anda Dengar di RRI Digital, Ikut Kami @rri_pro1banjarmasin, @rri_pro2banjarmasin, @rri_pro4banjarmasin, @rribanjarmasin</p>
    </div>

    <div class="separator"></div>

    <div class="meta-info">
      <span>No: <?= htmlspecialchars($transaksi['id_transaction']); ?></span>
      <span><?= date('d/m/y H:i', strtotime($transaksi['waktu_transaksi'])); ?></span>
    </div>
    <div class="meta-info">
      <span>Kasir: <?= htmlspecialchars($transaksi['nama_user']); ?></span>
    </div>

    <div class="separator"></div>

    <table class="items">
      <tbody>
        <?php foreach ($result_detail as $item): ?>
          <tr>
            <td colspan="2" class="item-name"><?= htmlspecialchars($item['nama_menu']); ?></td>
          </tr>
          <tr>
            <td><?= $item['jumlah']; ?> x <?= number_format($item['harga_saat_transaksi'], 0, ',', '.'); ?></td>
            <td class="item-price"><?= number_format($item['harga_saat_transaksi'] * $item['jumlah'], 0, ',', '.'); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="separator"></div>

    <div class="price-col">
      <span>TOTAL</span>
      <strong>Rp <?= number_format($transaksi['total_bayar'], 0, ',', '.'); ?></strong>
    </div>
    <div class="price-col">
      <span>PEMBAYARAN</span>
      <span><?= strtoupper(htmlspecialchars($transaksi['metode_pembayaran'])); ?></span>
    </div>

    <div class="separator"></div>

    <div class="footer-text">
      <p>Terima kasih atas kunjungan Anda!</p>
    </div>
  </div>

  <div class="page-actions">
    <button onclick="window.print();" class="btn btn-print">🖨️ Cetak Struk</button>
    <a href="pesanan_pending.php" class="btn btn-back">Kembali ke Pesanan</a>
  </div>

</body>

</html>