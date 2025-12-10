<?php
include "functions.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_buku'])) {
    $judul = sanitize_input($_POST['judul_buku']);
    $kategori = sanitize_input($_POST['kategori']);
    $status = sanitize_input($_POST['status']);
    $kondisi = $_POST['kondisi'] ?? [];
    tambah_buku($judul, $kategori, $status, $kondisi);
    header("Location: buku.php");
}
$buku_list = get_all_buku();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Buku</title>
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
                            <a class="nav-link mx-lg-2 active" aria-current="page" href="buku.php">Buku</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-lg-2" href="anggota.php">Anggota</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-lg-2" href="peminjaman.php">Peminjaman</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-lg-2" href="pengembalian.php">Pengembalian</a>
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
    <h1>Manajemen Buku</h1>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">Tambah Buku Baru</div>
        <div class="card-body">
            <form method="post" class="row g-3">
                <div class="col-md-6">
                    <label for="judul_buku" class="form-label">Judul Buku</label>
                    <input type="text" name="judul_buku" id="judul_buku" class="form-control" placeholder="Judul Buku" required>
                </div>
                <div class="col-md-6">
                    <label for="kategori" class="form-label">Kategori</label>
                    <select name="kategori" id="kategori" class="form-select">
                        <option value="Teknologi">Teknologi</option>
                        <option value="Sastra">Sastra</option>
                        <option value="Ilmu Pengetahuan">Ilmu Penmuan</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label d-block">Status</label>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="status" id="status_tersedia" value="tersedia" class="form-check-input" checked> 
                        <label class="form-check-label" for="status_tersedia">Tersedia</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="status" id="status_dipinjam" value="dipinjam" class="form-check-input"> 
                        <label class="form-check-label" for="status_dipinjam">Dipinjam</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label d-block">Kondisi</label>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="kondisi[]" id="kondisi_baru" value="baru" class="form-check-input"> 
                        <label class="form-check-label" for="kondisi_baru">Baru</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="kondisi[]" id="kondisi_bekas" value="bekas" class="form-check-input"> 
                        <label class="form-check-label" for="kondisi_bekas">Bekas</label>
                    </div>
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" name="tambah_buku" class="btn btn-primary">Tambah Buku</button>
                </div>
            </form>
        </div>
    </div>

    <h2>Daftar Buku</h2>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Kondisi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($buku_list as $b) { ?>
                <tr>
                    <td><?= $b['judul'] ?></td>
                    <td><?= $b['kategori'] ?></td>
                    <td><span class="badge bg-<?= ($b['status'] == 'tersedia') ? 'success' : 'warning' ?>"><?= ucfirst($b['status']) ?></span></td>
                    <td><?= implode(", ", $b['kondisi']) ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>