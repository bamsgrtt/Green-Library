<?php
require_once __DIR__ . '/functions.php';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Perpustakaan - Menu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    /* CSS Kustom di sini */
        .hero-section {
            height: 80vh; 
            background: url('assets/img/hero.jpg') no-repeat center center/cover;
            color: white; 
            display: flex;
            align-items: center; 
            position: relative;
        }

        .hero-section::before {
            /* Overlay gelap untuk membuat teks lebih mudah dibaca */
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 55, 0, 0.1); /* Opasitas 40% */
            z-index: 1;
        }

        .hero-content {
            z-index: 2; /* Pastikan konten di atas overlay */
            text-align: center;
            background: transparent;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: bold;
        }

        /* Styling untuk Search Box */
        .search-container {
            max-width: 600px;
            margin: 20px auto;
        }

        .hero-title .span {
            color: #00ddffff;
        }
  </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand me-auto" href="index.php">Green - Library</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"         aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">G-Library</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
                    <li class="nav-item">
                        <a class="nav-link mx-lg-2 active" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-lg-2" href="buku.php">Buku</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-lg-2" href="anggota.php">Anggota</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-lg-2" href="Peminjaman.php">Peminjaman</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-lg-2" href="Pengembalian.php">Pengembalian</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Header -->
     <header class="hero-section">
        <div class="container hero-content">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h1 class="hero-title">Welcome to <span class="span">Green - Library</span></h1>
                    <h2 class="h3 mb-4">Kamu dapat menemukan buku apapun.</h2>

                    <div class="search-container">
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" placeholder="Search with keyword, title, author..." aria-label="Search box">
                            <button class="btn btn-light" type="button" id="button-addon2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.268.351.529.685.789 1.002a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0-.708-.708l-3 3-.001-.001a6.471 6.471 0 0 0 1.002-.789zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </header>
<!-- Content -->


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
