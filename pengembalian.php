<?php
include 'functions.php';
$bukuFile='data/buku.txt';
$pinjamFile='data/peminjaman.txt';
$pengembalianFile='data/pengembalian.txt';

$buku=getData($bukuFile);
$peminjaman=getData($pinjamFile);
$pengembalian=getData($pengembalianFile);

if(isset($_POST['kembali'])){
    $id=$_POST['id'];
    $tgl_kembali=date('Y-m-d');

    foreach($peminjaman as $p){
        if($p['id']==$id){
            $tgl_jatuhTempo = strtotime($p['tgl_kembali']);
            $tgl_actual = strtotime($tgl_kembali);
            $denda = max(0, ($tgl_actual-$tgl_jatuhTempo)/86400)*1000; // 1000 per hari
            $pengembalian[]=[
                'id'=>getNextId($pengembalian),
                'id_peminjaman'=>$id,
                'tgl_kembali'=>$tgl_kembali,
                'denda'=>$denda
            ];
            // Update status buku
            foreach($buku as &$b){
                if($b['id']==$p['id_buku']){
                    $b['status']='tersedia';
                }
            }
        }
    }
    // Hapus peminjaman yang sudah kembali
    $peminjaman=array_filter($peminjaman, fn($p)=>$p['id']!=$id);
    saveData($bukuFile,$buku);
    saveData($pinjamFile,$peminjaman);
    saveData($pengembalianFile,$pengembalian);
}

// Tabel pengembalian
?>

<h2>Pengembalian Buku</h2>

<table border="1" cellpadding="5">
<tr><th>Buku</th><th>Anggota</th><th>Tgl Pinjam</th><th>Tgl Jatuh Tempo</th><th>Aksi</th></tr>
<?php foreach($peminjaman as $p):
$b=findById($buku,$p['id_buku']);
$a=findById(getData('data/anggota.txt'),$p['id_anggota']);
?>
<tr>
    <td><?= $b['judul'] ?></td>
    <td><?= $a['nama'] ?></td>
    <td><?= $p['tgl_pinjam'] ?></td>
    <td><?= $p['tgl_kembali'] ?></td>
    <td>
        <form method="POST" style="display:inline">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <button type="submit" name="kembali">Kembalikan</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>
