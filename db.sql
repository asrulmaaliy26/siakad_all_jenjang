-- 2. Drop tabel jika sudah ada (urutan terbalik karena foreign key)
DROP TABLE IF EXISTS ta_skripsi;
DROP TABLE IF EXISTS ta_seminar_proposal;
DROP TABLE IF EXISTS ta_pengajuan_judul;

-- 3. Buat tabel dengan tipe data INT (bukan BIGINT) agar kompatibel
CREATE TABLE ta_pengajuan_judul (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_tahun_akademik INT NOT NULL,
    id_riwayat_pendidikan INT NOT NULL,
    judul VARCHAR(500) NOT NULL,
    abstrak TEXT,
    id_dosen_review INT NOT NULL,
    tgl_pengajuan DATE NOT NULL,
    tgl_ujian DATE,
    ruangan_ujian VARCHAR(50),
    tgl_acc_judul DATE,
    file VARCHAR(255),
    id_dosen_pembimbing_1 INT,
    id_dosen_pembimbing_2 INT,
    id_dosen_pembimbing_3 INT,
    status_dosen_1 ENUM('pending','setuju','ditolak','revisi') DEFAULT 'pending',
    status_dosen_2 ENUM('pending','setuju','ditolak','revisi') DEFAULT 'pending',
    status_dosen_3 ENUM('pending','setuju','ditolak','revisi') DEFAULT 'pending',
    nilai_dosen_1 DECIMAL(5,2),
    nilai_dosen_2 DECIMAL(5,2),
    nilai_dosen_3 DECIMAL(5,2),
    file_revisi_dosen_1 VARCHAR(255),
    file_revisi_dosen_2 VARCHAR(255),
    file_revisi_dosen_3 VARCHAR(255),
    ctt_revisi_dosen_1 TEXT,
    ctt_revisi_dosen_2 TEXT,
    ctt_revisi_dosen_3 TEXT,
    status ENUM('pending','disetujui','ditolak','revisi','selesai') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_tahun_akademik) REFERENCES tahun_akademik(id),
    FOREIGN KEY (id_riwayat_pendidikan) REFERENCES riwayat_pendidikan(id),
    FOREIGN KEY (id_dosen_review) REFERENCES dosen_data(id_dosen),
    FOREIGN KEY (id_dosen_pembimbing_1) REFERENCES dosen_data(id_dosen),
    FOREIGN KEY (id_dosen_pembimbing_2) REFERENCES dosen_data(id_dosen),
    FOREIGN KEY (id_dosen_pembimbing_3) REFERENCES dosen_data(id_dosen)
);

-- 4. Buat tabel ta_seminar_proposal
CREATE TABLE ta_seminar_proposal (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_tahun_akademik INT NOT NULL,
    id_riwayat_pendidikan INT NOT NULL,
    judul VARCHAR(500) NOT NULL,
    abstrak TEXT,
    tgl_pengajuan DATE NOT NULL,
    tgl_ujian DATE,
    ruangan_ujian VARCHAR(50),
    tgl_acc_judul DATE,
    file VARCHAR(255),
    id_dosen_pembimbing_1 INT,
    id_dosen_pembimbing_2 INT,
    id_dosen_pembimbing_3 INT,
    status_dosen_1 ENUM('pending','lulus','tidak_lulus','revisi') DEFAULT 'pending',
    status_dosen_2 ENUM('pending','lulus','tidak_lulus','revisi') DEFAULT 'pending',
    status_dosen_3 ENUM('pending','lulus','tidak_lulus','revisi') DEFAULT 'pending',
    nilai_dosen_1 DECIMAL(5,2),
    nilai_dosen_2 DECIMAL(5,2),
    nilai_dosen_3 DECIMAL(5,2),
    file_revisi_1 VARCHAR(255),
    file_revisi_2 VARCHAR(255),
    file_revisi_3 VARCHAR(255),
    ctt_revisi_dosen_1 TEXT,
    ctt_revisi_dosen_2 TEXT,
    ctt_revisi_dosen_3 TEXT,
    status ENUM('pending','disetujui','ditolak','revisi','selesai') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_tahun_akademik) REFERENCES tahun_akademik(id),
    FOREIGN KEY (id_riwayat_pendidikan) REFERENCES riwayat_pendidikan(id),
    FOREIGN KEY (id_dosen_pembimbing_1) REFERENCES dosen_data(id_dosen),
    FOREIGN KEY (id_dosen_pembimbing_2) REFERENCES dosen_data(id_dosen),
    FOREIGN KEY (id_dosen_pembimbing_3) REFERENCES dosen_data(id_dosen)
);

-- 5. Buat tabel ta_skripsi
CREATE TABLE ta_skripsi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_tahun_akademik INT NOT NULL,
    id_riwayat_pendidikan INT NOT NULL,
    judul VARCHAR(500) NOT NULL,
    abstrak TEXT,
    tgl_pengajuan DATE NOT NULL,
    tgl_ujian DATE,
    ruangan_ujian VARCHAR(50),
    tgl_acc_skripsi DATE,
    file VARCHAR(255),
    id_dosen_pembimbing_1 INT,
    id_dosen_pembimbing_2 INT,
    id_dosen_pembimbing_3 INT,
    status_dosen_1 ENUM('pending','lulus','tidak_lulus','revisi') DEFAULT 'pending',
    status_dosen_2 ENUM('pending','lulus','tidak_lulus','revisi') DEFAULT 'pending',
    status_dosen_3 ENUM('pending','lulus','tidak_lulus','revisi') DEFAULT 'pending',
    nilai_dosen_1 DECIMAL(5,2),
    nilai_dosen_2 DECIMAL(5,2),
    nilai_dosen_3 DECIMAL(5,2),
    file_revisi_1 VARCHAR(255),
    file_revisi_2 VARCHAR(255),
    file_revisi_3 VARCHAR(255),
    ctt_revisi_dosen_1 TEXT,
    ctt_revisi_dosen_2 TEXT,
    ctt_revisi_dosen_3 TEXT,
    nilai_akhir DECIMAL(5,2),
    status ENUM('pending','disetujui','ditolak','revisi','selesai') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_tahun_akademik) REFERENCES tahun_akademik(id),
    FOREIGN KEY (id_riwayat_pendidikan) REFERENCES riwayat_pendidikan(id),
    FOREIGN KEY (id_dosen_pembimbing_1) REFERENCES dosen_data(id_dosen),
    FOREIGN KEY (id_dosen_pembimbing_2) REFERENCES dosen_data(id_dosen),
    FOREIGN KEY (id_dosen_pembimbing_3) REFERENCES dosen_data(id_dosen)
);

-- 6. Buat index untuk optimasi
CREATE INDEX idx_pengajuan_status ON ta_pengajuan_judul(status);
CREATE INDEX idx_pengajuan_tgl ON ta_pengajuan_judul(tgl_pengajuan);
CREATE INDEX idx_pengajuan_mhs ON ta_pengajuan_judul(id_riwayat_pendidikan);
CREATE INDEX idx_pengajuan_reviewer ON ta_pengajuan_judul(id_dosen_review);

CREATE INDEX idx_proposal_status ON ta_seminar_proposal(status);
CREATE INDEX idx_proposal_tgl ON ta_seminar_proposal(tgl_pengajuan);
CREATE INDEX idx_proposal_mhs ON ta_seminar_proposal(id_riwayat_pendidikan);
CREATE INDEX idx_proposal_ujian ON ta_seminar_proposal(tgl_ujian);

CREATE INDEX idx_skripsi_status ON ta_skripsi(status);
CREATE INDEX idx_skripsi_tgl ON ta_skripsi(tgl_pengajuan);
CREATE INDEX idx_skripsi_mhs ON ta_skripsi(id_riwayat_pendidikan);
CREATE INDEX idx_skripsi_acc ON ta_skripsi(tgl_acc_skripsi);

-- 7. Verifikasi tabel yang baru dibuat
SHOW TABLES LIKE 'ta_%';

-- 8. Cek struktur tabel yang sudah dibuat
DESCRIBE ta_pengajuan_judul;
DESCRIBE ta_seminar_proposal;
DESCRIBE ta_skripsi;