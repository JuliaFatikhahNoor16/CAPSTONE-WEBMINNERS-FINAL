<?php
// Hash password
$password = 'admin gaol';  // Ganti dengan password yang kamu inginkan
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Tampilkan hash password
echo $hashed_password;
?>
