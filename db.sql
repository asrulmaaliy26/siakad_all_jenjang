


SELECT 
    ak.id AS id_krs,
    sd.nama AS nama_siswa,
    sd.nomor_induk AS nim,
    rp.angkatan,
    kls.semester,
    mpm.nama AS mata_pelajaran,
    d.nama AS dosen_pengampu,
    kls.ro_program_kelas AS program_kelas,
    sdl.nilai,
    sdl.created_at AS tanggal_input_nilai
FROM siswa_data_ljk sdl
INNER JOIN akademik_krs ak ON sdl.id_akademik_krs = ak.id
INNER JOIN riwayat_pendidikan rp ON ak.id_riwayat_pendidikan = rp.id
INNER JOIN siswa_data sd ON rp.id_siswa_data = sd.id
INNER JOIN mata_pelajaran_kelas mpk ON sdl.id_mata_pelajaran_kelas = mpk.id
INNER JOIN mata_pelajaran_kurikulum mpk_kur ON mpk.id_mata_pelajaran_kurikulum = mpk_kur.id
INNER JOIN mata_pelajaran_master mpm ON mpk_kur.id_mata_pelajaran_master = mpm.id
LEFT JOIN dosen_data d ON mpk.id_dosen_data = d.id
INNER JOIN kelas kls ON ak.id_kelas = kls.id
WHERE sdl.id_mata_pelajaran_kelas = 1
ORDER BY sdl.nilai DESC;

SELECT 
    -- Informasi KRS
    ak.id AS id_krs,
    ak.semester AS semester_krs,
    ak.status_aktif AS status_krs_aktif,
    ak.status_bayar AS status_pembayaran,
    ak.jumlah_sks,
    ak.tgl_krs AS tanggal_krs,
    
    -- Informasi Siswa
    sd.id AS id_siswa,
    sd.nama AS nama_siswa,
    sd.nomor_induk AS nim,
    sd.email AS email_siswa,
    
    -- Informasi Riwayat Pendidikan
    rp.id AS id_riwayat_pendidikan,
    rp.ro_status_siswa AS status_siswa,
    rp.angkatan AS angkatan,
    rp.tanggal_mulai AS tgl_mulai_studi,
    
    -- Informasi Jurusan/Prodi
    j.id AS id_jurusan,
    j.nama AS program_studi,
    f.nama AS fakultas,
    
    -- Informasi Kelas
    kls.id AS id_kelas,
    kls.semester AS semester_kelas,
    jp.nama AS jenjang_pendidikan,
    ta.nama AS tahun_akademik,
    ta.periode AS periode,
    
    -- Informasi Mata Pelajaran
    mpm.id AS id_mata_pelajaran,
    mpm.nama AS mata_pelajaran,
    mpm.bobot AS sks,
    mpm.jenis AS tipe_matkul,
    
    -- Informasi Dosen
    d.id AS id_dosen,
    d.nama AS dosen_pengampu,
    d.NIPDN AS nip_dosen,
    d.gelar_depan,
    d.gelar_belakang,
    
    -- Informasi Nilai (LJK)
    sdl.id AS id_ljk,
    sdl.nilai,
    sdl.created_at AS tgl_input_nilai,
    sdl.updated_at AS tgl_update_nilai
    
FROM siswa_data_ljk sdl
INNER JOIN akademik_krs ak ON sdl.id_akademik_krs = ak.id
INNER JOIN riwayat_pendidikan rp ON ak.id_riwayat_pendidikan = rp.id
INNER JOIN siswa_data sd ON rp.id_siswa_data = sd.id
INNER JOIN mata_pelajaran_kelas mpk ON sdl.id_mata_pelajaran_kelas = mpk.id
INNER JOIN mata_pelajaran_kurikulum mpk_kur ON mpk.id_mata_pelajaran_kurikulum = mpk_kur.id
INNER JOIN mata_pelajaran_master mpm ON mpk_kur.id_mata_pelajaran_master = mpm.id
LEFT JOIN dosen_data d ON mpk.id_dosen_data = d.id
INNER JOIN kelas kls ON ak.id_kelas = kls.id
LEFT JOIN jurusan j ON rp.id_jurusan = j.id
LEFT JOIN fakultas f ON j.id_fakultas = f.id
LEFT JOIN jenjang_pendidikan jp ON kls.id_jenjang_pendidikan = jp.id
LEFT JOIN tahun_akademik ta ON kls.id_tahun_akademik = ta.id
WHERE sdl.id_mata_pelajaran_kelas = 1
ORDER BY sdl.nilai DESC, sd.nama ASC;