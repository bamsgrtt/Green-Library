<?php
include "functions.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kembalikan_buku'])) {
    $judul = sanitize_input($_POST['judul']);
    $nama = sanitize_input($_POST['nama_anggota']);
    $tanggal = date("Y-m-d");
    kembalikan_buku($judul, $nama, $tanggal);
    header("Location: pengembalian.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container-fluid">
    <nav class="navbar navbar-expand-lg fixed-top bg-dark" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand me-auto" href="beranda.php">Green Library</a>
           
            <div class="offcanvas offcanvas-end bg-dark" tabindex="-1" id="offcanvasNavbar"
                aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title text-white" id="offcanvasNavbarLabel">Green Library</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
                        <li class="nav-item">
                            <a class="nav-link mx-lg-2" href="beranda.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-lg-2" href="buku.php">Buku</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-lg-2" href="anggota.php">Anggota</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-lg-2" href="peminjaman.php">Peminjaman</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-lg-2 active" aria-current="page" href="pengembalian.php">Pengembalian</a>
                        </li>
                    </ul>
                </div>
            </div>
            <a href="login.php" class="login-button">Login</a>
             <button class="navbar-toggler pe-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>
    </div>

    <div class="container mt-5 pt-5">
        <h1>Pengembalian Buku</h1>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white">Form Pengembalian</div>
            <div class="card-body">
                <form method="post" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="judul" class="form-label">Judul Buku</label>
                        <input type="text" name="judul" id="judul" class="form-control" placeholder="Judul Buku" required>
                    </div>
                    <div class="col-md-5">
                        <label for="nama_anggota" class="form-label">Nama Anggota</label>
                        <input type="text" name="nama_anggota" id="nama_anggota" class="form-control" placeholder="Nama Anggota" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="kembalikan_buku" class="btn btn-secondary w-100">Kembalikan</button>
                    </div>
                </form>
            </div>
        </div>

        <h2>Riwayat Pengembalian</h2>
        <pre><?= implode("\n", read_file("data/pengembalian.txt")) ?></pre>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>