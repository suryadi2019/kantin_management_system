<?php
// Panggil komponen utama
include 'navbar.php';
require_once 'db_connect.php';

// --- Proteksi Halaman: Diperbarui untuk menyertakan supervisor ---
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['administrator', 'supervisor'])) {
  die("<div style='text-align:center; margin-top:50px;'><h1>圻 Akses Ditolak!</h1><p>Halaman ini hanya untuk Administrator atau Supervisor.</p><a href='index.php'>Kembali ke Menu Utama</a></div>");
}

// --- Fungsi untuk mengambil data keuangan ---
function getFinancialData($conn, $transaction_where, $expense_where)
{
  $sql_kas = "SELECT SUM(total_bayar) AS total FROM transaction t " . ($transaction_where ? "WHERE status_pesanan = 'selesai' AND " . $transaction_where : "WHERE status_pesanan = 'selesai'");
  $kas = mysqli_fetch_assoc(mysqli_query($conn, $sql_kas))['total'] ?? 0;

  $sql_piutang = "SELECT SUM(total_bayar) AS total FROM transaction t " . ($transaction_where ? "WHERE status_pesanan IN ('pending', 'diproses') AND " . $transaction_where : "WHERE status_pesanan IN ('pending', 'diproses')");
  $piutang = mysqli_fetch_assoc(mysqli_query($conn, $sql_piutang))['total'] ?? 0;

  $sql_pengeluaran = "SELECT SUM(jumlah) AS total FROM expense " . ($expense_where ? "WHERE " . $expense_where : "");
  $pengeluaran = mysqli_fetch_assoc(mysqli_query($conn, $sql_pengeluaran))['total'] ?? 0;

  $omset = $kas + $piutang;
  $profit = $omset - $pengeluaran;

  return ['kas' => $kas, 'piutang' => $piutang, 'pengeluaran' => $pengeluaran, 'omset' => $omset, 'profit' => $profit];
}

// --- Logika Penentuan Rentang Waktu ---
$rentang = $_GET['rentang'] ?? 'bulanan'; // Default
$judul_rentang = "";
$data_sekarang = [];
$data_sebelumnya = [];
$is_yoy = false;

$current_year = "YEAR(CURDATE())";
$last_year = "YEAR(CURDATE()) - 1";

$where_trans_sekarang = "";
$where_expense_sekarang = "";
$where_trans_sebelumnya = "";
$where_expense_sebelumnya = "";

switch ($rentang) {
  case 'harian':
    $judul_rentang = "Hari Ini";
    $where_trans_sekarang = "DATE(t.waktu_transaksi) = CURDATE()";
    $where_expense_sekarang = "DATE(tanggal) = CURDATE()";
    break;
  case 'mingguan':
    $judul_rentang = "Minggu Ini";
    $where_trans_sekarang = "YEARWEEK(t.waktu_transaksi, 1) = YEARWEEK(CURDATE(), 1)";
    $where_expense_sekarang = "YEARWEEK(tanggal, 1) = YEARWEEK(CURDATE(), 1)";
    break;
  case 'bulanan':
    $judul_rentang = "Bulan Ini";
    $where_trans_sekarang = "YEAR(t.waktu_transaksi) = $current_year AND MONTH(t.waktu_transaksi) = MONTH(CURDATE())";
    $where_expense_sekarang = "YEAR(tanggal) = $current_year AND MONTH(tanggal) = MONTH(CURDATE())";
    break;
  case 'triwulan':
    $judul_rentang = "Triwulan Ini";
    $where_trans_sekarang = "YEAR(t.waktu_transaksi) = $current_year AND QUARTER(t.waktu_transaksi) = QUARTER(CURDATE())";
    $where_expense_sekarang = "YEAR(tanggal) = $current_year AND QUARTER(tanggal) = QUARTER(CURDATE())";
    break;
  case 'semester':
    $judul_rentang = "Semester Ini";
    $semester_cond = (date('n') <= 6) ? "BETWEEN 1 AND 6" : "BETWEEN 7 AND 12";
    $where_trans_sekarang = "YEAR(t.waktu_transaksi) = $current_year AND MONTH(t.waktu_transaksi) $semester_cond";
    $where_expense_sekarang = "YEAR(tanggal) = $current_year AND MONTH(tanggal) $semester_cond";
    break;
  case 'ytd':
    $judul_rentang = "Year-to-Date (YtD)";
    $where_trans_sekarang = "YEAR(t.waktu_transaksi) = $current_year";
    $where_expense_sekarang = "YEAR(tanggal) = $current_year";
    break;
  case 'yoy':
    $is_yoy = true;
    $judul_rentang = "YoY (Bulan Ini vs Tahun Lalu)";
    // Data tahun ini (bulan ini)
    $where_trans_sekarang = "YEAR(t.waktu_transaksi) = $current_year AND MONTH(t.waktu_transaksi) = MONTH(CURDATE())";
    $where_expense_sekarang = "YEAR(tanggal) = $current_year AND MONTH(tanggal) = MONTH(CURDATE())";
    // Data tahun lalu (bulan yang sama)
    $where_trans_sebelumnya = "YEAR(t.waktu_transaksi) = $last_year AND MONTH(t.waktu_transaksi) = MONTH(CURDATE())";
    $where_expense_sebelumnya = "YEAR(tanggal) = $last_year AND MONTH(tanggal) = MONTH(CURDATE())";
    break;
  default: // Termasuk 'semua'
    $rentang = 'semua';
    $judul_rentang = "Semua Waktu";
    break;
}

// Ambil data berdasarkan klausa WHERE yang sudah ditentukan
$data_sekarang = getFinancialData($conn, $where_trans_sekarang, $where_expense_sekarang);
if ($is_yoy) {
  $data_sebelumnya = getFinancialData($conn, $where_trans_sebelumnya, $where_expense_sebelumnya);
}

// --- Query untuk Riwayat Penjualan ---
$sql_riwayat = "SELECT t.id_transaction, t.waktu_transaksi, t.total_bayar, t.metode_pembayaran, u.nama_user AS kasir 
                FROM transaction t JOIN user u ON t.id_user = u.id_user 
                WHERE t.status_pesanan = 'selesai' " . ($where_trans_sekarang ? "AND " . $where_trans_sekarang : "") . " 
                ORDER BY t.waktu_transaksi DESC LIMIT 200";
$result_riwayat = mysqli_query($conn, $sql_riwayat);

// --- Fungsi untuk menampilkan kartu ---
function renderCard($title, $value, $class = '')
{
  echo "<div class='card'><h3 class='card-title'>$title</h3><p class='card-value $class'>Rp " . number_format($value, 0, ',', '.') . "</p></div>";
}
function renderYoYCard($title, $value_now, $value_prev, $class = '')
{
  $growth = ($value_prev != 0) ? (($value_now - $value_prev) / $value_prev) * 100 : 0;
  $growth_class = $growth >= 0 ? 'growth-positive' : 'growth-negative';
  $growth_icon = $growth >= 0 ? '▲' : '▼';

  echo "<div class='card yoy-card'>";
  echo "<h3 class='card-title'>$title</h3>";
  echo "<p class='card-value $class'>Rp " . number_format($value_now, 0, ',', '.') . "</p>";
  echo "<p class='yoy-previous'>Tahun Lalu: Rp " . number_format($value_prev, 0, ',', '.') . "</p>";
  echo "<p class='yoy-growth $growth_class'>$growth_icon " . number_format($growth, 2) . "%</p>";
  echo "</div>";
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Laporan Keuangan - Kantin RRI Banjarmasin</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .summary-cards {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-around;
      gap: 15px;
      margin-bottom: 30px;
      text-align: center;
    }

    .card {
      background: #f9f9f9;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      flex-basis: 18%;
      min-width: 150px;
    }

    .card-title {
      margin: 0 0 10px 0;
      font-size: 1em;
      color: #555;
    }

    .card-value {
      margin: 0;
      font-size: 1.5em;
      font-weight: bold;
    }

    .profit {
      color: #28a745;
    }

    .omset {
      color: #007bff;
    }

    .pengeluaran {
      color: #dc3545;
    }

    .filter-form {
      margin-bottom: 30px;
      background: #f0f0f0;
      padding: 15px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .filter-form label {
      font-weight: bold;
    }

    .filter-form select,
    .filter-form button {
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #ddd;
    }

    .filter-form button {
      border: none;
      background: #007bff;
      color: white;
      cursor: pointer;
    }

    /* Style untuk YoY Card */
    .yoy-previous {
      font-size: 0.8em;
      color: #666;
      margin: 5px 0 0 0;
    }

    .yoy-growth {
      font-size: 1.1em;
      font-weight: bold;
      margin-top: 8px;
    }

    .growth-positive {
      color: #28a745;
    }

    .growth-negative {
      color: #dc3545;
    }
  </style>
</head>

<body>
  <header>
    <h1>📊 Laporan Keuangan</h1>
    <p>Menampilkan ringkasan finansial untuk periode: <strong><?= $judul_rentang ?></strong></p>
  </header>
  <main>
    <form action="laporan_keuangan.php" method="GET" class="filter-form">
      <label for="rentang">Pilih Rentang Waktu:</label>
      <select name="rentang" id="rentang" onchange="this.form.submit()">
        <option value="harian" <?= $rentang == 'harian' ? 'selected' : '' ?>>Harian</option>
        <option value="mingguan" <?= $rentang == 'mingguan' ? 'selected' : '' ?>>Mingguan</option>
        <option value="bulanan" <?= $rentang == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
        <option value="triwulan" <?= $rentang == 'triwulan' ? 'selected' : '' ?>>Triwulan</option>
        <option value="semester" <?= $rentang == 'semester' ? 'selected' : '' ?>>Semester</option>
        <option value="ytd" <?= $rentang == 'ytd' ? 'selected' : '' ?>>Year-to-Date (YtD)</option>
        <option value="yoy" <?= $rentang == 'yoy' ? 'selected' : '' ?>>Year-on-Year (YoY) - Bulan Ini</option>
        <option value="semua" <?= $rentang == 'semua' ? 'selected' : '' ?>>Semua Waktu</option>
      </select>
    </form>

    <div class="summary-cards">
      <?php if ($is_yoy): ?>
        <?php renderYoYCard('Omset', $data_sekarang['omset'], $data_sebelumnya['omset'], 'omset'); ?>
        <?php renderYoYCard('Kas Pendapatan', $data_sekarang['kas'], $data_sebelumnya['kas']); ?>
        <?php renderYoYCard('Piutang', $data_sekarang['piutang'], $data_sebelumnya['piutang']); ?>
        <?php renderYoYCard('Pengeluaran', $data_sekarang['pengeluaran'], $data_sebelumnya['pengeluaran'], 'pengeluaran'); ?>
        <?php renderYoYCard('Profit', $data_sekarang['profit'], $data_sebelumnya['profit'], 'profit'); ?>
      <?php else: ?>
        <?php renderCard('Omset', $data_sekarang['omset'], 'omset'); ?>
        <?php renderCard('Kas Pendapatan', $data_sekarang['kas']); ?>
        <?php renderCard('Piutang', $data_sekarang['piutang']); ?>
        <?php renderCard('Pengeluaran', $data_sekarang['pengeluaran'], 'pengeluaran'); ?>
        <?php renderCard('Profit', $data_sekarang['profit'], 'profit'); ?>
      <?php endif; ?>
    </div>

    <h2>📜 Riwayat Penjualan (Periode: <?= $judul_rentang ?>)</h2>
    <table>
      <thead>
        <tr>
          <th>No. Transaksi</th>
          <th>Waktu</th>
          <th>Kasir</th>
          <th>Metode Bayar</th>
          <th>Total</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result_riwayat && mysqli_num_rows($result_riwayat) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result_riwayat)): ?>
            <tr>
              <td>#<?= $row['id_transaction']; ?></td>
              <td><?= date('d M Y, H:i', strtotime($row['waktu_transaksi'])); ?></td>
              <td><?= htmlspecialchars($row['kasir']); ?></td>
              <td><?= htmlspecialchars($row['metode_pembayaran']); ?></td>
              <td>Rp <?= number_format($row['total_bayar'], 0, ',', '.'); ?></td>
              <td>
                <a href="hapus_transaksi.php?id=<?= $row['id_transaction']; ?>" class="btn-hapus" onclick="return confirm('PERINGATAN: Anda akan menghapus transaksi ini. Lanjutkan?');">Hapus</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align:center;">Tidak ada riwayat penjualan pada rentang waktu ini.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </main>
</body>

</html>