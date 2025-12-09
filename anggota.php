<?php
include 'functions.php';
$file='data/anggota.txt';
$anggota=getData($file);

if(isset($_POST['tambah'])){
    $anggota[]=[
        'id'=>getNextId($anggota),
        'nama'=>$_POST['nama'],
        'email'=>$_POST['email'],
        'status_keanggotaan'=>$_POST['status']??'aktif',
        'tgl_daftar'=>date('Y-m-d')
    ];
    saveData($file,$anggota);
}

if(isset($_POST['edit'])){
    foreach($anggota as &$a){
        if($a['id']==$_POST['id']){
            $a['nama']=$_POST['nama'];
            $a['email']=$_POST['email'];
            $a['status_keanggotaan']=$_POST['status'];
        }
    }
    saveData($file,$anggota);
}

if(isset($_GET['hapus'])){
    $id=$_GET['hapus'];
    $anggota=array_filter($anggota, fn($a)=>$a['id']!=$id);
    saveData($file,$anggota);
}

$search=$_GET['search']??'';
$display=$anggota;
if($search) $display=array_filter($display, fn($a)=>stripos($a['nama'],$search)!==false);
?>

<h2>Manajemen Anggota</h2>

<form method="POST">
    <input type="hidden" name="id" value="<?= $_GET['edit']??'' ?>">
    <input type="text" name="nama" placeholder="Nama" value="<?= $_GET['edit'] ? findById($anggota,$_GET['edit'])['nama'] : '' ?>" required>
    <input type="email" name="email" placeholder="Email" value="<?= $_GET['edit'] ? findById($anggota,$_GET['edit'])['email'] : '' ?>" required>
    <select name="status">
        <option value="aktif" <?= $_GET['edit'] && findById($anggota,$_GET['edit'])['status_keanggotaan']=='aktif' ? 'selected':'' ?>>Aktif</option>
        <option value="nonaktif" <?= $_GET['edit'] && findById($anggota,$_GET['edit'])['status_keanggotaan']=='nonaktif' ? 'selected':'' ?>>Nonaktif</option>
    </select>
    <button type="submit" name="<?= $_GET['edit'] ? 'edit':'tambah' ?>"><?= $_GET['edit']?'Update':'Tambah' ?></button>
</form>

<form method="GET">
    <input type="text" name="search" placeholder="Cari anggota" value="<?= $search ?>">
    <button type="submit">Filter</button>
</form>

<table border="1" cellpadding="5">
<tr><th>Nama</th><th>Email</th><th>Status</th><th>Tgl Daftar</th><th>Aksi</th></tr>
<?php foreach($display as $a): ?>
<tr>
    <td><?= $a['nama'] ?></td>
    <td><?= $a['email'] ?></td>
    <td><?= $a['status_keanggotaan'] ?></td>
    <td><?= $a['tgl_daftar'] ?></td>
    <td>
        <a href="?edit=<?= $a['id'] ?>">Edit</a> |
        <a href="?hapus=<?= $a['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
