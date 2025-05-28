<?php
require 'db.php';
// Menentukan halaman aktif
$currentPage = basename($_SERVER['PHP_SELF']);

// Mengambil data beasiswa dari database
$query = "SELECT * FROM beasiswa";
$result = $conn->query($query);

$scholarships = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $scholarships[] = $row;
    }
}

// Menangani penambahan atau pengeditan beasiswa
if (isset($_POST['tambah_beasiswa']) || isset($_POST['edit_beasiswa'])) {
    $nama = $_POST['nama'];
    $penyelenggara = $_POST['penyelenggara'];
    $jenis = $_POST['jenis'];
    $tenggat = $_POST['tenggat'];
    $persyaratan = $_POST['persyaratan'];
    $status = $_POST['status'];

    if (isset($_POST['edit_beasiswa'])) {
        // Edit data
        $id = $_POST['id'];
        $update_sql = "UPDATE beasiswa 
                       SET nama = '$nama', penyelenggara = '$penyelenggara', jenis = '$jenis', 
                           tenggat = '$tenggat', persyaratan = '$persyaratan', status = '$status'
                       WHERE id = $id";

        if ($conn->query($update_sql) === TRUE) {
            header("Location: ManajemenBeasiswa.php?update=success");
            exit();
        } else {
            echo "Error: " . $update_sql . "<br>" . $conn->error;
        }
    } else {
        // Tambah data baru
        $insert_sql = "INSERT INTO beasiswa (nama, penyelenggara, jenis, tenggat, persyaratan, status, created_at)
                       VALUES ('$nama', '$penyelenggara', '$jenis', '$tenggat', '$persyaratan', '$status', NOW())";

        if ($conn->query($insert_sql) === TRUE) {
            header("Location: ManajemenBeasiswa.php?success=1");
            exit();
        } else {
            echo "Error: " . $insert_sql . "<br>" . $conn->error;
        }
    }
}

// Menangani penghapusan beasiswa
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $delete_sql = "DELETE FROM beasiswa WHERE id = $delete_id";
    if ($conn->query($delete_sql) === TRUE) {
        header("Location: ManajemenBeasiswa.php?delete=success");
        exit();
    } else {
        echo "Error: " . $delete_sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi
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
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h2>Beasiscope Admin</h2>
            </div>
            <nav>
                <ul>
                    <li><a href="Dashboard.php" class="<?= $currentPage == 'Dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
                    <li><a href="ManajemenBeasiswa.php" class="<?= $currentPage == 'ManajemenBeasiswa.php' ? 'active' : '' ?>">Manajemen Beasiswa</a></li>
                    <li><a href="KategoriBeasiswa.php" class="<?= $currentPage == 'KategoriBeasiswa.php' ? 'active' : '' ?>">Kategori Beasiswa</a></li>
                    <li><a href="ManajemenInformasi.php" class="<?= $currentPage == 'ManajemenInformasi.php' ? 'active' : '' ?>">Manajemen Informasi</a></li>
                    <li><a href="ManajemenTestimoni.php" class="<?= $currentPage == 'ManajemenTestimoni.php' ? 'active' : '' ?>">Manajemen Testimoni</a></li>
                </ul>
            </nav>
        </div>

       <!-- Main Content -->
       <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
            <div class="dashboard">
            <h1>Manajemen Beasiswa</h1>
            </div>
                <div class="user-info">
                    <span>Admin </span>
                    <a href="Logout.php">Logout</a>
                </div>
            </div>
            <!-- Dashboard Content -->
            

            <!-- Scholarship Management Content -->
            <div class="scholarship-management">
                <div class="header-actions">
                    <h1></h1>
                    <button class="add-btn" onclick="showAddModal()">+ Tambah Beasiswa</button>
                </div>

                
                <!-- Scholarships Table -->
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
                                <td><?= $scholarship['id']; ?></td>
                                <td><?= $scholarship['nama']; ?></td>
                                <td><?= $scholarship['penyelenggara']; ?></td>
                                <td><?= $scholarship['jenis']; ?></td>
                                <td><?= $scholarship['tenggat']; ?></td>
                                <td><?= $scholarship['persyaratan']; ?></td>
                                <td><span class="status-badge <?= strtolower($scholarship['status']); ?>"><?= $scholarship['status']; ?></span></td>
                                <td class="actions">
                                <button class="view-btn" onclick="viewScholarship(<?php echo $scholarship['id']; ?>)">View</button>
                                    <button class="edit-btn" onclick="editScholarship(<?= $scholarship['id']; ?>)">Edit</button>
                                    <button class="delete-btn" onclick="deleteScholarship(<?= $scholarship['id']; ?>)">Delete</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<!-- Form untuk lihat beasiswa -->
<div id="viewScholarshipModal" class="modal">
    <div class="modal-content">
        <h2>Detail Beasiswa</h2>
        <div id="view-scholarship-details">
            <!-- Scholarship details will be dynamically loaded here -->
        </div>
        <div class="form-actions">
            <button type="button" onclick="closeViewModal()">Tutup</button>
        </div>
    </div>
</div>



    <style>
    /* Adjust the modal content to scroll if it's too long */
    #scholarshipModal .modal-content {
        max-height: 80vh; /* Limit the maximum height of the modal */
        overflow-y: auto; /* Enable vertical scrolling when content overflows */
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* Make the modal background slightly transparent */
    #scholarshipModal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
    }
</style>

<!-- Modal Structure -->
<div id="scholarshipModal" class="modal">
    <div class="modal-content">
        <h2 id="modal-title">Tambah Beasiswa Baru</h2>
        <form id="scholarshipForm" method="POST" action="ManajemenBeasiswa.php">
            <input type="hidden" name="edit_beasiswa" value="0" id="is-edit">
            <input type="hidden" name="id" value="" id="beasiswa-id">

            <div class="form-group">
                <label for="nama">Nama Beasiswa</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            <div class="form-group">
                <label for="penyelenggara">Penyelenggara</label>
                <input type="text" id="penyelenggara" name="penyelenggara" required>
            </div>
            <div class="form-group">
                <label for="jenis">Jenis Beasiswa</label>
                <select id="jenis" name="jenis" required>
                    <option value="Dalam Negeri">Dalam Negeri</option>
                    <option value="Luar Negeri">Luar Negeri</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tenggat">Deadline</label>
                <input type="date" id="tenggat" name="tenggat" required>
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>
            <div class="form-group">
                <label for="jenjang_pendidikan_id">Jenjang Pendidikan</label>
                <select id="jenjang_pendidikan_id" name="jenjang_pendidikan_id" required>
                    <option value="">Pilih Jenjang Pendidikan</option>
                    <!-- Jenjang Pendidikan Options Here -->
                </select>
            </div>
            <div class="form-group">
                <label for="jenis_beasiswa_id">Jenis Beasiswa</label>
                <select id="jenis_beasiswa_id" name="jenis_beasiswa_id" required>
                    <option value="">Pilih Jenis Beasiswa</option>
                    <!-- Jenis Beasiswa Options Here -->
                </select>
            </div>
            <div class="form-group">
                <label for="jenis_pendanaan_id">Jenis Pendanaan</label>
                <select id="jenis_pendanaan_id" name="jenis_pendanaan_id" required>
                    <option value="">Pilih Jenis Pendanaan</option>
                    <!-- Jenis Pendanaan Options Here -->
                </select>
            </div>
            <div class="form-group">
                <label for="persyaratan">Persyaratan</label>
                <textarea id="persyaratan" name="persyaratan" rows="4" required></textarea>
            </div>
            <div class="form-actions">
                <button type="button" onclick="closeModal()">Batal</button>
                <button type="submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
 function viewScholarship(id) {
    const scholarship = <?php echo json_encode($scholarships); ?>.find(s => s.id == id);

    if (scholarship) {
        document.getElementById('view-scholarship-details').innerHTML = `
            <p><strong>Nama Beasiswa:</strong> ${scholarship.nama}</p>
            <p><strong>Penyelenggara:</strong> ${scholarship.penyelenggara}</p>
            <p><strong>Jenis Beasiswa:</strong> ${scholarship.jenis}</p>
            <p><strong>Tenggat:</strong> ${scholarship.tenggat}</p>
            <p><strong>Persyaratan:</strong> ${scholarship.persyaratan}</p>
            <p><strong>Status:</strong> <span class="status-badge ${scholarship.status.toLowerCase()}">${scholarship.status}</span></p>
            
        `;
        document.getElementById('viewScholarshipModal').style.display = 'block';
    }
}

function closeViewModal() {
    document.getElementById('viewScholarshipModal').style.display = 'none';
}




    function showAddModal() {
        document.getElementById('modal-title').innerText = "Tambah Beasiswa Baru";
        document.getElementById('scholarshipModal').style.display = 'block';
        document.getElementById('is-edit').value = "0";
        document.getElementById('scholarshipForm').reset();
    }

    function closeModal() {
        document.getElementById('scholarshipModal').style.display = 'none';
    }

    function editScholarship(id) {
        const scholarship = <?= json_encode($scholarships); ?>.find(s => s.id == id);
        if (scholarship) {
            document.getElementById('modal-title').innerText = "Edit Beasiswa";
            document.getElementById('scholarshipModal').style.display = 'block';
            document.getElementById('is-edit').value = "1";
            document.getElementById('beasiswa-id').value = scholarship.id;
            document.getElementById('nama').value = scholarship.nama;
            document.getElementById('penyelenggara').value = scholarship.penyelenggara;
            document.getElementById('jenis').value = scholarship.jenis;
            document.getElementById('tenggat').value = scholarship.tenggat;
            document.getElementById('persyaratan').value = scholarship.persyaratan;
            document.getElementById('status').value = scholarship.status;
            document.getElementById('jenjang_pendidikan_id').value = scholarship.jenjang_pendidikan_id;
            document.getElementById('jenis_beasiswa_id').value = scholarship.jenis_beasiswa_id;
            document.getElementById('jenis_pendanaan_id').value = scholarship.jenis_pendanaan_id;
        }
    }

    function deleteScholarship(id) {
        if (confirm("Apakah Anda yakin ingin menghapus beasiswa ini?")) {
            window.location.href = ManajemenBeasiswa.php?delete_id=${id};
        }
    }
</script>

</body>
</html>