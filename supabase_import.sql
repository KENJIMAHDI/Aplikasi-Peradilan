-- ==============================================================================
-- SCHEMA LENGKAP & SEED DATA APLIKASI PENGADILAN (100% UNTUK SUPABASE)
-- ==============================================================================

-- 1. Table users (Termasuk MFA)
CREATE TABLE IF NOT EXISTS users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'Masyarakat',
    hakim_id BIGINT NULL,
    mfa_secret VARCHAR(255) NULL,
    mfa_enabled BOOLEAN DEFAULT FALSE,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Table password_reset_tokens
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);

-- 3. Table sessions
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity INT NOT NULL
);

-- 4. Table cache & cache_locks
CREATE TABLE IF NOT EXISTS cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration BIGINT NOT NULL
);

CREATE TABLE IF NOT EXISTS cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration BIGINT NOT NULL
);

-- 5. Table jobs, job_batches, failed_jobs
CREATE TABLE IF NOT EXISTS jobs (
    id BIGSERIAL PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    attempts SMALLINT NOT NULL,
    reserved_at INT NULL,
    available_at INT NOT NULL,
    created_at INT NOT NULL
);

CREATE TABLE IF NOT EXISTS job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids TEXT NOT NULL,
    options TEXT NULL,
    cancelled_at INT NULL,
    created_at INT NOT NULL,
    finished_at INT NULL
);

CREATE TABLE IF NOT EXISTS failed_jobs (
    id BIGSERIAL PRIMARY KEY,
    uuid VARCHAR(255) UNIQUE NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. Table hakims
CREATE TABLE IF NOT EXISTS hakims (
    id BIGSERIAL PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    nip VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 7. Table ruang_sidangs
CREATE TABLE IF NOT EXISTS ruang_sidangs (
    id BIGSERIAL PRIMARY KEY,
    nama_ruangan VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 8. Table e_court_perkaras
CREATE TABLE IF NOT EXISTS e_court_perkaras (
    id BIGSERIAL PRIMARY KEY,
    nomor_register VARCHAR(255) NOT NULL,
    penggugat VARCHAR(255) NOT NULL,
    tergugat VARCHAR(255) NOT NULL,
    status VARCHAR(255) DEFAULT 'Diajukan',
    jenis_perdata VARCHAR(255) NULL,
    tanggal_daftar DATE NULL,
    posita TEXT NULL,
    petitum TEXT NULL,
    file_gugatan VARCHAR(255) NULL,
    biaya_pendaftaran DECIMAL(12,2) DEFAULT 0,
    is_bayar BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 9. Table perkara_pidanas
CREATE TABLE IF NOT EXISTS perkara_pidanas (
    id BIGSERIAL PRIMARY KEY,
    nomor_perkara VARCHAR(255) NOT NULL,
    terdakwa VARCHAR(255) NOT NULL,
    jaksa VARCHAR(255) NOT NULL,
    pasal VARCHAR(255) NOT NULL,
    status VARCHAR(255) DEFAULT 'Proses',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 10. Table jadwal_sidangs
CREATE TABLE IF NOT EXISTS jadwal_sidangs (
    id BIGSERIAL PRIMARY KEY,
    nomor_perkara VARCHAR(255) NOT NULL,
    hakim_id BIGINT REFERENCES hakims(id) ON DELETE SET NULL,
    ruang_sidang_id BIGINT REFERENCES ruang_sidangs(id) ON DELETE SET NULL,
    waktu_mulai TIMESTAMP NOT NULL,
    waktu_selesai TIMESTAMP NOT NULL,
    status_relaas VARCHAR(255) DEFAULT 'Belum Dipanggil',
    hadir_hakim BOOLEAN DEFAULT FALSE,
    hadir_panitera BOOLEAN DEFAULT FALSE,
    hadir_penggugat BOOLEAN DEFAULT FALSE,
    hadir_tergugat BOOLEAN DEFAULT FALSE,
    nomor_antrean INT NULL,
    status_antrean VARCHAR(50) DEFAULT 'menunggu',
    catatan_hakim TEXT NULL,
    status_putusan VARCHAR(100) NULL,
    file_putusan VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 11. Table riwayat_perkaras
CREATE TABLE IF NOT EXISTS riwayat_perkaras (
    id BIGSERIAL PRIMARY KEY,
    jadwal_sidang_id BIGINT REFERENCES jadwal_sidangs(id) ON DELETE CASCADE,
    tanggal_sidang DATE NOT NULL,
    agenda VARCHAR(255) NOT NULL,
    hasil_sidang TEXT NULL,
    amar_putusan TEXT NULL,
    status_perkara VARCHAR(50) DEFAULT 'Proses',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 12. Table presensi_hakims
CREATE TABLE IF NOT EXISTS presensi_hakims (
    id BIGSERIAL PRIMARY KEY,
    hakim_id BIGINT REFERENCES hakims(id) ON DELETE CASCADE,
    tanggal DATE NOT NULL,
    status VARCHAR(50) DEFAULT 'Hadir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 13. Table berkas_putusans
CREATE TABLE IF NOT EXISTS berkas_putusans (
    id BIGSERIAL PRIMARY KEY,
    nomor_perkara VARCHAR(255) NOT NULL,
    file_asli VARCHAR(255) NOT NULL,
    file_anonim VARCHAR(255) NULL,
    is_anonim_selesai BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 14. Table delegasi_perkaras
CREATE TABLE IF NOT EXISTS delegasi_perkaras (
    id BIGSERIAL PRIMARY KEY,
    nomor_perkara VARCHAR(255) NOT NULL,
    pengadilan_tujuan VARCHAR(255) NOT NULL,
    status VARCHAR(50) DEFAULT 'Proses',
    file_surat_delegasi VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 15. Table e_berpadus
CREATE TABLE IF NOT EXISTS e_berpadus (
    id BIGSERIAL PRIMARY KEY,
    nomor_surat VARCHAR(255) NOT NULL,
    instansi_pengaju VARCHAR(255) NOT NULL,
    jenis_permohonan VARCHAR(255) NOT NULL,
    nama_tersangka VARCHAR(255) NOT NULL,
    status_persetujuan_hakim VARCHAR(50) DEFAULT 'Menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 16. Table e_raterangs
CREATE TABLE IF NOT EXISTS e_raterangs (
    id BIGSERIAL PRIMARY KEY,
    nomor_permohonan VARCHAR(255) NOT NULL,
    nik_pemohon VARCHAR(50) NOT NULL,
    nama_pemohon VARCHAR(255) NOT NULL,
    jenis_surat VARCHAR(255) NOT NULL,
    status_verifikasi VARCHAR(50) DEFAULT 'Belum Diverifikasi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 17. Table audit_logs
CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    action VARCHAR(255) NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT NULL,
    old_values JSONB NULL,
    new_values JSONB NULL,
    ip_address VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 18. Table personal_access_tokens
CREATE TABLE IF NOT EXISTS personal_access_tokens (
    id BIGSERIAL PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT NOT NULL,
    name TEXT NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==============================================================================
-- SEED DATA AWAL (USERS, HAKIM, RUANGAN, DEMO PERKARA)
-- ==============================================================================

-- 1. Data Hakim
INSERT INTO hakims (nama, nip) VALUES
('Budi Santoso, S.H., M.H.', '197001011995031001'),
('Siti Aminah, S.H., M.H.', '197502021999032002'),
('Agus Wijaya, S.H.', '198003032005011003')
ON CONFLICT (nip) DO NOTHING;

-- 2. Data Users (Password untuk semua akun: password)
-- Hash bcrypt Laravel: $2y$12$g9zF2fJ/7JdC0PzL7aLgO.Z9O3K6XzE8hW5qV7sT9uX1yZ3a5b7c
INSERT INTO users (name, email, password, role, hakim_id) VALUES
('Administrator', 'admin@pengadilan.go.id', '$2y$10$xzsIN6rexT5DHHhMaEyiQeOIAMbRz851abd2KzmnXapdFyRK03GuW', 'super_admin', NULL),
('Budi Santoso, S.H., M.H.', 'hakim@pengadilan.go.id', '$2y$10$xzsIN6rexT5DHHhMaEyiQeOIAMbRz851abd2KzmnXapdFyRK03GuW', 'hakim', 1),
('Warga Masyarakat', 'user@gmail.com', '$2y$10$xzsIN6rexT5DHHhMaEyiQeOIAMbRz851abd2KzmnXapdFyRK03GuW', 'masyarakat', NULL)
ON CONFLICT (email) DO UPDATE SET password = EXCLUDED.password;

-- 3. Data Ruang Sidang
INSERT INTO ruang_sidangs (nama_ruangan) VALUES
('Ruang Sidang Utama (Cakra)'),
('Ruang Sidang Anak (Tirta)'),
('Ruang Sidang Mediasi');

-- 4. Data e-Court Perkara
INSERT INTO e_court_perkaras (nomor_register, penggugat, tergugat, status, jenis_perdata, tanggal_daftar) VALUES
('12/Pdt.G/2026/PN.Xyz', 'PT Maju Mundur', 'CV Sumber Makmur', 'Sedang Di Proses', 'Gugatan', '2026-08-01'),
('15/Pdt.P/2026/PN.Xyz', 'Ahmad Fulan', '-', 'Diajukan', 'Permohonan', '2026-08-05'),
('18/Pdt.Sus-PHI/2026/PN.Xyz', 'Serikat Pekerja Sejahtera', 'PT Tekstil Global', 'Selesai', 'PHI', '2026-07-20');

-- 5. Data Perkara Pidana
INSERT INTO perkara_pidanas (nomor_perkara, terdakwa, jaksa, pasal, status) VALUES
('101/Pid.B/2026/PN.Xyz', 'Joko alias Jek', 'Iwan Setiawan, S.H.', 'Pasal 362 KUHP', 'Proses'),
('205/Pid.Sus/2026/PN.Xyz', 'Rina Melati', 'Dina Mariana, S.H.', 'UU Narkotika No. 35 Tahun 2009', 'Khusus'),
('30/Pid.Pra/2026/PN.Xyz', 'Doni Setiawan', 'KPK RI', 'Sah tidaknya penahanan', 'Pra Peradilan');
