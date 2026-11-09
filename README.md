# Sistem Informasi Penjualan Toko

Tugas praktik basis data (MySQL/MariaDB) dan website PHP native.

- **Nama:** arazzel
- **Kelas:** xii rpl 2

## Struktur Repository

```
├── README.md
├── sql/
│   ├── 01_toko.sql                  Bab 1: database, tabel, data, 2 view
│   ├── 02_laporan_jobsheet2.sql     Bab 2: JOIN, subquery, UNION
│   ├── 03_procedure_jobsheet3.sql   Bab 3: 5 stored procedure
│   ├── 04_function_jobsheet4.sql    Bab 4: 5 stored function
│   └── 05_trigger_jobsheet5.sql     Bab 5: 3 trigger
├── screenshot/
│   ├── bab1/ ... bab5/              Screenshot hasil query per bab
└── toko-web/                        Website PHP native
    └── database/toko.sql            Export database lengkap (siap import)
```

## Isi Tiap Bab

| Bab | Materi | File |
|-----|--------|------|
| 1 | Database `toko`, tabel `pelanggan` dan `transaksi`, view laporan | `01_toko.sql` |
| 2 | Advanced JOIN dan Subquery: 7 laporan termasuk UNION | `02_laporan_jobsheet2.sql` |
| 3 | Stored Procedure: `TampilPelanggan`, `CariPelanggan`, `TambahPelanggan`, `HitungPelanggan`, `LaporanTransaksi` | `03_procedure_jobsheet3.sql` |
| 4 | Stored Function: `HitungDiskon`, `HitungTotal`, `HitungPajak`, `KategoriBelanja`, `KonversiNilai` | `04_function_jobsheet4.sql` |
| 5 | Trigger: `after_insert_pelanggan`, `after_update_pelanggan`, `before_insert_transaksi` | `05_trigger_jobsheet5.sql` |

## Cara Menjalankan File SQL

File SQL harus dijalankan **berurutan** dari `01` sampai `05`, karena Bab 3 sampai 5 membutuhkan tabel dari Bab 1.

```sql
source C:/xampp/mysql/bin/01_toko.sql
source C:/xampp/mysql/bin/02_laporan_jobsheet2.sql
source C:/xampp/mysql/bin/03_procedure_jobsheet3.sql
source C:/xampp/mysql/bin/04_function_jobsheet4.sql
source C:/xampp/mysql/bin/05_trigger_jobsheet5.sql
```

Atau lewat phpMyAdmin: tab **Import**, pilih file satu per satu sesuai urutan.

## Website (toko-web)

Website PHP native (PDO) yang memakai database `toko`, termasuk view, procedure, function, dan trigger dari bab-bab di atas.

**Cara memasang (XAMPP):**
1. Nyalakan Apache dan MySQL.
2. Import `toko-web/database/toko.sql` lewat phpMyAdmin.
3. Salin folder `toko-web` ke `C:\xampp\htdocs\`.
4. Buka `http://localhost/toko-web/`.

**Halaman:** Dashboard, Pelanggan (CRUD, cari, detail), Transaksi (CRUD, filter), Laporan, Log Aktivitas.

## Teknologi

PHP 8, MariaDB 10.4, PDO, HTML dan CSS.
