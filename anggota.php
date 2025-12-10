<?php
include "functions.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_anggota'])) {
    $nama = sanitize_input($_POST['nama']);
    $email = sanitize_input($_POST['email']);
    $status = sanitize_input($_POST['status']);
    if(!validate_email($email)){
        echo "Email tidak valid!"; // Harusnya redirect dengan pesan error
    } else {
        tambah_anggota($nama, $email, $status);
        header("Location: anggota.php");
    }
}
$anggota_list = get_all_anggota();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Anggota</title>
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
                            <a class="nav-link mx-lg-2 active" aria-current="page" href="anggota.php">Anggota</a>
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
    <h1>Manajemen Anggota</h1>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">Tambah Anggota Baru</div>
        <div class="card-body">
            <form method="post" class="row g-3">
                <div class="col-md-4">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama" required>
                </div>
                <div class="col-md-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" name="tambah_anggota" class="btn btn-primary w-100">Tambah Anggota</button>
                </div>
            </form>
        </div>
    </div>

    <h2>Daftar Anggota</h2>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($anggota_list as $a) { ?>
                <tr>
                    <td><?= $a['nama'] ?></td>
                    <td><?= $a['email'] ?></td>
                    <td><span class="badge bg-<?= ($a['status'] == 'aktif') ? 'success' : 'danger' ?>"><?= ucfirst($a['status']) ?></span></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>