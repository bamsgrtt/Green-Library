<?php
require_once __DIR__ . '/functions.php';

$books = readData(BOOKS_FILE);
$errors = [];

// handle POST actions: add, edit, delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $judul = trim($_POST['judul'] ?? '');
        $penulis = trim($_POST['penulis'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');
        $tahun = trim($_POST['tahun'] ?? '');
        $status = trim($_POST['status'] ?? 'tersedia');
        $kond = $_POST['kondisi'] ?? [];
        if (strlen($judul) < 3) $errors[] = 'Judul minimal 3 karakter';
        if (!is_numeric($tahun) || intval($tahun) < 1900) $errors[] = 'Tahun tidak valid';
        if (empty($errors)) {
            $id = nextID(BOOKS_FILE, 'B');
            appendRow(BOOKS_FILE, [$id, $judul, $penulis, $kategori, $tahun, $status, implode(',', $kond)]);
            header('Location: buku.php');
            exit;
        }
    }
    if ($action === 'edit') {
        $id = $_POST['id'] ?? '';
        foreach ($books as &$b) {
            if ($b[0] === $id) {
                $b[1] = trim($_POST['judul'] ?? $b[1]);
                $b[2] = trim($_POST['penulis'] ?? $b[2]);
                $b[3] = trim($_POST['kategori'] ?? $b[3]);
                $b[4] = trim($_POST['tahun'] ?? $b[4]);
                $b[5] = trim($_POST['status'] ?? $b[5]);
                $b[6] = implode(',', $_POST['kondisi'] ?? explode(',', $b[6]));
                break;
            }
        }
        writeTable(BOOKS_FILE, $books);
        header('Location: buku.php');
        exit;
    }
    if ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $new = array_filter($books, function($r) use ($id){ return $r[0] !== $id; });
        writeTable(BOOKS_FILE, $new);
        header('Location: buku.php');
        exit;
    }
}

// search / filter
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

$display_books = array_values(array_filter($books, function($b) use ($search, $category) {
    if ($category && strtolower($b[3]) !== strtolower($category)) return false;
    if (!$search) return true;
    $s = strtolower($search);
    return (strpos(strtolower($b[1]), $s) !== false) || (strpos(strtolower($b[2]), $s) !== false) || (strpos(strtolower($b[3]), $s) !== false);
}));

$cats = [];
foreach ($books as $b) if (!empty($b[3])) $cats[strtolower($b[3])] = $b[3];
ksort($cats);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Buku - Perpustakaan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand me-auto" href="index.php">Green - Library</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="index.phpoffcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
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
<!-- Search & Filter -->
<div class="container my-4">
    <div class="card mb-3 p-3">
      <form class="row g-3" method="GET">
          <div class="col-md-5">
              <input type="text" name="search" class="form-control" placeholder="Cari judul/penulis/kategori" value="<?=h($search)?>">
          </div>
          <div class="col-md-4">
            <select name="category" class="form-select">
              <option value="">Semua Kategori</option>
              <?php foreach ($cats as $c): ?>
                <option value="<?=h($c)?>" <?=strtolower($category)===strtolower($c)?'selected':''?>><?=h($c)?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3 d-flex g-3 ">
            <button class="btn btn-success flex-grow-1">Cari</button>
            <button type="button" class="btn btn-primary flex-grow-1" data-bs-toggle="modal" data-bs-target="#tambahModal">
                 Tambah
               </button>
          </div>
        
      </form>
    </div>

  <!-- Daftar Buku -->
  <div class="card mb-4 p-3">
    <h3>Daftar Buku</h3>
    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark"><tr><th>ID</th><th>Judul</th><th>Penulis</th><th>Kategori</th><th>Tahun</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php foreach ($display_books as $b): ?>
            <tr>
              <td><?=h($b[0])?></td>
              <td><?=h($b[1])?></td>
              <td><?=h($b[2])?></td>
              <td><?=h($b[3])?></td>
              <td><?=h($b[4])?></td>
              <td><?=h($b[5])?></td>
              <td>
                <a href="buku.php?edit=<?=h($b[0])?>" class="btn btn-sm btn-primary">Edit</a>
                <form method="POST" style="display:inline" onsubmit="return confirm('Hapus buku?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?=h($b[0])?>">
                  <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

 
  <!-- Modal Tambah Buku -->
<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="tambahModalLabel">Tambah Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <form method="POST" class="row g-3">
          <input type="hidden" name="action" value="add">
          <div class="col-md-6"><input class="form-control" name="judul" placeholder="Judul"></div>
          <div class="col-md-6"><input class="form-control" name="penulis" placeholder="Penulis"></div>
          <div class="col-md-6"><input class="form-control" name="kategori" placeholder="Kategori" value="Umum"></div>
          <div class="col-md-6"><input class="form-control" name="tahun" placeholder="2024"></div>
          <div class="col-md-12">
            <div class="form-check form-check-inline">
              <input type="radio" name="status" value="tersedia" class="form-check-input" checked>
              <label class="form-check-label">Tersedia</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" name="status" value="dipinjam" class="form-check-input">
              <label class="form-check-label">Dipinjam</label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-check form-check-inline">
              <input type="checkbox" name="kondisi[]" value="baru" class="form-check-input">
              <label class="form-check-label">Baru</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" name="kondisi[]" value="bekas" class="form-check-input">
              <label class="form-check-label">Bekas</label>
            </div>
          </div>
          <div class="col-md-12 d-grid">
            <button class="btn btn-primary">Tambah Buku</button>
          </div>
        </form>
      </div>
      
    </div>
  </div>
</div>

<?php
$edit = null;
if (!empty($_GET['edit'])) {
    $eid = $_GET['edit'];
    $edit = array_values(array_filter($books, fn($x)=>$x[0]===$eid))[0] ?? null;
}
?>

<?php if($edit): ?>
<div class="modal fade show" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" style="display:block;" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Buku</h5>
        <a href="buku.php" class="btn-close" aria-label="Close"></a>
      </div>
      
      <div class="modal-body">
        <form method="POST" class="row g-3">
          <input type="hidden" name="action" value="edit">
          <input type="hidden" name="id" value="<?=h($edit[0])?>">

          <div class="col-md-6">
            <input class="form-control" name="judul" placeholder="Judul" value="<?=h($edit[1])?>">
          </div>
          <div class="col-md-6">
            <input class="form-control" name="penulis" placeholder="Penulis" value="<?=h($edit[2])?>">
          </div>
          <div class="col-md-6">
            <input class="form-control" name="kategori" placeholder="Kategori" value="<?=h($edit[3])?>">
          </div>
          <div class="col-md-6">
            <input class="form-control" name="tahun" placeholder="Tahun" value="<?=h($edit[4])?>">
          </div>

          <div class="col-md-12">
            <div class="form-check form-check-inline">
              <input type="radio" name="status" value="tersedia" class="form-check-input" <?=($edit[5]==='tersedia')?'checked':''?>>
              <label class="form-check-label">Tersedia</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" name="status" value="dipinjam" class="form-check-input" <?=($edit[5]==='dipinjam')?'checked':''?>>
              <label class="form-check-label">Dipinjam</label>
            </div>
          </div>

          <div class="col-md-12">
            <?php $kond = explode(',', $edit[6]); ?>
            <div class="form-check form-check-inline">
              <input type="checkbox" name="kondisi[]" value="baru" class="form-check-input" <?=in_array('baru',$kond)?'checked':''?>>
              <label class="form-check-label">Baru</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" name="kondisi[]" value="bekas" class="form-check-input" <?=in_array('bekas',$kond)?'checked':''?>>
              <label class="form-check-label">Bekas</label>
            </div>
          </div>

          <div class="col-md-12 d-grid">
            <button class="btn btn-success">Simpan</button>
          </div>
        </form>
      </div>
      
    </div>
  </div>
</div>
<script>
  var editModal = new bootstrap.Modal(document.getElementById('editModal'));
  editModal.show();
</script>
<?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
