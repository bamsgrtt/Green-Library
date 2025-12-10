<?php
require_once __DIR__ . '/functions.php';
$returns = readData(RETURNS_FILE);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Pengembalian - Perpustakaan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
    
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
                            <a class="nav-link mx-lg-2" aria-current="page" href="index.php">Home</a>
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
        
<div class="container my-4 pt-5">
  <div class="card p-3 shadow-sm">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead class="table-primary">
          <tr>
            <th>ID</th>
            <th>Loan ID</th>
            <th>Tgl Kembali</th>
            <th>Denda</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($returns as $r): ?>
            <?php if (count($r) >= 4): ?>
            <tr>
              <td><?=h($r[0] ?? '')?></td>
              <td><?=h($r[1] ?? '')?></td>
              <td><?=h($r[2] ?? '')?></td>
              <td><?=rp($r[3] ?? 0)?></td>
            </tr>
            <?php endif; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
