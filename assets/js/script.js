// NAVIGASI ANTAR HALAMAN
function showPage(page) {
    document.querySelectorAll(".page").forEach(p => p.classList.remove("active"));
    document.getElementById(page).classList.add("active");
}

// File ini disiapkan untuk tim backend:
// Mereka cukup menghubungkan API ke script.js
// Semua ID form & elemen sudah disiapkan

/* ----------------------------
    BACKEND CONNECT POINTS
------------------------------*/

// FORM TAMBAH BUKU
document.getElementById("formTambahBuku").onsubmit = e => {
    e.preventDefault();
    // Backend lihat ID berikut:
    console.log({
        judul: document.getElementById("judulBuku").value,
        penulis: document.getElementById("penulisBuku").value,
        kategori: document.getElementById("kategoriBuku").value,
        stok: document.getElementById("stokBuku").value
    });
};

// FORM TAMBAH ANGGOTA
document.getElementById("formTambahAnggota").onsubmit = e => {
    e.preventDefault();
    console.log({
        nama: document.getElementById("namaAnggota").value,
        nim: document.getElementById("nimAnggota").value,
        kelas: document.getElementById("kelasAnggota").value
    });
};

// FORM PEMINJAMAN
document.getElementById("formPeminjaman").onsubmit = e => {
    e.preventDefault();
    console.log({
        anggota: document.getElementById("anggotaPinjam").value,
        buku: document.getElementById("bukuPinjam").value,
        tanggal: document.getElementById("tanggalPinjam").value
    });
};

// FORM PENGEMBALIAN
document.getElementById("formPengembalian").onsubmit = e => {
    e.preventDefault();
    console.log({
        transaksi: document.getElementById("transaksiKembali").value,
        tanggal: document.getElementById("tanggalKembali").value
    });
};