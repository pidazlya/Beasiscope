<?php
require 'db.php';
// Fungsi untuk mencegah SQL injection
function sanitize($conn, $str) {
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($str)));
}

// Fungsi menangani operasi CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $nama = sanitize($conn, $_POST['nama']);
    $penyelenggara = sanitize($conn, $_POST['penyelenggara']);
    $jenis = sanitize($conn, $_POST['jenis']);
    $tenggat = sanitize($conn, $_POST['tenggat']);
    $persyaratan = sanitize($conn, $_POST['persyaratan']);
    $status = sanitize($conn, $_POST['status']);
    $jenjang_pendidikan_id = (int)$_POST['jenjang_pendidikan_id'];
    $jenis_beasiswa_id = (int)$_POST['jenis_beasiswa_id'];
    $jenis_pendanaan_id = (int)$_POST['jenis_pendanaan_id'];

    if ($id) {
        // Update data
        $stmt = $conn->prepare("UPDATE beasiswa SET 
            nama=?, penyelenggara=?, jenis=?, tenggat=?, 
            persyaratan=?, status=?, jenjang_pendidikan_id=?,
            jenis_beasiswa_id=?, jenis_pendanaan_id=?
            WHERE id=?");
        $stmt->bind_param('ssssssiiii', 
            $nama, $penyelenggara, $jenis, $tenggat, 
            $persyaratan, $status, $jenjang_pendidikan_id,
            $jenis_beasiswa_id, $jenis_pendanaan_id, $id);
        
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // Insert data baru
        $stmt = $conn->prepare("INSERT INTO beasiswa 
            (nama, penyelenggara, jenis, tenggat, persyaratan, status, 
            jenjang_pendidikan_id, jenis_beasiswa_id, jenis_pendanaan_id, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param('sssssssss', 
            $nama, $penyelenggara, $jenis, $tenggat, 
            $persyaratan, $status, $jenjang_pendidikan_id,
            $jenis_beasiswa_id, $jenis_pendanaan_id);
        
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $delete_id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM beasiswa WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Mengambil data beasiswa
$query = "SELECT * FROM beasiswa ORDER BY id DESC";
$result = $conn->query($query);
$scholarships = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $scholarships[] = $row;
    }
}

// Mengambil data referensi
$queries = [
    'jenjang' => "SELECT * FROM jenjang_pendidikan",
    'jenis_beasiswa' => "SELECT * FROM jenis_beasiswa",
    'jenis_pendanaan' => "SELECT * FROM jenis_pendanaan"
];

$options = [];
foreach ($queries as $key => $query) {
    $result = $conn->query($query);
    $options[$key] = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $options[$key][] = $row;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Beasiswa - Beasiscope Admin</title>
    <link rel="stylesheet" href="ManajemenBeasiswa.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <h2>Beasiscope Admin</h2>
            </div>
            <nav>
                <ul>
                    <li><a href="Dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'Dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
                    <li><a href="#" class="active">Manajemen Beasiswa</a></li>
                    <li><a href="KategoriBeasiswa.php" class="<?= basename($_SERVER['PHP_SELF']) == 'KategoriBeasiswa.php' ? 'active' : '' ?>">Kategori Beasiswa</a></li>
                    <li><a href="ManajemenInformasi.php" class="<?= basename($_SERVER['PHP_SELF']) == 'ManajemenInformasi.php' ? 'active' : '' ?>">Manajemen Informasi</a></li>
                    <li><a href="ManajemenTestimoni.php" class="<?= basename($_SERVER['PHP_SELF']) == 'ManajemenTestimoni.php' ? 'active' : '' ?>">Manajemen Testimoni</a></li>
                </ul>
            </nav>
        </div>

        <div class="main-content">
            <div class="top-bar">
                <div class="dashboard">
                    <h1>Manajemen Beasiswa</h1>
                </div>
                <div class="user-info">
                    <span>Admin </span>
                    <a href="Logout.php">Logout</a>
                </div>
            </div>

            <div class="scholarship-management">
                <div class="header-actions">
                    <button class="add-btn" onclick="showAddModal()">+ Tambah Beasiswa</button>
                </div>

                <div class="scholarship-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Beasiswa</th>
                                <th>Penyelenggara</th>
                                <th>Jenis</th>
                                <th>Deadline</th>
                                <th>Persyaratan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($scholarships as $scholarship): ?>
                            <tr>
                                <td><?php echo $scholarship['id']; ?></td>
                                <td><?php echo $scholarship['nama']; ?></td>
                                <td><?php echo $scholarship['penyelenggara']; ?></td>
                                <td><?php echo $scholarship['jenis']; ?></td>
                                <td><?php echo $scholarship['tenggat']; ?></td>
                                <td><?php echo $scholarship['persyaratan']; ?></td>
                                <td><span class="status-badge <?php echo strtolower($scholarship['status']); ?>"><?php echo $scholarship['status']; ?></span></td>
                                <td class="actions">
                                    <button class="view-btn" onclick="viewScholarship(<?php echo $scholarship['id']; ?>)">View</button>
                                    <button class="edit-btn" onclick="editScholarship(<?php echo $scholarship['id']; ?>)">Edit</button>
                                    <button class="delete-btn" onclick="if(confirm('Hapus beasiswa ini?')) { window.location.href='?action=delete&id=<?php echo $scholarship['id']; ?>'; }">Hapus</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- View Scholarship Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <h2>Detail Beasiswa</h2>
            <div id="scholarshipDetails"></div>
            <div class="form-actions">
                <button type="button" onclick="closeViewModal()">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Add/Edit Scholarship Modal -->
    <div id="scholarshipModal" class="modal">
        <div class="modal-content">
            <h2 id="modal-title">Tambah/Edit Beasiswa</h2>
            <form id="scholarshipForm" method="POST">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-group">
                    <label for="nama">Nama Beasiswa</label>
                    <input type="text" id="nama" name="nama" required>
                </div>
                <div class="form-group">
                    <label for="penyelenggara">Penyelenggara</label>
                    <input type="text" id="penyelenggara" name="penyelenggara" required>
                </div>
                <div class="form-group">
                    <label for="jenis">Jenis</label>
                    <input type="text" id="jenis" name="jenis" required>
                </div>
                <div class="form-group">
                    <label for="tenggat">Tenggat Waktu</label>
                    <input type="date" id="tenggat" name="tenggat" required>
                </div>
                <div class="form-group">
                    <label for="persyaratan">Persyaratan</label>
                    <textarea id="persyaratan" name="persyaratan" required></textarea>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="jenjang_pendidikan_id">Jenjang Pendidikan</label>
                    <select id="jenjang_pendidikan_id" name="jenjang_pendidikan_id" required>
                        <option value="">Pilih Jenjang Pendidikan</option>
                        <?php foreach ($options['jenjang'] as $jenjang): ?>
                            <option value="<?php echo $jenjang['id']; ?>"><?php echo $jenjang['nama']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="jenis_beasiswa_id">Jenis Beasiswa</label>
                    <select id="jenis_beasiswa_id" name="jenis_beasiswa_id" required>
                        <option value="">Pilih Jenis Beasiswa</option>
                        <?php foreach ($options['jenis_beasiswa'] as $jenis): ?>
                            <option value="<?php echo $jenis['id']; ?>"><?php echo $jenis['nama']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="jenis_pendanaan_id">Jenis Pendanaan</label>
                    <select id="jenis_pendanaan_id" name="jenis_pendanaan_id" required>
                        <option value="">Pilih Jenis Pendanaan</option>
                        <?php foreach ($options['jenis_pendanaan'] as $pendanaan): ?>
                            <option value="<?php echo $pendanaan['id']; ?>"><?php echo $pendanaan['nama']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="closeModal()">Batal</button>
                    <button type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    /// Function to decode HTML entities
function decodeHTML(html) {
    const txt = document.createElement('textarea');
    txt.innerHTML = html;
    return txt.value;
}

// Show Add Modal
function showAddModal() {
    resetForm();
    document.getElementById('modal-title').textContent = 'Tambah Beasiswa Baru';
    document.getElementById('scholarshipModal').style.display = 'block';
}

// Reset Form
function resetForm() {
    document.getElementById('scholarshipForm').reset();
    document.getElementById('edit_id').value = '';
}

// View Scholarship
function viewScholarship(id) {
    const scholarships = JSON.parse(document.getElementById('scholarshipData').value);
    const scholarship = scholarships.find(s => s.id == id);
    
    if (scholarship) {
        let detail = `
            <strong>Nama Beasiswa:</strong> ${decodeHTML(scholarship.nama)}<br>
            <strong>Penyelenggara:</strong> ${decodeHTML(scholarship.penyelenggara)}<br>
            <strong>Jenis:</strong> ${decodeHTML(scholarship.jenis)}<br>
            <strong>Tenggat:</strong> ${scholarship.tenggat}<br>
            <strong>Status:</strong> ${decodeHTML(scholarship.status)}<br>
            <strong>Persyaratan:</strong><br>${decodeHTML(scholarship.persyaratan).replace(/\n/g, '<br>')}
        `;
        document.getElementById('scholarshipDetails').innerHTML = detail;
        document.getElementById('viewModal').style.display = 'block';
    }
}

// Edit Scholarship
function editScholarship(id) {
    const scholarships = JSON.parse(document.getElementById('scholarshipData').value);
    const scholarship = scholarships.find(s => s.id == id);
    
    if (scholarship) {
        document.getElementById('edit_id').value = scholarship.id;
        document.getElementById('nama').value = decodeHTML(scholarship.nama);
        document.getElementById('penyelenggara').value = decodeHTML(scholarship.penyelenggara);
        document.getElementById('jenis').value = decodeHTML(scholarship.jenis);
        document.getElementById('tenggat').value = scholarship.tenggat;
        document.getElementById('persyaratan').value = decodeHTML(scholarship.persyaratan);
        document.getElementById('status').value = scholarship.status;
        document.getElementById('jenjang_pendidikan_id').value = scholarship.jenjang_pendidikan_id;
        document.getElementById('jenis_beasiswa_id').value = scholarship.jenis_beasiswa_id;
        document.getElementById('jenis_pendanaan_id').value = scholarship.jenis_pendanaan_id;

        document.getElementById('modal-title').textContent = 'Edit Beasiswa';
        document.getElementById('scholarshipModal').style.display = 'block';
    }
}

// Close modals
function closeModal() {
    document.getElementById('scholarshipModal').style.display = 'none';
    resetForm();
}

function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

// Add event listeners for closing modals when clicking outside
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
        if (event.target.id === 'scholarshipModal') {
            resetForm();
        }
    }
}
    </script>
    <input type="hidden" id="scholarshipData" value='<?php echo json_encode($scholarships); ?>'>
</body>
</html>