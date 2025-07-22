<?php
// Ganti 'admin123' dengan password apa pun yang Anda inginkan
$passwordAsli = 'fnO*EIDYN@#FINR#(*FNY*FYI(awyf';

// PHP akan membuat hash yang aman
$hash = password_hash($passwordAsli, PASSWORD_DEFAULT);

echo "Gunakan hash di bawah ini untuk disimpan di kolom 'password' pada database Anda:<br><br>";
echo "<strong>" . $hash . "</strong>";
