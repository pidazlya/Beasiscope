<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['username'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $created_at = date("Y-m-d H:i:s");
    
    // Check if email already exists
    $check_email = "SELECT * FROM pengguna WHERE email = ?";
    $check_stmt = $conn->prepare($check_email);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email sudah terdaftar']);
        exit();
    }
    
    // Insert data into pengguna table
    $sql = "INSERT INTO pengguna (nama, email, password, created_at) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nama, $email, $pass, $created_at);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Registrasi berhasil']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal melakukan registrasi']);
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
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="./assets/css/register.css" rel="stylesheet">
</head>
<body>
    <div class="modal" id="registerModal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Daftar Akun Baru</h2>
            
            <div class="error-message" id="errorMessage"></div>
            
            <form id="registerForm" method="POST">
                <div class="form-group">
                    <label for="username">Nama Lengkap:</label>
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Masukkan nama lengkap" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Masukkan email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Konfirmasi Password:</label>
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Ulangi password" required>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i> Daftar
                </button>
            </form>
            
            <p class="small-text">
                Sudah punya akun? <a href="login.php" id="loginLink">Masuk di sini</a>
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('registerModal');
            const closeBtn = document.querySelector('.close-btn');
            const registerForm = document.getElementById('registerForm');
            const errorMessage = document.getElementById('errorMessage');

            // Show modal
            modal.style.display = 'flex';

            // Close modal when clicking close button
            closeBtn.onclick = function() {
                window.location.href="index.php";
            }
            
            // Form validation and submission
            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirmPassword').value;

                if (password !== confirmPassword) {
                    errorMessage.textContent = 'Password tidak cocok';
                    errorMessage.style.display = 'block';
                    return;
                }

                if (password.length < 8) {
                    errorMessage.textContent = 'Password harus minimal 8 karakter';
                    errorMessage.style.display = 'block';
                    return;
                }
                
                const formData = new FormData(this);
                
                fetch('register.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        errorMessage.style.backgroundColor = '#c6f6d5';
                        errorMessage.style.borderColor = '#68d391';
                        errorMessage.style.color = '#2f855a';
                        errorMessage.textContent = data.message;
                        errorMessage.style.display = 'block';
                        
                        // Redirect to login after successful registration
                        setTimeout(() => {
                            window.location.href = 'login.php';
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
        });
    </script>
</body>
</html>