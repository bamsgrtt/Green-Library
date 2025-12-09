<?php
include 'functions.php';
$file = 'data/buku.txt';
$buku = getData($file);

// Tambah Buku
if(isset($_POST['tambah'])){
    $buku[] = [
        'id'=>getNextId($buku),
        'judul'=>$_POST['judul'],
        'penulis'=>$_POST['penulis'],
        'kategori'=>$_POST['kategori'],
        'tahun'=>$_POST['tahun'],
        'status'=>'tersedia'
    ];
    saveData($file,$buku);
}

// Edit Buku
if(isset($_POST['edit'])){
    foreach($buku as &$b){
        if($b['id']==$_POST['id']){
            $b['judul']=$_POST['judul'];
            $b['penulis']=$_POST['penulis'];
            $b['kategori']=$_POST['kategori'];
            $b['tahun']=$_POST['tahun'];
        }
    }
    saveData($file,$buku);
}

// Hapus Buku
if(isset($_GET['hapus'])){
    $id=$_GET['hapus'];
    $buku=array_filter($buku, fn($b)=>$b['id']!=$id);
    saveData($file,$buku);
}

// Filter / Pencarian
$search=$_GET['search']??'';
$kategori=$_GET['kategori']??'';
$display=$buku;
if($search) $display=array_filter($display, fn($b)=>stripos($b['judul'],$search)!==false);
if($kategori) $display=array_filter($display, fn($b)=>$b['kategori']==$kategori);
?>

<h2>Manajemen Buku</h2>

<!-- Form Tambah / Edit -->
<form method="POST">
    <input type="hidden" name="id" value="<?= $_GET['edit']??'' ?>">
    <input type="text" name="judul" placeholder="Judul" value="<?= $_GET['edit'] ? findById($buku,$_GET['edit'])['judul'] : '' ?>" required>
    <input type="text" name="penulis" placeholder="Penulis" value="<?= $_GET['edit'] ? findById($buku,$_GET['edit'])['penulis'] : '' ?>">
    <input type="text" name="kategori" placeholder="Kategori" value="<?= $_GET['edit'] ? findById($buku,$_GET['edit'])['kategori'] : '' ?>">
    <input type="number" name="tahun" placeholder="Tahun" value="<?= $_GET['edit'] ? findById($buku,$_GET['edit'])['tahun'] : '' ?>">
    <button type="submit" name="<?= $_GET['edit'] ? 'edit':'tambah' ?>"><?= $_GET['edit'] ? 'Update':'Tambah' ?></button>
</form>

<!-- Filter / Pencarian -->
<form method="GET">
    <input type="text" name="search" placeholder="Cari judul" value="<?= $search ?>">
    <input type="text" name="kategori" placeholder="Kategori" value="<?= $kategori ?>">
    <button type="submit">Filter</button>
</form>

<table border="1" cellpadding="5">
<tr><th>Judul</th><th>Penulis</th><th>Kategori</th><th>Tahun</th><th>Status</th><th>Aksi</th></tr>
<?php foreach($display as $b): ?>
<tr>
    <td><?= $b['judul'] ?></td>
    <td><?= $b['penulis'] ?></td>
    <td><?= $b['kategori'] ?></td>
    <td><?= $b['tahun'] ?></td>
    <td><?= $b['status'] ?></td>
    <td>
        <a href="?edit=<?= $b['id'] ?>">Edit</a> |
        <a href="?hapus=<?= $b['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
