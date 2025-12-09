<?php
include 'functions.php';
$bukuFile='data/buku.txt';
$anggotaFile='data/anggota.txt';
$pinjamFile='data/peminjaman.txt';

$buku=getData($bukuFile);
$anggota=getData($anggotaFile);
$peminjaman=getData($pinjamFile);

// Proses Pinjam
if(isset($_POST['pinjam'])){
    $id_buku=$_POST['buku'];
    $id_anggota=$_POST['anggota'];

    // Cek ketersediaan buku
    foreach($buku as &$b){
        if($b['id']==$id_buku){
            if($b['status']!='tersedia'){
                die("Buku sedang dipinjam!");
            }
            $b['status']='dipinjam';
        }
    }
    saveData($bukuFile,$buku);

    $peminjaman[]=[
        'id'=>getNextId($peminjaman),
        'id_buku'=>$id_buku,
        'id_anggota'=>$id_anggota,
        'tgl_pinjam'=>date('Y-m-d'),
        'tgl_kembali'=>date('Y-m-d', strtotime('+7 days'))
    ];
    saveData($pinjamFile,$peminjaman);
}

// Filter / tampilkan peminjaman
$display=$peminjaman;
?>

<h2>Peminjaman Buku</h2>

<form method="POST">
    <select name="buku" required>
        <option value="">Pilih Buku</option>
        <?php foreach($buku as $b): if($b['status']=='tersedia'): ?>
        <option value="<?= $b['id'] ?>"><?= $b['judul'] ?></option>
        <?php endif; endforeach; ?>
    </select>
    <select name="anggota" required>
        <option value="">Pilih Anggota</option>
        <?php foreach($anggota as $a): ?>
        <option value="<?= $a['id'] ?>"><?= $a['nama'] ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="pinjam">Pinjam</button>
</form>

<table border="1" cellpadding="5">
<tr><th>Buku</th><th>Anggota</th><th>Tgl Pinjam</th><th>Tgl Kembali</th></tr>
<?php foreach($display as $p):
$b=findById($buku,$p['id_buku']);
$a=findById($anggota,$p['id_anggota']);
?>
<tr>
    <td><?= $b['judul'] ?></td>
    <td><?= $a['nama'] ?></td>
    <td><?= $p['tgl_pinjam'] ?></td>
    <td><?= $p['tgl_kembali'] ?></td>
</tr>
<?php endforeach; ?>
</table>
