<?php
require_once __DIR__ . '/functions.php';

$books = readData(BOOKS_FILE);
$members = readData(MEMBERS_FILE);
$loans = readData(LOANS_FILE);
$returns = readData(RETURNS_FILE);
global $BORROW_LIMIT, $BORROW_DAYS, $FINE_PER_DAY;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'borrow') {
        $member_id = $_POST['member_id'] ?? '';
        $book_id = $_POST['book_id'] ?? '';
        $foundMember = null; foreach ($members as $m) if ($m[0] === $member_id) { $foundMember = $m; break; }
        if (!$foundMember) $errors[] = 'Member tidak ditemukan';
        $foundBook = null; foreach ($books as $b) if ($b[0] === $book_id) { $foundBook = $b; break; }
        if (!$foundBook) $errors[] = 'Buku tidak ditemukan';
        if ($foundBook && ($foundBook[5] ?? '') !== 'tersedia') $errors[] = 'Buku tidak tersedia';
        $countBorrowed = 0;
        foreach ($loans as $l) if ($l[1] === $member_id && $l[5] !== '1') $countBorrowed++;
        if ($countBorrowed >= $BORROW_LIMIT) $errors[] = "Member telah mencapai batas peminjaman ({$BORROW_LIMIT})";
        if (empty($errors)) {
            $id = nextID(LOANS_FILE, 'L');
            $borrow_date = date('Y-m-d');
            $due = date('Y-m-d', strtotime("+{$BORROW_DAYS} days"));
            appendRow(LOANS_FILE, [$id, $member_id, $book_id, $borrow_date, $due, '0']);
            foreach ($books as &$b) if ($b[0] === $book_id) { $b[5] = 'dipinjam'; break; }
            writeTable(BOOKS_FILE, $books);
            header('Location: peminjaman.php');
            exit;
        }
    }
    if ($action === 'return') {
        $loan_id = $_POST['loan_id'] ?? '';
        $foundLoan = null;
        foreach ($loans as &$l) {
            if ($l[0] === $loan_id) { $foundLoan = &$l; break; }
        }
        if ($foundLoan) {
            $return_date = date('Y-m-d');
            $due = $foundLoan[4];
            $daysLate = max(0, (int)floor((strtotime($return_date) - strtotime($due)) / 86400));
            $fine = $daysLate * $FINE_PER_DAY;
            $foundLoan[5] = '1';
            writeTable(LOANS_FILE, $loans);
            foreach ($books as &$b) if ($b[0] === $foundLoan[2]) { $b[5] = 'tersedia'; break; }
            writeTable(BOOKS_FILE, $books);
            $rid = nextID(RETURNS_FILE, 'R');
            appendRow(RETURNS_FILE, [$rid, $loan_id, $return_date, $fine]);
            header('Location: peminjaman.php');
            exit;
        } else {
            $errors[] = 'Loan ID tidak ditemukan';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Peminjaman - Perpustakaan</title>
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
                        <a class="nav-link  mx-lg-2" aria-current="page" href="index.php">Home</a>
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
  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $e) echo h($e)."<br>"; ?>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card p-3 shadow-sm">
        <h4>Form Peminjaman</h4>
        <form method="POST">
          <input type="hidden" name="action" value="borrow">
          <div class="mb-3">
            <label class="form-label">Anggota</label>
            <select class="form-select" name="member_id">
              <?php foreach ($members as $m): ?>
                <option value="<?=h($m[0])?>"><?=h($m[1])?> (<?=h($m[0])?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Buku (hanya tersedia)</label>
            <select class="form-select" name="book_id">
              <?php foreach ($books as $b): if (($b[5] ?? '') === 'tersedia'): ?>
                <option value="<?=h($b[0])?>"><?=h($b[1])?> - <?=h($b[3])?> (<?=h($b[0])?>)</option>
              <?php endif; endforeach; ?>
            </select>
          </div>
          <button class="btn btn-primary w-100">Proses Peminjaman</button>
        </form>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card p-3 shadow-sm">
        <h4>Riwayat Peminjaman</h4>
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle">
            <thead class="table-primary">
              <tr>
                <th>ID</th><th>Anggota</th><th>Buku</th><th>Pinjam</th><th>Jatuh Tempo</th><th>Status</th><th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($loans as $l):
                $en = enrichLoan($l); $loan = $en[0]; $member = $en[1]; $book = $en[2];
                $isReturned = $loan[5] === '1';
                $late = 0;
                if (!$isReturned && strtotime(date('Y-m-d')) > strtotime($loan[4])) {
                    $late = (int)floor((strtotime(date('Y-m-d')) - strtotime($loan[4]))/86400);
                }
              ?>
                <tr>
                  <td><?=h($loan[0])?></td>
                  <td><?=h($member[1] ?? 'Unknown')?></td>
                  <td><?=h($book[1] ?? 'Unknown')?></td>
                  <td><?=h($loan[3])?></td>
                  <td><?=h($loan[4])?></td>
                  <td><?= $isReturned ? '<span class="badge bg-success">Dikembalikan</span>' : ($late ? "<span class='badge bg-danger'>Terlambat {$late} hari</span>" : '<span class="badge bg-warning text-dark">Dipinjam</span>') ?></td>
                  <td>
                    <?php if (!$isReturned): ?>
                      <form method="POST" class="d-inline" onsubmit="return confirm('Proses pengembalian?')">
                        <input type="hidden" name="action" value="return">
                        <input type="hidden" name="loan_id" value="<?=h($loan[0])?>">
                        <button class="btn btn-sm btn-success">Kembalikan</button>
                      </form>
                    <?php else: ?> - <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="card mt-3 p-3 text-muted">
    <p>Limit peminjaman: <?=h($BORROW_LIMIT)?> buku. Lama pinjam default: <?=h($BORROW_DAYS)?> hari. Denda: Rp <?=rp($FINE_PER_DAY)?>/hari.</p>
  </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
