?<?php
require 'db.php';

// Mengambil data kategori dari database
function fetchCategories($category) {
    global $pdo;
    $query = "SELECT * FROM $category WHERE status = 'Aktif'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$jenjangPendidikan = fetchCategories('jenjang_pendidikan');
$jenisBeasiswa = fetchCategories('jenis_beasiswa');
$jenisPendanaan = fetchCategories('jenis_pendanaan');

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Beasiswa - Beasiscope Admin</title>
    <link rel="stylesheet" href="KategoriBeasiswa.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h2>Beasiscope Admin</h2>
            </div>
            <nav>
                <ul>
                    <li><a href="Dashboard.php"
                            class="<?= $currentPage == 'Dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
                    <li><a href="ManajemenBeasiswa.php"
                            class="<?= $currentPage == 'ManajemenBeasiswa.php' ? 'active' : '' ?>">Manajemen
                            Beasiswa</a></li>
                    <li><a href="KategoriBeasiswa.php"
                            class="<?= $currentPage == 'KategoriBeasiswa.php' ? 'active' : '' ?>">Kategori Beasiswa</a>
                    </li>
                    <li><a href="ManajemenInformasi.php"
                            class="<?= $currentPage == 'ManajemenInformasi.php' ? 'active' : '' ?>">Manajemen
                            Informasi</a></li>
                    <li><a href="ManajemenTestimoni.php"
                            class="<?= $currentPage == 'ManajemenTestimoni.php' ? 'active' : '' ?>">Manajemen
                            Testimoni</a></li>
                </ul>
            </nav>
        </div>


        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
            <div class="dashboard">
            <h1>Kategori Beasiswa</h1>
            </div>
                <div class="user-info">
                    <span>Admin </span>
                    <a href="Logout.php">Logout</a>
                </div>
            </div>
            <!-- Dashboard Content -->
            
            <!-- Category Management Content -->
            <div class="scholarship-management">
                <h1></h1>

                <!-- Jenjang Pendidikan Section -->
                <div class="category-section">
                    <div class="category-header">
                        <h2>Jenjang Pendidikan</h2>
                        <button class="add-category-btn" onclick="showAddModal('jenjang')">+ Tambah Jenjang</button>
                    </div>
                    <table class="category-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Jenjang</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jenjangPendidikan as $jenjang): ?>
                            <tr>
                                <td><?php echo $jenjang['id']; ?></td>
                                <td><?php echo $jenjang['nama']; ?></td>
                                <td><span class="status-badge <?php echo strtolower($jenjang['status']); ?>"><?php echo $jenjang['status']; ?></span></td>
                                <td class="action-buttons">
                                    <button class="edit-btn" onclick="editCategory('jenjang', <?php echo $jenjang['id']; ?>)">Edit</button>
                                    <button class="delete-btn" onclick="deleteCategory('jenjang', <?php echo $jenjang['id']; ?>)">Hapus</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Jenis Beasiswa Section -->
                <div class="category-section">
                    <div class="category-header">
                        <h2>Jenis Beasiswa</h2>
                        <button class="add-category-btn" onclick="showAddModal('jenis')">+ Tambah Jenis</button>
                    </div>
                    <table class="category-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Jenis</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jenisBeasiswa as $jenis): ?>
                            <tr>
                                <td><?php echo $jenis['id']; ?></td>
                                <td><?php echo $jenis['nama']; ?></td>
                                <td><span class="status-badge <?php echo strtolower($jenis['status']); ?>"><?php echo $jenis['status']; ?></span></td>
                                <td class="action-buttons">
                                    <button class="edit-btn" onclick="editCategory('jenis', <?php echo $jenis['id']; ?>)">Edit</button>
                                    <button class="delete-btn" onclick="deleteCategory('jenis', <?php echo $jenis['id']; ?>)">Hapus</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Jenis Pendanaan Section -->
                <div class="category-section">
                    <div class="category-header">
                        <h2>Jenis Pendanaan</h2>
                        <button class="add-category-btn" onclick="showAddModal('pendanaan')">+ Tambah Pendanaan</button>
                    </div>
                    <table class="category-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Pendanaan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jenisPendanaan as $pendanaan): ?>
                            <tr>
                                <td><?php echo $pendanaan['id']; ?></td>
                                <td><?php echo $pendanaan['nama']; ?></td>
                                <td><span class="status-badge <?php echo strtolower($pendanaan['status']); ?>"><?php echo $pendanaan['status']; ?></span></td>
                                <td class="action-buttons">
                                    <button class="edit-btn" onclick="editCategory('pendanaan', <?php echo $pendanaan['id']; ?>)">Edit</button>
                                    <button class="delete-btn" onclick="deleteCategory('pendanaan', <?php echo $pendanaan['id']; ?>)">Hapus</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Category Modal -->
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Tambah Kategori</h2>
            <form id="categoryForm">
                <div class="form-group">
                    <label for="categoryName">Nama Kategori</label>
                    <input type="text" id="categoryName" name="categoryName" required>
                </div>
                <div class="form-group">
                    <label for="categoryStatus">Status</label>
                    <select id="categoryStatus" name="categoryStatus" required>
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
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
            <p>Apakah Anda yakin ingin menghapus kategori ini?</p>
            <div class="form-actions">
                <button type="button" onclick="closeDeleteModal()">Batal</button>
                <button type="button" class="delete-confirm" onclick="confirmDelete()">Hapus</button>
            </div>
        </div>
    </div>

    <script>
        function showAddModal(type) {
            document.getElementById('categoryModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Tambah Kategori ' + type.charAt(0).toUpperCase() + type.slice(1);
            document.getElementById('categoryForm').reset();
        }

        function editCategory(type, id) {
            document.getElementById('categoryModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Edit Kategori ' + type.charAt(0).toUpperCase() + type.slice(1);
            // Fetch the current data of the category using AJAX and fill the form (implement this in your backend)
        }

        function closeModal() {
            document.getElementById('categoryModal').style.display = 'none';
        }

        function deleteCategory(type, id) {
            document.getElementById('deleteModal').style.display = 'block';
            // Store the category to be deleted (implement this in your backend)
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        function confirmDelete() {
            // Perform delete action here (implement this in your backend)
            closeDeleteModal();
        }
    </script>
    <script>
    // Show Add Modal
    function showAddModal(type) {
        document.getElementById('categoryModal').style.display = 'block';
        document.getElementById('modalTitle').textContent = 'Tambah Kategori ' + type.charAt(0).toUpperCase() + type.slice(1);
        document.getElementById('categoryForm').onsubmit = function(e) {
            e.preventDefault();
            saveCategory('create', type);
        };
    }

    // Show Edit Modal
    function editCategory(type, id) {
        fetchCategory(type, id);
        document.getElementById('categoryModal').style.display = 'block';
        document.getElementById('modalTitle').textContent = 'Edit Kategori ' + type.charAt(0).toUpperCase() + type.slice(1);
        document.getElementById('categoryForm').onsubmit = function(e) {
            e.preventDefault();
            saveCategory('update', type, id);
        };
    }

    // Save Category (Create/Update)
    function saveCategory(action, type, id = null) {
        const formData = new FormData();
        formData.append('action', action);
        formData.append('table', type === 'jenjang' ? 'jenjang_pendidikan' : (type === 'jenis' ? 'jenis_beasiswa' : 'jenis_pendanaan'));
        formData.append('nama', document.getElementById('categoryName').value);
        formData.append('status', document.getElementById('categoryStatus').value);

        if (id) formData.append('id', id);

        fetch('kategori_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Berhasil disimpan!');
                location.reload(); // Refresh page
            } else {
                alert('Gagal menyimpan data!');
            }
        });
    }

    // Delete Category
    function deleteCategory(type, id) {
        if (confirm("Apakah Anda yakin ingin menghapus kategori ini?")) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('table', type === 'jenjang' ? 'jenjang_pendidikan' : (type === 'jenis' ? 'jenis_beasiswa' : 'jenis_pendanaan'));
            formData.append('id', id);

            fetch('kategori_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Data berhasil dihapus!');
                    location.reload();
                } else {
                    alert('Gagal menghapus data!');
                }
            });
        }
    }

    // Fetch Category Data (For Edit)
    function fetchCategory(type, id) {
        // Implement fetching existing category data using AJAX if needed
    }

    // Close Modal
    function closeModal() {
        document.getElementById('categoryModal').style.display = 'none';
    }
</script>

</body>
</html>
