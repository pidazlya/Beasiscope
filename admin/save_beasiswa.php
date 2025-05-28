<?php
require 'db.php';
// Process uploaded image
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
    $gambar = $_FILES['gambar']['name'];
    $gambar_tmp = $_FILES['gambar']['tmp_name'];
    $gambar_path = 'uploads/' . $gambar;

    move_uploaded_file($gambar_tmp, $gambar_path);
}

// Insert data
$judul = $_POST['judul'];
$kategori = $_POST['kategori'];
$tanggal = $_POST['tanggal'];
$penulis = $_POST['penulis'];
$konten = $_POST['konten'];

$query = "INSERT INTO informasi (judul, kategori, tanggal, penulis, konten, gambar) 
          VALUES ('$judul', '$kategori', '$tanggal', '$penulis', '$konten', '$gambar')";

if ($conn->query($query) === TRUE) {
    header("Location: ManajemenInformasi.php");
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
