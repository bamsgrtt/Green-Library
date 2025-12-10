<?php
require_once __DIR__ . '/functions.php';

session_start();

$books = readData(BOOKS_FILE);
$errors = [];
$editBook = null;

// Handle POST actions: add, edit, delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $judul = trim($_POST['judul'] ?? '');
        $penulis = trim($_POST['penulis'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');
        $tahun = trim($_POST['tahun'] ?? '');
        $status = trim($_POST['status'] ?? 'tersedia');
        $kond = is_array($_POST['kondisi'] ?? []) ? $_POST['kondisi'] : [];

        if (strlen($judul) < 3) $errors[] = 'Judul minimal 3 karakter';
        if (strlen($penulis) < 3) $errors[] = 'Penulis minimal 3 karakter';
        if (!is_numeric($tahun) || intval($tahun) < 1900 || intval($tahun) > date('Y') + 1) $errors[] = 'Tahun tidak valid';
        if (!in_array($status, ['tersedia', 'dipinjam'])) $errors[] = 'Status tidak valid';

        // Upload cover dengan validasi
        $cover = '';
        if(isset($_FILES['cover']) && $_FILES['cover']['error'] === 0){
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($_FILES['cover']['type'], $allowed)) {
                $errors[] = 'Format gambar tidak didukung (JPEG, PNG, GIF, WebP)';
            } elseif ($_FILES['cover']['size'] > $maxSize) {
                $errors[] = 'Ukuran gambar maksimal 5MB';
            } else {
                $cover = time() . '_' . bin2hex(random_bytes(4)) . '_' . basename($_FILES['cover']['name']);
                if (!move_uploaded_file($_FILES['cover']['tmp_name'], 'assets/img/' . $cover)) {
                    $errors[] = 'Gagal mengunggah gambar';
                    $cover = '';
                }
            }
        }

        if (empty($errors)) {
            $id = nextID(BOOKS_FILE, 'B');
            appendRow(BOOKS_FILE, [$id, $judul, $penulis, $kategori, $tahun, $status,'', $cover]);
            header('Location: buku.php');
            exit;
        }
    }

    if ($action === 'edit') {
        $id = $_POST['id'] ?? '';
        $judul = trim($_POST['judul'] ?? '');
        $penulis = trim($_POST['penulis'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');
        $tahun = trim($_POST['tahun'] ?? '');
        $status = trim($_POST['status'] ?? '');
        $kond = $_POST['kondisi'] ?? [];
        if (!is_array($kond)) $kond = [];

        if (strlen($judul) < 3) $errors[] = 'Judul minimal 3 karakter';
        if (!is_numeric($tahun) || intval($tahun) < 1900 || intval($tahun) > date('Y') + 1) $errors[] = 'Tahun tidak valid';

        if (empty($errors)) {
            foreach ($books as &$b) {
                if ($b[0] === $id) {
                    $b[1] = $judul;
                    $b[2] = $penulis;
                    $b[3] = $kategori;
                    $b[4] = $tahun;
                    $b[5] = $status;
                    $b[6] = '';

                    // Upload cover baru
                    if(isset($_FILES['cover']) && $_FILES['cover']['error'] === 0){
                        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                        $maxSize = 5 * 1024 * 1024;
                        
                        if (in_array($_FILES['cover']['type'], $allowed) && $_FILES['cover']['size'] <= $maxSize) {
                            $cover = time() . '_' . bin2hex(random_bytes(4)) . '_' . basename($_FILES['cover']['name']);
                            if (move_uploaded_file($_FILES['cover']['tmp_name'], 'assets/img/' . $cover)) {
                                if (!empty($b[7]) && file_exists('assets/img/' . $b[7])) {
                                    @unlink('assets/img/' . $b[7]);
                                }
                                $b[7] = $cover;
                            }
                        }
                    }
                    break;
                }
            }
            writeTable(BOOKS_FILE, $books);
            header('Location: buku.php');
            exit;
        }
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $new = array_filter($books, fn($r) => $r[0] !== $id);
        writeTable(BOOKS_FILE, $new);
        header('Location: buku.php');
        exit;
    }
}

// Check for edit parameter
$editId = $_GET['edit'] ?? '';
if ($editId) {
    foreach ($books as $b) {
        if ($b[0] === $editId) {
            $editBook = $b;
            break;
        }
    }
}

// Search / filter
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

$display_books = array_values(array_filter($books, function($b) use ($search, $category) {
  if ($category && strtolower($b[3]) !== strtolower($category)) return false;
  if ($search && stripos($b[1], $search) === false && stripos($b[2], $search) === false) return false;
  return true;
}));

// Get all categories
$cats = array_unique(array_column($books, 3));
sort($cats);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Buku - Green Library</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body class="pt-5">

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
                        <a class="nav-link mx-lg-2 " aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active mx-lg-2" href="buku.php">Buku</a>
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

<!-- Search & Filter -->
<div class="container mt-4">
  <div class="card mb-3 p-3">
  <form class="row g-3" method="GET">
    <div class="col-md-5">
      <input type="text" name="search" class="form-control" placeholder="Cari buku..." value="<?=h($search)?>">
    </div>
    <div class="col-md-4">
      <select name="category" class="form-select">
        <option value="">Semua Kategori</option>
        <?php foreach ($cats as $c): ?>
        <option value="<?=h($c)?>" <?=strtolower($category)===strtolower($c)?'selected':''?>><?=h($c)?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
      <button class="btn btn-success flex-grow-1">Cari</button>
      <button type="button" class="btn btn-primary flex-grow-1" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah</button>
    </div>
  </form>
  </div>

  <?php if (!empty($errors)): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>Kesalahan!</strong>
  <ul class="mb-0">
    <?php foreach ($errors as $err): ?>
    <li><?=h($err)?></li>
    <?php endforeach; ?>
  </ul>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <!-- Daftar Buku -->
  <div class="row">
  <?php foreach ($display_books as $b): ?>
  <div class="col-md-6 col-lg-2 mb-4">
    <div class="card h-100">
      <?php if (!empty($b[7])): ?>
      <img src="assets/img/<?=h($b[7])?>" class="card-img-top cover-img" alt="Cover">
      <?php else: ?>
      <div class="card-img-top bg-dark d-flex align-items-center justify-content-center"    style="height: 180px;"><span class="text-muted">Tidak ada cover</span></div>
      <?php endif; ?>
      <div class="card-body d-flex flex-column book-info">
        <h5 class="card-title"><?=h($b[1])?></h5>
        <p class="card-text text-muted"><?=h($b[2])?></p>
        <small><strong>Kategori:</strong> <?=h($b[3])?></small>
        <small><strong>Tahun:</strong> <?=h($b[4])?></small>
        <small><strong>Status:</strong> <span class="badge <?=$b[5]==='tersedia'?'bg-success':'bg-warning'?>"><?=h($b[5])?></span></small>



<!-- Modal Tambah Buku -->
<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="tambahModalLabel">Tambah Buku</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form method="POST" class="row g-3" enctype="multipart/form-data">
          <input type="hidden" name="action" value="add">
          <div class="col-md-6"><input class="form-control" name="judul" placeholder="Judul" required></div>
          <div class="col-md-6"><input class="form-control" name="penulis" placeholder="Penulis" required></div>
          <div class="col-md-6"><input class="form-control" name="kategori" placeholder="Kategori" value="Umum" required></div>
          <div class="col-md-6"><input type="number" class="form-control" name="tahun" placeholder="2024" min="1900" max="<?=date('Y')+1?>" required></div>
          <div class="col-md-12 d-flex gap-3">
            <div class="form-check form-check-inline">
              <input type="radio" name="status" value="tersedia" class="form-check-input" checked required>
              <label class="form-check-label">Tersedia</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" name="status" value="dipinjam" class="form-check-input" required>
              <label class="form-check-label">Dipinjam</label>
            </div>
          </div>
         
          <div class="mb-2">
            <label>Cover Buku</label>
            <input type="file" name="cover" accept="image/*" class="form-control">
            <small class="text-muted">Format: JPEG, PNG, GIF, WebP. Ukuran maksimal: 5MB</small>
          </div>
          <div class="col-md-12 d-grid">
            <button type="submit" class="btn btn-primary">Tambah Buku</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Buku -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Buku</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <?php if ($editBook): ?>
        <form method="POST" class="row g-3" enctype="multipart/form-data">
          <input type="hidden" name="action" value="edit">
          <input type="hidden" name="id" value="<?=h($editBook[0])?>">
          <div class="col-md-6"><input class="form-control" name="judul" placeholder="Judul" value="<?=h($editBook[1])?>" required></div>
          <div class="col-md-6"><input class="form-control" name="penulis" placeholder="Penulis" value="<?=h($editBook[2])?>" required></div>
          <div class="col-md-6"><input class="form-control" name="kategori" placeholder="Kategori" value="<?=h($editBook[3])?>" required></div>
          <div class="col-md-6"><input type="number" class="form-control" name="tahun" placeholder="2024" value="<?=h($editBook[4])?>" min="1900" max="<?=date('Y')+1?>" required></div>
          <div class="col-md-12 d-flex gap-3">
            <div class="form-check form-check-inline">
              <input type="radio" name="status" value="tersedia" class="form-check-input" <?=$editBook[5]==='tersedia'?'checked':''?> required>
              <label class="form-check-label">Tersedia</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" name="status" value="dipinjam" class="form-check-input" <?=$editBook[5]==='dipinjam'?'checked':''?> required>
              <label class="form-check-label">Dipinjam</label>
            </div>
          </div>

          <div class="mb-2">
            <label>Cover Buku</label>
            <?php if (!empty($editBook[7])): ?>
            <div class="mb-2"><img src="assets/img/<?=h($editBook[7])?>" alt="Cover" style="max-width:150px;"></div>
            <?php endif; ?>
            <input type="file" name="cover" accept="image/*" class="form-control">
            <small class="text-muted">Format: JPEG, PNG, GIF, WebP. Ukuran maksimal: 5MB</small>
          </div>
          <div class="col-md-12 d-grid">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
// Show edit modal if editing
<?php if ($editBook): ?>
document.addEventListener('DOMContentLoaded', function() {
    new bootstrap.Modal(document.getElementById('editModal')).show();
});
<?php endif; ?>
</script>
            <div class="mt-auto d-flex justify-content-between">
              <a href="buku.php?edit=<?=h($b[0])?>" class="btn btn-sm btn-primary">Edit</a>
              <form method="POST" onsubmit="return confirm('Hapus buku?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?=h($b[0])?>">
                  <button class="btn btn-sm btn-danger">Hapus</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
