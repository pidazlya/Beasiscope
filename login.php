<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    // Ambil data pengguna berdasarkan email
    $sql = "SELECT * FROM pengguna WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id']; // Simpan sesi pengguna
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email']; // Simpan email pengguna

            echo json_encode(['success' => true, 'message' => 'Login berhasil']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Password salah']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email tidak ditemukan']);
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="./assets/css/login.css" rel="stylesheet">
</head>
<body>
    <div class="modal">
        <div class="modal-content">
        <span class="close-btn">&times;</span>
            <h2>Masuk</h2>

            <div class="error-message" id="errorMessage"></div>

            <form id="loginForm">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Masukkan email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn-submit">Masuk</button>
            </form>
            <p class="small-text">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </p>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const errorMessage = document.getElementById('errorMessage');
            const formData = new FormData(this);

            // Tambahkan log untuk debugging
            console.log("Mengirim data:", Object.fromEntries(formData));

            fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Respons diterima:", data); // Debug respons
                if (data.success) {
                    errorMessage.style.backgroundColor = '#c6f6d5';
                    errorMessage.style.borderColor = '#68d391';
                    errorMessage.style.color = '#2f855a';
                    errorMessage.textContent = data.message;
                    errorMessage.style.display = 'block';

                    setTimeout(() => {
                        window.location.href = 'profile.php'; // Ganti dengan halaman yang sesuai
                    }, 2000);
                } else {
                    errorMessage.style.backgroundColor = '#fed7d7';
                    errorMessage.style.borderColor = '#f56565';
                    errorMessage.style.color = '#c53030';
                    errorMessage.textContent = data.message;
                    errorMessage.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorMessage.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
                errorMessage.style.display = 'block';
            });
        });

        // Event listener untuk tombol close
        document.querySelector('.close-btn').addEventListener('click', function() {
            window.location.href="index.php";
        });
    </script>
</body>
</html>
