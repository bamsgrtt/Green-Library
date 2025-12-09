<?php
include 'functions.php';
$buku = getData('data/buku.txt');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Green Library</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <h1>Green Library</h1>
</header>

<div class="container">
    <h2>Daftar Buku</h2>
    <div class="grid">
        <?php foreach($buku as $b): ?>
        <div class="card">
            <img src="assets/images/book_placeholder.jpg" alt="<?= $b['judul'] ?>">
            <h3><?= $b['judul'] ?></h3>
            <p>Author: <?= $b['penulis'] ?></p>
            <p>Kategori: <?= $b['kategori'] ?></p>
            <p>Tahun: <?= $b['tahun'] ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
