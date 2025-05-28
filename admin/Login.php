<?php
require 'db.php';

// Jika form login disubmit
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Cek apakah username dan password ada di database
    $query = "SELECT * FROM dblogin_admin WHERE username='$username'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        // Ambil data pengguna
        $user = mysqli_fetch_assoc($result);
        
        // Cek apakah password yang dimasukkan cocok
        if ($password == $user['password']) {
            $_SESSION['admin'] = $user['username'];  // Menyimpan username dalam session
            header("Location: dashboard.php");  // Redirect ke dashboard
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }

        .login-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 300px;
        }

        .login-title {
            color: #2494a8;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2494a8;
        }

        .login-button {
            width: 100%;
            padding: 0.75rem;
            background-color: #2494a8;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        .login-button:hover {
            background-color: #1d7a8a;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">Login</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login" class="login-button">Login</button>
            <?php if (isset($error)) { ?>
                <p style="color: red; text-align: center;"><?php echo $error; ?></p>
            <?php } ?>
        </form>
    </div>
</body>
</html>
