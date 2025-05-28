<?php
session_start();

// Menghapus semua sesi
session_unset();
session_destroy();

// Mengarahkan ke halaman login
header('Location: Login.php');
exit();
?>