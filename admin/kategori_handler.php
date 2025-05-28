<?php
require 'db.php';

// Ambil data dari permintaan POST
$action = $_POST['action'] ?? null;
$table = $_POST['table'] ?? null;
$id = $_POST['id'] ?? null;
$nama = $_POST['nama'] ?? null;
$status = $_POST['status'] ?? null;

$response = ['success' => false, 'message' => ''];

// Validasi data dasar
if (!$table || !in_array($table, ['jenjang_pendidikan', 'jenis_beasiswa', 'jenis_pendanaan'])) {
    $response['message'] = 'Tabel tidak valid.';
    echo json_encode($response);
    exit;
}

try {
    if ($action === 'create') {
        // Tambah data baru
        $query = "INSERT INTO $table (nama, status) VALUES (:nama, :status)";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['nama' => $nama, 'status' => $status]);
        $response['success'] = true;
        $response['message'] = 'Data berhasil ditambahkan.';
    } elseif ($action === 'update' && $id) {
        // Perbarui data
        $query = "UPDATE $table SET nama = :nama, status = :status WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['nama' => $nama, 'status' => $status, 'id' => $id]);
        $response['success'] = true;
        $response['message'] = 'Data berhasil diperbarui.';
    } elseif ($action === 'delete' && $id) {
        // Hapus data
        $query = "DELETE FROM $table WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        $response['success'] = true;
        $response['message'] = 'Data berhasil dihapus.';
    } else {
        $response['message'] = 'Aksi atau parameter tidak valid.';
    }
} catch (PDOException $e) {
    $response['message'] = 'Kesalahan database: ' . $e->getMessage();
}

// Kirimkan respons ke JavaScript
echo json_encode($response);
?>
