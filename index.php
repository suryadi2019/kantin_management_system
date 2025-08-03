<?php
// File: index.php

// 1. Memanggil file koneksi database
require_once 'db_connect.php';

// 2. Menulis query SQL untuk mengambil semua data dari tabel menu
$sql = "SELECT nama_menu, kategori, harga_jual FROM menu ORDER BY kategori, nama_menu";

// 3. Menjalankan query dan menyimpan hasilnya
$result = mysqli_query($conn, $sql);
?>
<?php include 'navbar.php'; ?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu Kantin RRI Banjarmasin</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <header>
    <h1>🍽️ Menu Kantin RRI Banjarmasin</h1>
    <p>Silakan pilih menu favorit Anda</p>
  </header>

  <main>
    <table>
      <thead>
        <tr>
          <th>Nama Menu</th>
          <th>Kategori</th>
          <th>Harga</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Query diubah untuk mengambil id_menu
        $sql = "SELECT id_menu, nama_menu, kategori, harga_jual FROM menu ORDER BY kategori, nama_menu";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>"; // Hapus class 'menu-item' agar tidak memicu alert lama
            echo "<td>" . htmlspecialchars($row["nama_menu"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["kategori"]) . "</td>";
            echo "<td>Rp " . number_format($row["harga_jual"], 0, ',', '.') . "</td>";
            // Tombol untuk menambahkan ke keranjang
            echo "<td><a href='tambah_keranjang.php?id=" . $row["id_menu"] . "' class='btn-pesan'>+ Pesan</a></td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='4'>Belum ada menu yang tersedia.</td></tr>";
        }
        mysqli_close($conn);
        ?>
      </tbody>
    </table>
  </main>

  <footer>
    <p>&copy; 2025 Developed by 20230100226</p>
  </footer>

  <script>
    // Menambahkan event listener ke setiap baris tabel dengan class 'menu-item'
    document.querySelectorAll('.menu-item').forEach(item => {
      item.addEventListener('click', event => {
        // Mengambil nama menu dari sel pertama (index 0) di baris yang diklik
        const menuName = item.cells[0].textContent;
        alert("Anda memilih: " + menuName);
      });
    });
  </script>

</body>

</html>
