<?php
// Path: functions.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ===== PATH KONSTANTA =====
define('DB_BUKU', 'data/buku.txt');
define('DB_ANGGOTA', 'data/anggota.txt');
define('DB_PEMINJAMAN', 'data/peminjaman.txt');

// ===== UTILITY & FILE HANDLING =====

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

function write_file($filename, $data_array) {
    // Menyimpan array PHP ke file sebagai string yang dipisahkan '|'
    $lines = [];
    // Jika array kosong, kita hanya membuat file kosong
    if (empty($data_array) && file_exists($filename)) {
        file_put_contents($filename, '');
        return;
    }
    
    // Asumsi data array of associative arrays
    foreach ($data_array as $item) {
        $line_parts = array_values($item);
        // Khusus untuk buku/anggota/peminjaman, kondisikan array ke string
        if (isset($item['kondisi']) && is_array($item['kondisi'])) {
            $line_parts[array_search($item['kondisi'], $line_parts)] = implode(",", $item['kondisi']);
        }
        $lines[] = implode("|", $line_parts);
    }
    
    // Pastikan folder data ada
    if (!is_dir('data')) {
        mkdir('data', 0777, true);
    }
    file_put_contents($filename, implode("\n", $lines));
}

function read_file($filename) {
    if (!file_exists($filename)) {
        if (!is_dir('data')) { mkdir('data', 0777, true); }
        file_put_contents($filename, '');
        return [];
    }
    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return $lines;
}

// Helper untuk menemukan data buku/anggota yang siap digunakan dalam array asosiatif
function find_data_by_id($id, $data_list) {
    $found = array_filter($data_list, fn($item) => $item['id'] == $id);
    return current($found) ?: null;
}

// ===== 📖 MANAJEMEN BUKU LOGIC =====
// Format Data Buku: ID|Judul|Kategori|Status|Kondisi(array)
function get_all_buku() {
    $lines = read_file(DB_BUKU);
    $result = [];
    foreach ($lines as $line) {
        $parts = explode("|", $line);
        if (count($parts) >= 5) { 
             $result[] = [
                "id" => $parts[0],
                "judul" => $parts[1],
                "kategori" => $parts[2],
                "status" => $parts[3],
                "kondisi" => explode(",", $parts[4])
            ];
        }
    }
    return $result;
}

function update_buku_status($buku_id, $new_status) {
    $buku_list = get_all_buku();
    $updated = false;
    foreach ($buku_list as $key => $buku) {
        if ($buku['id'] == $buku_id) {
            $buku_list[$key]['status'] = $new_status;
            $updated = true;
            break;
        }
    }
    if ($updated) {
        write_file(DB_BUKU, $buku_list);
    }
    return $updated;
}

// ===== 👥 MANAJEMEN ANGGOTA LOGIC =====
// Format Data Anggota: ID|Nama|Email|StatusKeanggotaan
function get_all_anggota() {
    $lines = read_file(DB_ANGGOTA);
    $result = [];
    foreach ($lines as $line) {
        $parts = explode("|", $line);
         if (count($parts) >= 4) {
            $result[] = [
                "id" => $parts[0],
                "nama" => $parts[1],
                "email" => $parts[2],
                "status_keanggotaan" => $parts[3]
            ];
        }
    }
    return $result;
}

function tambah_anggota($nama, $email) {
    $anggota_list = get_all_anggota();
    $new_id = uniqid();
    $new_anggota = [
        "id" => $new_id,
        "nama" => $nama,
        "email" => $email,
        "status_keanggotaan" => 'Aktif'
    ];
    $anggota_list[] = $new_anggota;
    write_file(DB_ANGGOTA, $anggota_list);
}

// ===== 🔄 & ⏰ SISTEM PEMINJAMAN & PENGEMBALIAN LOGIC =====
// Format Data Peminjaman: ID|AnggotaID|BukuID|TglPinjam|TglJatuhTempo|TglKembali|Status|Denda(Rp)

function get_all_peminjaman() {
    $lines = read_file(DB_PEMINJAMAN);
    $result = [];
    foreach ($lines as $line) {
        $parts = explode("|", $line);
        if (count($parts) >= 8) {
            $result[] = [
                "id" => $parts[0],
                "anggota_id" => $parts[1],
                "buku_id" => $parts[2],
                "tgl_pinjam" => $parts[3],
                "tgl_jatuh_tempo" => $parts[4],
                "tgl_kembali" => $parts[5],
                "status" => $parts[6],
                "denda" => (int)$parts[7]
            ];
        }
    }
    return $result;
}

function cek_ketersediaan_buku($buku_id) {
    $buku = find_data_by_id($buku_id, get_all_buku());
    return $buku && $buku['status'] == 'tersedia';
}

function hitung_batas_pinjam($anggota_id) {
    $peminjaman_aktif = array_filter(get_all_peminjaman(), function($p) use ($anggota_id) {
        return $p['anggota_id'] == $anggota_id && $p['status'] == 'Dipinjam';
    });
    return count($peminjaman_aktif) < 3; // Batas peminjaman: Maksimal 3 buku
}


function pinjam_buku($anggota_id, $buku_id) {
    if (!cek_ketersediaan_buku($buku_id)) {
        return ['success' => false, 'msg' => 'Buku tidak tersedia.'];
    }
    if (!hitung_batas_pinjam($anggota_id)) {
        return ['success' => false, 'msg' => 'Anggota telah mencapai batas peminjaman (3 buku).'];
    }

    $peminjaman_list = get_all_peminjaman();
    $tgl_pinjam = date("Y-m-d");
    $tgl_jatuh_tempo = date("Y-m-d", strtotime("+7 days")); 

    $new_peminjaman = [
        "id" => uniqid(),
        "anggota_id" => $anggota_id,
        "buku_id" => $buku_id,
        "tgl_pinjam" => $tgl_pinjam,
        "tgl_jatuh_tempo" => $tgl_jatuh_tempo,
        "tgl_kembali" => "N/A", 
        "status" => "Dipinjam", 
        "denda" => 0
    ];
    
    $peminjaman_list[] = $new_peminjaman;
    write_file(DB_PEMINJAMAN, $peminjaman_list);

    // Update status buku
    update_buku_status($buku_id, 'dipinjam');
    
    return ['success' => true, 'msg' => 'Peminjaman berhasil. Jatuh tempo: ' . $tgl_jatuh_tempo];
}

function kembalikan_buku($id_peminjaman) {
    $peminjaman_list = get_all_peminjaman();
    $tgl_kembali = date("Y-m-d");
    $denda_per_hari = 2000;
    $result = ['success' => false, 'msg' => 'ID Peminjaman tidak valid atau sudah dikembalikan.'];
    
    foreach ($peminjaman_list as $key => $p) {
        if ($p['id'] == $id_peminjaman && $p['status'] == 'Dipinjam') {
            
            $tgl_jatuh_tempo_ts = strtotime($p['tgl_jatuh_tempo']);
            $tgl_kembali_ts = strtotime($tgl_kembali);
            $denda = 0;
            
            if ($tgl_kembali_ts > $tgl_jatuh_tempo_ts) {
                // Perhitungan Denda
                $diff = $tgl_kembali_ts - $tgl_jatuh_tempo_ts;
                $days_late = floor($diff / (60 * 60 * 24));
                $denda = $days_late * $denda_per_hari;
            }

            // Update record peminjaman
            $peminjaman_list[$key]['tgl_kembali'] = $tgl_kembali;
            $peminjaman_list[$key]['status'] = 'Dikembalikan';
            $peminjaman_list[$key]['denda'] = $denda;
            
            // Update status buku
            update_buku_status($p['buku_id'], 'tersedia');

            write_file(DB_PEMINJAMAN, $peminjaman_list);

            $result = [
                'success' => true, 
                'denda' => $denda,
                'msg' => $denda > 0 ? "Buku dikembalikan. Denda: Rp. " . number_format($denda) : "Buku dikembalikan tepat waktu."
            ];
            break;
        }
    }
    
    return $result;
}