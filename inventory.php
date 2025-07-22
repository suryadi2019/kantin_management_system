<?php
include 'navbar.php';
require_once 'db_connect.php';

// Proteksi Halaman: Diperbarui untuk menyertakan supervisor
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['dapur', 'administrator', 'supervisor'])) {
  die("<div style='text-align:center; margin-top:50px;'><h1>Akses Ditolak!</h1><p>Halaman ini hanya untuk Staf Dapur, Supervisor, atau Administrator.</p><a href='index.php'>Kembali</a></div>");
}

$pesan = '';
// --- Logika untuk Menambah Menu Baru (tidak diubah) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_menu') {
  // ... (Kode PHP untuk menambah menu tetap sama) ...
}

// --- Query untuk mengambil data inventory ---
$sql_inventory = "SELECT id_inventory, nama_bahan, stok_saat_ini, satuan FROM inventory ORDER BY nama_bahan ASC";
$result_inventory = mysqli_query($conn, $sql_inventory);

// --- Query untuk mengambil data menu ---
$sql_menu_list = "SELECT id_menu, nama_menu, harga_jual, kategori FROM menu ORDER BY kategori, nama_menu ASC";
$result_menu_list = mysqli_query($conn, $sql_menu_list);

?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Dapur Menu & Bahan Baku</title>
  <link rel="stylesheet" href="style.css">
  <style>
    /* =============================================================== */
    /* CSS BARU UNTUK LAYOUT BERSEBELAHAN (SIDE-BY-SIDE)         */
    /* =============================================================== */
    .dapur-container {
      display: flex;
      /* Mengaktifkan Flexbox */
      flex-wrap: wrap;
      /* Izinkan wrap jika layar terlalu kecil */
      gap: 20px;
      /* Jarak antara dua kolom */
    }

    .kolom-kiri,
    .kolom-kanan {
      flex: 1;
      /* Membuat kedua kolom memiliki lebar yang sama */
      min-width: 350px;
      /* Lebar minimum sebelum kolom turun ke bawah */
    }

    .table-container {
      border: 1px solid #ddd;
      padding: 20px;
      border-radius: 8px;
      background-color: #f9f9f9;
      height: 100%;
      /* Memastikan tinggi kontainer sama */
    }

    .table-container h3 {
      margin-top: 0;
      border-bottom: 2px solid #eee;
      padding-bottom: 10px;
    }

    /* CSS kecil untuk merapikan form update stok (tetap sama) */
    .form-stok {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .input-stok {
      width: 80px;
      padding: 5px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .btn-update-stok {
      padding: 6px 10px;
      border: none;
      background-color: #17a2b8;
      /* Biru Info */
      color: white;
      border-radius: 4px;
      cursor: pointer;
    }

    .btn-update-stok:hover {
      background-color: #138496;
    }
  </style>
</head>

<body>
  <header>
    <h1>📦 Dapur Menu & Bahan Baku</h1>
    <p>Kelola daftar menu, resep, dan stok bahan baku di halaman ini.</p>
  </header>
  <main>
    <?= $pesan ?>

    <div class="dapur-container">

      <div class="kolom-kiri">
        <div class="table-container">
          <h3>✏️ Daftar Menu (Edit Harga & Resep)</h3>
          <table>
            <thead>
              <tr>
                <th>Nama Menu</th>
                <th>Kategori</th>
                <th>Harga Jual</th>
                <th style="width:15%">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($result_menu_list) > 0): ?>
                <?php while ($menu = mysqli_fetch_assoc($result_menu_list)): ?>
                  <tr>
                    <td><?= htmlspecialchars($menu['nama_menu']); ?></td>
                    <td><?= htmlspecialchars($menu['kategori']); ?></td>
                    <td>Rp <?= number_format($menu['harga_jual'], 0, ',', '.'); ?></td>
                    <td>
                      <a href="edit_menu.php?id=<?= $menu['id_menu']; ?>" class="btn-pesan" style="background-color:#ffc107; color:black;">Edit</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" style="text-align:center;">Belum ada menu yang ditambahkan.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="kolom-kanan">
        <div class="table-container">
          <h3>📋 Stok Bahan Baku Saat Ini</h3>
          <table>
            <thead>
              <tr>
                <th>Nama Bahan</th>
                <th>Stok</th>
                <th>Satuan</th>
                <th style="width: 25%;">Update Stok</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($result_inventory) > 0): ?>
                <?php while ($item = mysqli_fetch_assoc($result_inventory)): ?>
                  <tr>
                    <td><?= htmlspecialchars($item['nama_bahan']); ?></td>
                    <td><?= rtrim(rtrim(number_format($item['stok_saat_ini'], 2, ',', '.'), '0'), ','); ?></td>
                    <td><?= htmlspecialchars($item['satuan']); ?></td>
                    <td>
                      <form action="proses_update_stok.php" method="POST" class="form-stok">
                        <input type="hidden" name="id_inventory" value="<?= $item['id_inventory']; ?>">
                        <input type="number" name="stok_baru" step="0.01" class="input-stok" placeholder="Isi stok.." required>
                        <button type="submit" class="btn-update-stok">Update</button>
                      </form>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" style="text-align:center;">Belum ada bahan baku di inventory.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>
</body>

</html>