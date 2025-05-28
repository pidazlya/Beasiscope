<?php
require 'db.php';

// Ambil data untuk dropdown
$jenjang_pendidikan = $conn->query("SELECT id, nama FROM jenjang_pendidikan WHERE status = 'aktif'");
$jenis_beasiswa = $conn->query("SELECT id, nama FROM jenis_beasiswa WHERE status = 'aktif'");
$jenis_pendanaan = $conn->query("SELECT id, nama FROM jenis_pendanaan WHERE status = 'aktif'");

// Tangani form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jenjang_dipilih = $_POST['jenjang'] ?? '';
    $jenis_dipilih = $_POST['jenis'] ?? '';
    $pendanaan_dipilih = $_POST['pendanaan'] ?? '';

    // Ambil data beasiswa yang sesuai dengan pilihan
    $query = "
        SELECT * FROM beasiswa 
        WHERE jenjang_pendidikan_id = $jenjang_dipilih
        AND jenis_beasiswa_id = $jenis_dipilih
        AND jenis_pendanaan_id = $pendanaan_dipilih
    ";
    $result_beasiswa = $conn->query($query);

    // Ambil data yang dipilih
    $jenjang_nama = $conn->query("SELECT nama FROM jenjang_pendidikan WHERE id = $jenjang_dipilih")->fetch_assoc()['nama'] ?? 'N/A';
    $jenis_nama = $conn->query("SELECT nama FROM jenis_beasiswa WHERE id = $jenis_dipilih")->fetch_assoc()['nama'] ?? 'N/A';
    $pendanaan_nama = $conn->query("SELECT nama FROM jenis_pendanaan WHERE id = $pendanaan_dipilih")->fetch_assoc()['nama'] ?? 'N/A';
}
?>
<?php include('sideatas.php'); ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beasiscope - Pilih Beasiswa</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/Pilih_Beasiswa.css">
</head>
<body>
    <!-- Main Content -->
    <div class="container mx-auto mt-10">
        <div class="form-container">
            <h2 class="text-2xl font-bold mb-6">Form Pemilihan Beasiswa</h2>
            <form action="" method="POST" class="space-y-4">
                <!-- Dropdown Jenjang Pendidikan -->
                <div class="form-group">
                    <label for="jenjang" class="block text-gray-700 font-medium mb-2">Jenjang Pendidikan:</label>
                    <select name="jenjang" id="jenjang" class="border rounded p-2 w-full" required>
                        <option value="">Pilih Jenjang Pendidikan</option>
                        <?php while ($row = $jenjang_pendidikan->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>" 
                                <?php echo (isset($jenjang_dipilih) && $jenjang_dipilih == $row['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Dropdown Jenis Beasiswa -->
                <div class="form-group">
                    <label for="jenis" class="block text-gray-700 font-medium mb-2">Jenis Beasiswa:</label>
                    <select name="jenis" id="jenis" class="border rounded p-2 w-full" required>
                        <option value="">Pilih Jenis Beasiswa</option>
                        <?php while ($row = $jenis_beasiswa->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>" 
                                <?php echo (isset($jenis_dipilih) && $jenis_dipilih == $row['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Dropdown Jenis Pendanaan -->
                <div class="form-group">
                    <label for="pendanaan" class="block text-gray-700 font-medium mb-2">Jenis Pendanaan:</label>
                    <select name="pendanaan" id="pendanaan" class="border rounded p-2 w-full" required>
                        <option value="">Pilih Jenis Pendanaan</option>
                        <?php while ($row = $jenis_pendanaan->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>" 
                                <?php echo (isset($pendanaan_dipilih) && $pendanaan_dipilih == $row['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">
                    <i class="fas fa-search"></i> Cari Beasiswa
                </button>
            </form>

            <!-- Hasil Pencarian -->
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $jenjang_dipilih && $jenis_dipilih && $pendanaan_dipilih): ?>
                <div class="mt-6 p-4 border rounded bg-gray-100">
                    <h3 class="text-lg font-semibold mb-3"><i class="fas fa-check-circle"></i> Hasil Pencarian Beasiswa:</h3>
                    <p><strong>Jenjang Pendidikan:</strong> <?php echo htmlspecialchars($jenjang_nama); ?></p>
                    <p><strong>Jenis Beasiswa:</strong> <?php echo htmlspecialchars($jenis_nama); ?></p>
                    <p><strong>Jenis Pendanaan:</strong> <?php echo htmlspecialchars($pendanaan_nama); ?></p>

                    <?php if ($result_beasiswa->num_rows > 0): ?>
                        <table class="table-auto w-full mt-4">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">Nama Beasiswa</th>
                                    <th class="px-4 py-2">Penyelenggara</th>
                                    <th class="px-4 py-2">Tenggat</th>
                                    <th class="px-4 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result_beasiswa->fetch_assoc()): ?>
                                    <tr>
                                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['nama']); ?></td>
                                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['penyelenggara']); ?></td>
                                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['tenggat']); ?></td>
                                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['status']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="mt-4 text-gray-700">Tidak ada beasiswa yang ditemukan dengan kriteria tersebut.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php include('footer.php'); ?>

    
