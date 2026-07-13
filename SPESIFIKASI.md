# Spesifikasi Sistem — Enuma Absen

Dokumentasi spesifikasi sistem absensi Enuma Technology sesuai kondisi kode saat ini.

## 1. Gambaran Umum

Enuma Absen adalah sistem absensi berbasis web dengan dukungan perangkat IoT (fingerprint). Sistem mencatat kehadiran anggota berdasarkan jadwal per grup, mendukung absen fisik (via fingerprint), absen online (via web + upload foto), pemberian ijin, serta pembuatan laporan dalam format Excel dan PDF.

## 2. Teknologi

| Komponen | Teknologi |
|---|---|
| Bahasa | PHP ^8.1 |
| Framework | CodeIgniter 4 |
| Database | MySQL/MariaDB (via CI4 Database) |
| Export Excel | PhpSpreadsheet ^4.1 |
| Export PDF | Dompdf ^3.1 (via library `PdfGenerator`) |
| Deployment | Docker (Dockerfile + docker-compose.yml) |
| Testing | PHPUnit ^10.5 |

## 3. Peran Pengguna (Role)

Role ditentukan saat login berdasarkan kategori user:

| Role | Kriteria | Hak Akses |
|---|---|---|
| **Admin** | `category_id == 1` | Kelola user, admin, grup, jadwal; lihat semua data; export laporan |
| **Pengawas** | Kategori dengan flag `overseer = 1` | Kelola grup yang diawasinya (via `subcategory_overseer`), beri/batalkan ijin, tambah/hapus log anggota, absen online sendiri |
| **User** | Selain di atas | Absen online (log harian), lihat riwayat absensi sendiri |

Ketentuan tambahan:
- Hanya user dengan `can_login = 1` yang dapat login ke web.
- User dengan `active = 0` ditolak saat login ("anda dinonaktifkan").
- Anggota tanpa akses login tetap dapat absen melalui fingerprint (`id_fingerprint`).

## 4. Struktur Database

### 4.1 `categories` (Grup)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | CHAR(20), PK | Dibuat dengan `uniqid()` |
| name | VARCHAR(100) | Nama grup |
| description | TEXT, nullable | Deskripsi grup |
| pengawas_id | CHAR(36), nullable | Pengawas utama grup (relasi ke users) |
| overseer | TINYINT(1) | 1 = grup ini berisi para pengawas |
| created_at / updated_at | DATETIME | Timestamp otomatis |

Catatan: `id = 1` adalah kategori khusus **Admin** dan disembunyikan dari daftar grup.

### 4.2 `users` (Anggota)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | CHAR(36), PK | UUID v4 (helper `uuidv4`) |
| id_fingerprint | INT(11), nullable | ID pada mesin fingerprint, unik |
| name | VARCHAR(150) | Nama anggota |
| username | VARCHAR(150), nullable | Untuk login, unik |
| email | VARCHAR(150), nullable | — |
| password | VARCHAR(255), nullable | Hash `password_hash()` (bcrypt default) |
| category_id | CHAR(20), FK → categories | ON DELETE/UPDATE CASCADE |
| can_login | INT(1) | 1 = punya akses login web |
| active | INT(1) | 1 = aktif |
| created_at / updated_at | DATETIME | Timestamp otomatis |

### 4.3 `schedules` (Jadwal per Grup)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT, PK, AI | — |
| category_id | CHAR(20), FK → categories | ON DELETE/UPDATE CASCADE |
| day | ENUM(Monday…Sunday) | Hari berlaku jadwal |
| no_limit | TINYINT(1) | 1 = tanpa batasan jam (hanya checkin, tanpa checkout) |
| online | TINYINT(1) | 1 = jadwal mendukung absen online |
| start_time / end_time | TIME, nullable | Jam masuk & pulang |
| created_at / updated_at | DATETIME | Timestamp otomatis |

### 4.4 `attendances` (Absensi & Log)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT, PK, AI | — |
| user_id | CHAR(36), FK → users | ON DELETE/UPDATE CASCADE |
| status | ENUM('Hadir', 'Ijin', 'Hadir (Online)') | Default 'Hadir' |
| no_limit | TINYINT(1) | Salinan flag jadwal saat absen |
| checkin | DATETIME, nullable | Waktu masuk |
| checkout | DATETIME, nullable | Waktu pulang |
| activity | VARCHAR(255), nullable | Aktivitas (log harian) |
| description | TEXT, nullable | Deskripsi aktivitas |
| image | CHAR(255) | Nama file foto bukti (absen online) |
| terlambat | flag | Ditandai jika checkin melewati `start_time`* |
| pulcep | flag | Ditandai jika checkout sebelum `end_time`* |
| lembur | flag | Ditandai jika checkout ≥ 1 jam setelah `end_time`* |
| created_at / updated_at | DATETIME | Timestamp otomatis |

\* Kolom `terlambat`, `pulcep`, `lembur` dipakai oleh aplikasi tetapi tidak terdapat pada file migration — ditambahkan langsung ke database.

### 4.5 `subcategory_overseer` (Relasi Pengawas ↔ Grup)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT, PK, AI | — |
| category_id | CHAR(20), FK → categories | Grup yang diawasi |
| user_id | CHAR(36), FK → users, nullable | User pengawas |
| created_at / updated_at | DATETIME | Timestamp otomatis |

### 4.6 `iot_devices` (Perangkat IoT)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT, PK, AI | — |
| device_name | VARCHAR(255) | Nama perangkat |
| device_token | VARCHAR(255), unik | Token autentikasi (header `Authorization`) |
| created_at / updated_at | DATETIME | Timestamp otomatis |

## 5. Autentikasi & Filter

| Filter | Fungsi |
|---|---|
| `login` | Halaman login — redirect ke dashboard jika sudah login |
| `auth` | Wajib login (semua role) |
| `admin` | Hanya admin |
| `admin-pengawas` | Admin atau pengawas |
| `user-pengawas` | User atau pengawas |
| `iotAuth` | Validasi header `Authorization` terhadap `iot_devices.device_token` |

Session menyimpan: `user_id`, `user_name`, `username`, `user_role` (admin/pengawas/user), `is_logged_in`.

## 6. Routing & Fitur

### 6.1 Autentikasi (`Auth`)
| Method | Route | Fungsi |
|---|---|---|
| GET | `/` | Halaman login |
| POST | `/login` | Proses login (username + password, cek `can_login` & `active`) |
| GET | `/profile` | Halaman profil |
| POST | `/profile/changepass` | Ganti password (verifikasi password lama, minimal 8 karakter) |
| GET | `/logout` | Hapus session |

### 6.2 Dashboard (`Dashboard`)
| Method | Route | Fungsi |
|---|---|---|
| GET | `/dashboard` | Dashboard sesuai role: admin (jumlah user & grup), pengawas (profil + jumlah grup diawasi), user (profil + rekap hadir/ijin + status hari ini) |

### 6.3 Log Harian / Absen Online (`Log`) — filter `user-pengawas`
| Method | Route | Fungsi |
|---|---|---|
| GET | `/log` | Halaman log + rekap kehadiran user |
| POST | `/log` | Simpan log: jika belum absen hari ini → buat absensi 'Hadir (Online)' + wajib foto (dikonversi ke JPG, kualitas 50, disimpan di `images/data/{user_id}/`); jika sudah → update activity/description/foto |
| GET | `/log/getdata` | Status absen online hari ini (JSON), validasi jadwal `online = 1` dan rentang jam |
| POST | `/log/edit` | Edit activity/description sebuah log (user hanya miliknya sendiri) |
| GET | `/log/getdata/{id}` | Detail satu record log (JSON) — filter `auth` |

### 6.4 Grup (`Category`) — filter `admin-pengawas`
| Method | Route | Fungsi |
|---|---|---|
| GET | `/category` | Daftar grup: admin melihat semua, pengawas hanya grup yang diawasinya |
| GET/POST | `/category/add` | Tambah grup (nama 3–50 kar., deskripsi 3–255 kar.) |
| POST | `/category/addpengawas` | Tambah pengawas ke grup (khusus admin, batch insert) |
| POST | `/category/deletepengawas` | Hapus pengawas dari grup (khusus admin) |
| GET | `/category/detail/{id}` | Detail grup: daftar anggota + status hari ini, jadwal terurut per hari, daftar pengawas |
| GET/POST | `/category/edit/{id}` | Edit grup |
| POST | `/category/delete` | Hapus grup |
| GET | `/category/user/detail/{id}` | Detail anggota: profil, riwayat absensi, rekap (hadir/ijin/online/status hari ini) |
| POST | `/category/user/ijin` | Beri ijin hari ini (gagal jika tidak ada jadwal / sudah ijin / sudah absen) |
| POST | `/category/user/cancelijin` | Batalkan ijin hari ini (gagal jika sudah absen / tidak ada ijin) |
| POST | `/category/user/addlog` | Tambah log manual untuk anggota (tanggal, jam, status, aktivitas, deskripsi; `no_limit = 1`) |
| POST | `/category/user/logdelete` | Hapus satu record absensi/log |

### 6.5 Jadwal (`Schedule`) — filter `admin-pengawas`
| Method | Route | Fungsi |
|---|---|---|
| POST | `/schedule/store` | Tambah jadwal grup |
| GET | `/schedule/check/{id}` | Cek data jadwal (JSON) |
| POST | `/schedule/edit` | Edit jadwal |
| POST | `/schedule/delete` | Hapus jadwal |

### 6.6 Absensi & Laporan (`Attendance`) — filter `auth`
| Method | Route | Fungsi |
|---|---|---|
| GET | `/attendance/data/{id}/{date}` | Riwayat absensi user (JSON). `date` = `all` atau `awal:akhir` (`akhir` boleh `sekarang`). Keterangan: Terlambat / Pulang Cepat / Lembur |
| POST | `/attendance/printxls` | Export laporan Excel (PhpSpreadsheet) |
| POST | `/attendance/printpdf` | Export laporan PDF (Dompdf, template `layout/laporan_layout`) |

Parameter laporan (`printxls` / `printpdf`):
- **Periode**: `min` & `max` (kosong = semua data)
- **Urutan** (`order_by`): 1 = checkin ASC, 2 = checkin DESC, 3 = status ASC, 4 = status DESC
- **Isi laporan** (`data_show`): 1 = Absensi dan Log, 2 = Absensi saja, 3 = Log saja

### 6.7 Anggota (`User`) — filter `admin`
| Method | Route | Fungsi |
|---|---|---|
| GET | `/user` | Daftar anggota (non-admin), diurutkan berdasarkan `id_fingerprint` |
| GET/POST | `/user/add` | Tambah anggota (UUID v4; jika `can_login` → wajib username unik & password) |
| GET/POST | `/user/edit/{id}` | Edit anggota (validasi kondisional untuk username/fingerprint unik) |
| POST | `/user/delete` | Hapus anggota + folder foto `images/data/{id}` |

### 6.8 Admin (`Admin`) — filter `admin`
| Method | Route | Fungsi |
|---|---|---|
| GET | `/admin` | Daftar admin (`category_id = 1`) |
| GET/POST | `/admin/add` | Tambah admin |
| GET/POST | `/admin/edit/{id}` | Edit admin |
| POST | `/admin/delete` | Hapus admin |

### 6.9 API IoT (`Api`) — filter `iotAuth`
| Method | Route | Fungsi |
|---|---|---|
| GET | `/api/users/{id_fingerprint}` | Data user berdasarkan ID fingerprint (JSON) |
| POST | `/api/attendance` | Proses absen fingerprint (lihat aturan bisnis di bawah) |

## 7. Aturan Bisnis Absensi

### 7.1 Absen via Fingerprint (`POST /api/attendance`)
Input JSON: `{ "user_id": <id_fingerprint> }`.

1. User dicari berdasarkan `id_fingerprint`; jika tidak ada → 404.
2. Jadwal dicari berdasarkan kategori user + hari ini; jika tidak ada → 403 "Tidak ada jadwal".
3. **Jadwal `no_limit = 1`** (hanya checkin):
   - Sudah ada absen hari ini → 403 "sudah absen".
   - Belum → insert `status = Hadir`, `checkin = sekarang`.
4. **Jadwal normal** (checkin + checkout):
   - **Belum absen hari ini:**
     - `waktu ≤ start_time` → checkin normal.
     - `start_time < waktu < end_time` → checkin dengan flag `terlambat = 1`.
     - `waktu ≥ end_time` → ditolak ("Sudah waktunya pulang").
   - **Sudah checkin:**
     - Status `online` → ditolak (sudah absen online).
     - Kurang dari **15 menit** sejak checkin → ditolak ("Tunggu 15 menit").
     - `waktu < end_time` → checkout dengan flag `pulcep = 1` (pulang cepat).
     - `waktu ≥ end_time + 1 jam` → checkout dengan flag `lembur = 1`.
     - Selainnya → checkout normal.

### 7.2 Absen Online (`POST /log`)
1. Hanya jika jadwal hari ini memiliki `online = 1`.
2. Jadwal berbatas waktu: hanya dalam rentang `start_time`–`end_time`.
3. Absen pertama hari itu wajib menyertakan foto; foto dikonversi ke JPG (kualitas 50) dan disimpan di `images/data/{user_id}/`.
4. Status tercatat sebagai `Hadir (Online)` dengan `no_limit = 1`.
5. Pengisian ulang di hari yang sama meng-update activity/description/foto (foto lama dihapus).

### 7.3 Ijin
- Hanya dapat diberikan oleh admin/pengawas, dan hanya jika ada jadwal hari itu.
- Gagal jika anggota sudah absen atau sudah ijin.
- Ijin dapat dibatalkan selama anggota belum absen.

### 7.4 Tampilan Waktu Absensi
- Status `Ijin` → waktu "-".
- `no_limit = 1` → hanya jam checkin.
- Checkout kosong + tanggal hari ini → "Belum Pulang".
- Checkout kosong + tanggal lampau → "Tidak Absen".
- Lengkap → "jam masuk - jam pulang".

## 8. Struktur Direktori Utama

```
app/
├── Controllers/     # Auth, Dashboard, Log, Category, Schedule, Attendance, User, Admin, Api
├── Models/          # UserModel, AttendanceModel, CategoryModel, ScheduleModel, SubcategoryOverseerModel
├── Filters/         # AuthFilter, AdminFilter, AdminPengawasFilter, UserPengawasFilter, PengawasFilter, LoginFilter, IoTAuth
├── Libraries/       # PdfGenerator (wrapper Dompdf)
├── Helpers/         # uuidv4_helper
├── Validation/      # CustomRules (rule no_space, dll.)
├── Database/
│   ├── Migrations/  # 7 file migration (2025-03 s.d. 2025-04)
│   └── Seeds/
└── Views/
    ├── layout/      # termasuk laporan_layout (template PDF)
    └── pages/       # auth, dashboard, log, category, user, admin
public/
└── images/data/{user_id}/   # foto bukti absen online
```
