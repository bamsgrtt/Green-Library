<?php
require_once __DIR__ . '/functions.php';
$members = readData(MEMBERS_FILE);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $tel = trim($_POST['telepon'] ?? '');
        if (strlen($nama) < 3) $errors[] = 'Nama minimal 3 karakter';
        if (!is_valid_email($email)) $errors[] = 'Email tidak valid';
        if (empty($errors)) {
            $id = nextID(MEMBERS_FILE, 'M');
            appendRow(MEMBERS_FILE, [$id, $nama, $email, $tel, 'aktif']);
            header('Location: anggota.php');
            exit;
        }
    }
    if ($action === 'edit') {
        $id = $_POST['id'] ?? '';
        foreach ($members as &$m) {
            if ($m[0] === $id) {
                $m[1] = trim($_POST['nama'] ?? $m[1]);
                $email = trim($_POST['email'] ?? $m[2]);
                if (is_valid_email($email)) $m[2] = $email;
                $m[3] = trim($_POST['telepon'] ?? $m[3]);
                $m[4] = trim($_POST['status'] ?? $m[4]);
                break;
            }
        }
        writeTable(MEMBERS_FILE, $members);
        header('Location: anggota.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Anggota - Perpustakaan</title>
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
    
    <!-- Daftar Anggota -->
<div class="container my-4 pt-5">
    <div class="card mb-4 p-3">
        <h3>Daftar Anggota</h3>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-dark">
                    <tr><th>ID</th><th>Nama</th><th>Email</th><th>Telp</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <?php foreach ($members as $m): ?>
            <tr>
              <td><?=h($m[0])?></td>
              <td><?=h($m[1])?></td>
              <td><?=h($m[2])?></td>
              <td><?=h($m[3])?></td>
              <td><?=h($m[4])?></td>
              <td>
                <a href="anggota.php?edit=<?=h($m[0])?>" class="btn btn-sm btn-warning">Edit</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Form Tambah/Edit -->
  <div class="card p-3 mb-4">
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $e) echo h($e)."<br>"; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($_GET['edit'])):
        $eid = $_GET['edit'];
        $edit = array_values(array_filter($members, fn($x)=>$x[0]===$eid))[0] ?? null;
    ?>
      <h3>Edit Anggota <?=h($eid)?></h3>
      <form method="POST" class="row g-3">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?=h($eid)?>">
        <div class="col-md-6"><input class="form-control" name="nama" placeholder="Nama" value="<?=h($edit[1] ?? '')?>"></div>
        <div class="col-md-6"><input class="form-control" name="email" placeholder="Email" value="<?=h($edit[2] ?? '')?>"></div>
        <div class="col-md-6"><input class="form-control" name="telepon" placeholder="Telepon" value="<?=h($edit[3] ?? '')?>"></div>
        <div class="col-md-6">
          <select class="form-select" name="status">
            <option value="aktif" <?=($edit[4] ?? '')==='aktif'?'selected':''?>>Aktif</option>
            <option value="nonaktif" <?=($edit[4] ?? '')==='nonaktif'?'selected':''?>>Nonaktif</option>
          </select>
        </div>
        <div class="col-md-12 d-grid"><button class="btn btn-success">Simpan</button></div>
      </form>
    <?php else: ?>
      <h3>Registrasi Anggota</h3>
      <form method="POST" class="row g-3">
        <input type="hidden" name="action" value="add">
        <div class="col-md-6"><input class="form-control" name="nama" placeholder="Nama"></div>
        <div class="col-md-6"><input class="form-control" name="email" placeholder="Email"></div>
        <div class="col-md-6"><input class="form-control" name="telepon" placeholder="Telepon"></div>
        <div class="col-md-6"><input class="form-control" name="status" placeholder="status"></div>
        <div class="col-md-12 d-grid"><button class="btn btn-primary">Daftar</button></div>
      </form>
    <?php endif; ?>
  </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
