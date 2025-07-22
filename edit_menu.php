<?php
include 'navbar.php';
require_once 'db_connect.php';

// Proteksi Halaman: Hanya untuk 'dapur' dan 'administrator'
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['dapur', 'administrator'])) {
  die("<div style='text-align:center; margin-top:50px;'><h1>Akses Ditolak!</h1><p>Halaman ini hanya untuk Staf Dapur atau Administrator.</p><a href='index.php'>Kembali</a></div>");
}

// Pastikan ID menu ada di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header('Location: inventory.php');
  exit();
}
$id_menu = $_GET['id'];
$pesan = '';

// --- LOGIKA UNTUK MEMPROSES FORM UPDATE (METHOD POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_menu = $_POST['nama_menu'];
  $harga_jual = $_POST['harga_jual'];
  $kategori = $_POST['kategori'];
  $bahan = $_POST['bahan'] ?? [];

  mysqli_begin_transaction($conn);
  try {
    // 1. Update data utama di tabel 'menu'
    $sql_update_menu = "UPDATE menu SET nama_menu = ?, harga_jual = ?, kategori = ? WHERE id_menu = ?";
    $stmt_menu = mysqli_prepare($conn, $sql_update_menu);
    mysqli_stmt_bind_param($stmt_menu, 'sdsi', $nama_menu, $harga_jual, $kategori, $id_menu);
    mysqli_stmt_execute($stmt_menu);

    // 2. Hapus resep lama dari tabel 'recipe'
    $sql_delete_recipe = "DELETE FROM recipe WHERE id_menu = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete_recipe);
    mysqli_stmt_bind_param($stmt_delete, 'i', $id_menu);
    mysqli_stmt_execute($stmt_delete);

    // 3. Masukkan resep baru ke tabel 'recipe'
    $sql_insert_recipe = "INSERT INTO recipe (id_menu, id_inventory, jumlah_bahan) VALUES (?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert_recipe);
    foreach ($bahan as $item) {
      if (!empty($item['id_inventory']) && is_numeric($item['jumlah']) && $item['jumlah'] > 0) {
        mysqli_stmt_bind_param($stmt_insert, 'iid', $id_menu, $item['id_inventory'], $item['jumlah']);
        mysqli_stmt_execute($stmt_insert);
      }
    }

    mysqli_commit($conn);
    $pesan = "<p style='color:green; text-align:center;'>Menu berhasil diperbarui!</p>";
  } catch (Exception $e) {
    mysqli_rollback($conn);
    $pesan = "<p style='color:red; text-align:center;'>Gagal memperbarui menu: " . $e->getMessage() . "</p>";
  }
}

// --- LOGIKA UNTUK MENGAMBIL DATA AWAL (METHOD GET) ---
// Ambil data menu
$sql_menu = "SELECT * FROM menu WHERE id_menu = ?";
$stmt_menu = mysqli_prepare($conn, $sql_menu);
mysqli_stmt_bind_param($stmt_menu, 'i', $id_menu);
mysqli_stmt_execute($stmt_menu);
$menu = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_menu));
if (!$menu) { // Jika menu tidak ditemukan
  header('Location: inventory.php');
  exit();
}

// Ambil data resep yang sudah ada
$sql_recipe = "SELECT r.id_inventory, r.jumlah_bahan, i.nama_bahan, i.satuan FROM recipe r JOIN inventory i ON r.id_inventory = i.id_inventory WHERE r.id_menu = ?";
$stmt_recipe = mysqli_prepare($conn, $sql_recipe);
mysqli_stmt_bind_param($stmt_recipe, 'i', $id_menu);
mysqli_stmt_execute($stmt_recipe);
$resep_lama = mysqli_fetch_all(mysqli_stmt_get_result($stmt_recipe), MYSQLI_ASSOC);

// Ambil semua bahan baku dari inventory untuk dropdown
$semua_bahan = mysqli_fetch_all(mysqli_query($conn, "SELECT id_inventory, nama_bahan, satuan FROM inventory ORDER BY nama_bahan ASC"), MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Edit Menu: <?= htmlspecialchars($menu['nama_menu']); ?></title>
  <link rel="stylesheet" href="style.css">
  <style>
    .recipe-row {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 10px;
    }

    .recipe-row select,
    .recipe-row input {
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .recipe-row input[type="number"] {
      width: 100px;
    }

    .btn-remove {
      background-color: #dc3545;
      color: white;
      border: none;
      padding: 8px 12px;
      border-radius: 4px;
      cursor: pointer;
    }
  </style>
</head>

<body>
  <header>
    <h1>✏️ Edit Menu: <?= htmlspecialchars($menu['nama_menu']); ?></h1>
  </header>
  <main>
    <?= $pesan ?>
    <form action="edit_menu.php?id=<?= $id_menu; ?>" method="POST">
      <h3>Informasi Dasar Menu</h3>
      <div class="form-group">
        <label for="nama_menu">Nama Menu</label>
        <input type="text" id="nama_menu" name="nama_menu" value="<?= htmlspecialchars($menu['nama_menu']); ?>" required>
      </div>
      <div class="form-group">
        <label for="harga_jual">Harga Jual (Rp)</label>
        <input type="number" id="harga_jual" name="harga_jual" value="<?= $menu['harga_jual']; ?>" required>
      </div>
      <div class="form-group">
        <label for="kategori">Kategori</label>
        <select id="kategori" name="kategori" required>
          <option value="Makanan Berat" <?= $menu['kategori'] == 'Makanan Berat' ? 'selected' : ''; ?>>Makanan Berat</option>
          <option value="Makanan Ringan" <?= $menu['kategori'] == 'Makanan Ringan' ? 'selected' : ''; ?>>Makanan Ringan</option>
          <option value="Minuman" <?= $menu['kategori'] == 'Minuman' ? 'selected' : ''; ?>>Minuman</option>
        </select>
      </div>

      <h3 style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">Resep / Bahan Baku yang Digunakan</h3>
      <div id="recipe-container">
        <?php foreach ($resep_lama as $index => $item): ?>
          <div class="recipe-row">
            <select name="bahan[<?= $index ?>][id_inventory]">
              <?php foreach ($semua_bahan as $bahan_baku): ?>
                <option value="<?= $bahan_baku['id_inventory']; ?>" <?= $bahan_baku['id_inventory'] == $item['id_inventory'] ? 'selected' : ''; ?>>
                  <?= htmlspecialchars($bahan_baku['nama_bahan']); ?> (<?= htmlspecialchars($bahan_baku['satuan']); ?>)
                </option>
              <?php endforeach; ?>
            </select>
            <input type="number" name="bahan[<?= $index ?>][jumlah]" value="<?= $item['jumlah_bahan']; ?>" step="0.01" placeholder="Jumlah" required>
            <button type="button" class="btn-remove" onclick="this.parentElement.remove()">Hapus</button>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" id="add-recipe-btn" class="btn-pesan" style="background-color: #28a745; margin-top: 10px;">+ Tambah Bahan</button>

      <div style="margin-top: 30px; text-align: right;">
        <a href="inventory.php" class="btn-pesan" style="background-color:#6c757d;">Kembali</a>
        <button type="submit" class="btn-pesan">Simpan Perubahan</button>
      </div>
    </form>
  </main>

  <script>
    // Gunakan counter yang nilainya selalu bertambah untuk memastikan indeks unik
    let recipeIndex = <?= count($resep_lama); ?>;

    document.getElementById('add-recipe-btn').addEventListener('click', function() {
      recipeIndex++; // Naikkan indeks setiap kali tombol diklik

      const container = document.getElementById('recipe-container');
      const newRow = document.createElement('div');
      newRow.classList.add('recipe-row');

      // Dropdown bahan baku
      const select = document.createElement('select');
      select.name = `bahan[${recipeIndex}][id_inventory]`;
      select.required = true; // Tambahkan validasi

      const defaultOption = document.createElement('option');
      defaultOption.value = "";
      defaultOption.textContent = "-- Pilih Bahan --";
      select.appendChild(defaultOption);

      <?php foreach ($semua_bahan as $bahan_baku): ?>
        const option = document.createElement('option');
        option.value = "<?= $bahan_baku['id_inventory']; ?>";
        option.textContent = "<?= htmlspecialchars($bahan_baku['nama_bahan'] . ' (' . $bahan_baku['satuan'] . ')'); ?>";
        select.appendChild(option);
      <?php endforeach; ?>

      // Input jumlah
      const input = document.createElement('input');
      input.type = 'number';
      input.name = `bahan[${recipeIndex}][jumlah]`;
      input.placeholder = 'Jumlah';
      input.step = '0.01';
      input.required = true;

      // Tombol Hapus
      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.textContent = 'Hapus';
      removeBtn.classList.add('btn-remove');
      removeBtn.onclick = function() {
        newRow.remove();
      };

      newRow.appendChild(select);
      newRow.appendChild(input);
      newRow.appendChild(removeBtn);
      container.appendChild(newRow);
    });
  </script>

</body>

</html>