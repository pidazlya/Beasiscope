<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <title>Beasiscope</title>
    <link rel="stylesheet" href="./assets/css/header.css">
</head>

<body>
    <header class="header">
        <div class="logo">
            <img src="./assets/img/LOGO_BEASISCOPE.png" alt="Logo Beasiscope" class="logo-img">
        </div>
        <div class="nav-cta-container">
            <nav class="nav">
                <ul>
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="Pilih_Beasiswa.php">Pilih Beasiswa</a></li>
                    <li><a href="Portofolio.php">Portofolio</a></li>
                    <li><a href="Artikel.php">Artikel</a></li>
                </ul>
            </nav>
            <?php if (!isset($_SESSION['user_id'])) { ?>
                <button class="signup-btn" id="openModalBtn" onclick="location.href='register.php';">
                    <i class="fas fa-sign-in-alt me-2"></i> Masuk
                </button>
            <?php } else { ?>
                <a class="signup-btn" id="profileBtn" href="profile.php">
                    <i class="fas fa-user me-2"></i> Profil
                </a>
                <a class="signout-btn" id="openModalBtn" href="logout.php">
                    <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                </a>
            <?php } ?>
            <div class="dropdown" id="dropdownContainer" style="display: none;">
                <div class="dropdown-content" id="dropdownContent">
                    <a href="#">Edit Profil</a>
                    <a href="#" id="logoutBtn">Keluar</a>
                </div>
            </div>
        </div>
    </header>
</body>

</html>