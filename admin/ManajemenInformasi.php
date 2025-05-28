<?php
require 'db.php';

// Fungsi untuk mencegah SQL injection
function sanitize($conn, $str) {
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($str)));
}

// Fungsi menangani operasi CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $judul = sanitize($conn, $_POST['judul']);
    $kategori = sanitize($conn, $_POST['kategori']);
    $tanggal = sanitize($conn, $_POST['tanggal']);
    $penulis = sanitize($conn, $_POST['penulis']);
    $status = sanitize($conn, $_POST['status']);
    $konten = sanitize($conn, $_POST['konten']);

    // Handle upload gambar
    $gambar = null;
    if (!empty($_FILES['gambar']['name'])) {
        $gambar = sanitize($conn, $_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], "informasi/" . $gambar);
    }

    if ($id) {
        // Update data
        $query = "UPDATE informasi SET 
                  judul=?, kategori=?, tanggal=?, 
                  penulis=?, status=?, konten=?";
        $params = [$judul, $kategori, $tanggal, $penulis, $status, $konten];
        
        if ($gambar) {
            $query .= ", gambar=?";
            $params[] = $gambar;
        }
        
        $query .= " WHERE id=?";
        $params[] = $id;
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($params)-1) . 'i', ...$params);
        
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // Insert data baru
        $stmt = $conn->prepare("INSERT INTO informasi (judul, kategori, tanggal, penulis, status, gambar, konten) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssss', $judul, $kategori, $tanggal, $penulis, $status, $gambar, $konten);
        
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

// Mengambil data dari database
$query = "SELECT * FROM informasi ORDER BY id DESC";
$result = $conn->query($query);
$informasiList = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $informasiList[] = $row;
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Informasi - Beasiscope Admin</title>
    <link rel="stylesheet" href="ManajemenInformasi.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <h2>Beasiscope Admin</h2>
            </div>
            <nav>
                <ul>
                    <li><a href="Dashboard.php" class="<?= $currentPage == 'Dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
                    <li><a href="ManajemenBeasiswa.php" class="<?= $currentPage == 'ManajemenBeasiswa.php' ? 'active' : '' ?>">Manajemen Beasiswa</a></li>
                    <li><a href="KategoriBeasiswa.php" class="<?= $currentPage == 'KategoriBeasiswa.php' ? 'active' : '' ?>">Kategori Beasiswa</a></li>
                    <li><a href="#" class="active">Manajemen Informasi</a></li>
                    <li><a href="ManajemenTestimoni.php" class="<?= $currentPage == 'ManajemenTestimoni.php' ? 'active' : '' ?>">Manajemen Testimoni</a></li>
                </ul>
            </nav>
        </div>
<!-- Main Content -->
<div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
            <div class="dashboard">
            <h1>Manajemen Informasi</h1>
            </div>
                <div class="user-info">
                    <span>Admin </span>
                    <a href="Logout.php">Logout</a>
                </div>
            </div>
            <!-- Dashboard Content -->
            <div class="info-management">
                <div class="header-actions">
                    
                    <button class="add-btn" onclick="showAddModal()">+ Tambah Informasi</button>
                </div>

                <div class="info-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Penulis</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($informasiList as $info): ?>
                            <tr>
                                <td><?php echo $info['id']; ?></td>
                                <td><?php echo $info['judul']; ?></td>
                                <td><?php echo $info['kategori']; ?></td>
                                <td><?php echo $info['tanggal']; ?></td>
                                <td><?php echo $info['penulis']; ?></td>
                                <td><span class="status-badge <?php echo strtolower($info['status']); ?>"><?php echo $info['status']; ?></span></td>
                                <td class="actions">
                                    <button class="view-btn" onclick="viewInfo(<?php echo $info['id']; ?>, '<?php echo addslashes($info['judul']); ?>', '<?php echo addslashes($info['kategori']); ?>', '<?php echo $info['tanggal']; ?>', '<?php echo addslashes($info['penulis']); ?>', '<?php echo addslashes($info['status']); ?>', '<?php echo addslashes($info['konten']); ?>')">View</button>

                                    <button class="edit-btn" onclick="editInfo(<?php echo $info['id']; ?>, '<?php echo addslashes($info['judul']); ?>', '<?php echo addslashes($info['kategori']); ?>', '<?php echo $info['tanggal']; ?>', '<?php echo addslashes($info['penulis']); ?>', '<?php echo addslashes($info['status']); ?>', '<?php echo addslashes($info['konten']); ?>')">Edit</button>

                                    <button class="delete-btn" onclick="if (confirm('Hapus informasi ini?')) { window.location.href='?action=delete&id=<?= $info['id']; ?>'; }">Hapus</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- View Information Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <h2>Detail Informasi</h2>
            <p id="informasiDetails"></p>
            <div class="form-actions">
            <button type="button" class="btn btn-close" onclick="closeViewModal()" aria-label="Close">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Add/Edit Information Modal -->
    <div id="addInformasiModal" class="modal">
        <div class="modal-content">
            <h2>Tambah/Edit Informasi</h2>
            <form id="informasiForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-group">
                    <label for="judul">Judul</label>
                    <input type="text" id="judul" name="judul" required>
                </div>
                <div class="form-group">
                    <label for="kategori">Kategori</label>
                    <select id="kategori" name="kategori" required>
                        <option value="pengumuman">Pengumuman</option>
                        <option value="artikel">Artikel</option>
                        <option value="berita">Berita</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" required>
                </div>
                <div class="form-group">
                    <label for="penulis">Penulis</label>
                    <input type="text" id="penulis" name="penulis" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Terbit">Terbit</option>
                        <option value="Draf">Draf</option>
                        <option value="Arsip">Arsip</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="gambar">Gambar</label>
                    <input type="file" id="gambar" name="gambar">
                </div>
                <div class="form-group">
                    <label for="konten">Konten</label>
                    <textarea id="konten" name="konten" rows="4" required></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="closeModal()">Batal</button>
                    <button type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Konfirmasi Hapus</h2>
            <p>Apakah Anda yakin ingin menghapus informasi ini?</p>
            <div class="form-actions">
                <button type="button" onclick="closeDeleteModal()">Batal</button>
                <button type="button" onclick="confirmDelete()" class="delete-btn">Hapus</button>
            </div>
        </div>
    </div>

    <script>
    // Show Add Modal
    function showAddModal() {
        resetForm();
        document.getElementById('addInformasiModal').style.display = 'block';
    }

    // Reset Form
    function resetForm() {
        document.getElementById('informasiForm').reset();
        document.getElementById('edit_id').value = '';
    }

    // Edit Info
    function editInfo(id, judul, kategori, tanggal, penulis, status, konten) {
        // Decode HTML entities
        const decodeHTML = (html) => {
            const txt = document.createElement('textarea');
            txt.innerHTML = html;
            return txt.value;
        };

        document.getElementById('edit_id').value = id;
        document.getElementById('judul').value = decodeHTML(judul);
        document.getElementById('kategori').value = decodeHTML(kategori);
        document.getElementById('tanggal').value = tanggal;
        document.getElementById('penulis').value = decodeHTML(penulis);
        document.getElementById('status').value = decodeHTML(status);
        document.getElementById('konten').value = decodeHTML(konten);
        
        document.getElementById('addInformasiModal').style.display = 'block';
    }

    // View Info
    function viewInfo(id, judul, kategori, tanggal, penulis, status, konten) {
        // Decode HTML entities
        const decodeHTML = (html) => {
            const txt = document.createElement('textarea');
            txt.innerHTML = html;
            return txt.value;
        };

        let detail = `
            <strong>Judul:</strong> ${decodeHTML(judul)}<br>
            <strong>Kategori:</strong> ${decodeHTML(kategori)}<br>
            <strong>Tanggal:</strong> ${tanggal}<br>
            <strong>Penulis:</strong> ${decodeHTML(penulis)}<br>
            <strong>Status:</strong> ${decodeHTML(status)}<br>
            <strong>Konten:</strong><br>${decodeHTML(konten).replace(/\n/g, '<br>')}
        `;
        document.getElementById('informasiDetails').innerHTML = detail;
        document.getElementById('viewModal').style.display = 'block';
    }

    // Close modals
    function closeModal() {
        document.getElementById('addInformasiModal').style.display = 'none';
        resetForm();
    }

    function closeViewModal() {
        document.getElementById('viewModal').style.display = 'none';
    }

    // Add event listeners for closing modals when clicking outside
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = 'none';
            if (event.target.id === 'addInformasiModal') {
                resetForm();
            }
        }
    };
</script>
</html>