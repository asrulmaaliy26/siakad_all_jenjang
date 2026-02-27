<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $tenants = '[]';
        $users = '[]';
        $userTenantPivot = '[]';
        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["ViewAny:AbsensiSiswa","View:AbsensiSiswa","Create:AbsensiSiswa","Update:AbsensiSiswa","Delete:AbsensiSiswa","Restore:AbsensiSiswa","ForceDelete:AbsensiSiswa","ForceDeleteAny:AbsensiSiswa","RestoreAny:AbsensiSiswa","Replicate:AbsensiSiswa","Reorder:AbsensiSiswa","ViewAny:AkademikKrs","View:AkademikKrs","Create:AkademikKrs","Update:AkademikKrs","Delete:AkademikKrs","Restore:AkademikKrs","ForceDelete:AkademikKrs","ForceDeleteAny:AkademikKrs","RestoreAny:AkademikKrs","Replicate:AkademikKrs","Reorder:AkademikKrs","ViewAny:DosenData","View:DosenData","Create:DosenData","Update:DosenData","Delete:DosenData","Restore:DosenData","ForceDelete:DosenData","ForceDeleteAny:DosenData","RestoreAny:DosenData","Replicate:DosenData","Reorder:DosenData","ViewAny:Fakultas","View:Fakultas","Create:Fakultas","Update:Fakultas","Delete:Fakultas","Restore:Fakultas","ForceDelete:Fakultas","ForceDeleteAny:Fakultas","RestoreAny:Fakultas","Replicate:Fakultas","Reorder:Fakultas","ViewAny:Jurusan","View:Jurusan","Create:Jurusan","Update:Jurusan","Delete:Jurusan","Restore:Jurusan","ForceDelete:Jurusan","ForceDeleteAny:Jurusan","RestoreAny:Jurusan","Replicate:Jurusan","Reorder:Jurusan","ViewAny:Kelas","View:Kelas","Create:Kelas","Update:Kelas","Delete:Kelas","Restore:Kelas","ForceDelete:Kelas","ForceDeleteAny:Kelas","RestoreAny:Kelas","Replicate:Kelas","Reorder:Kelas","ViewAny:Kurikulum","View:Kurikulum","Create:Kurikulum","Update:Kurikulum","Delete:Kurikulum","Restore:Kurikulum","ForceDelete:Kurikulum","ForceDeleteAny:Kurikulum","RestoreAny:Kurikulum","Replicate:Kurikulum","Reorder:Kurikulum","ViewAny:MataPelajaranKelasDistribusi","View:MataPelajaranKelasDistribusi","Create:MataPelajaranKelasDistribusi","Update:MataPelajaranKelasDistribusi","Delete:MataPelajaranKelasDistribusi","Restore:MataPelajaranKelasDistribusi","ForceDelete:MataPelajaranKelasDistribusi","ForceDeleteAny:MataPelajaranKelasDistribusi","RestoreAny:MataPelajaranKelasDistribusi","Replicate:MataPelajaranKelasDistribusi","Reorder:MataPelajaranKelasDistribusi","ViewAny:MataPelajaranKelas","View:MataPelajaranKelas","Create:MataPelajaranKelas","Update:MataPelajaranKelas","Delete:MataPelajaranKelas","Restore:MataPelajaranKelas","ForceDelete:MataPelajaranKelas","ForceDeleteAny:MataPelajaranKelas","RestoreAny:MataPelajaranKelas","Replicate:MataPelajaranKelas","Reorder:MataPelajaranKelas","ViewAny:MataPelajaranKurikulum","View:MataPelajaranKurikulum","Create:MataPelajaranKurikulum","Update:MataPelajaranKurikulum","Delete:MataPelajaranKurikulum","Restore:MataPelajaranKurikulum","ForceDelete:MataPelajaranKurikulum","ForceDeleteAny:MataPelajaranKurikulum","RestoreAny:MataPelajaranKurikulum","Replicate:MataPelajaranKurikulum","Reorder:MataPelajaranKurikulum","ViewAny:MataPelajaranMaster","View:MataPelajaranMaster","Create:MataPelajaranMaster","Update:MataPelajaranMaster","Delete:MataPelajaranMaster","Restore:MataPelajaranMaster","ForceDelete:MataPelajaranMaster","ForceDeleteAny:MataPelajaranMaster","RestoreAny:MataPelajaranMaster","Replicate:MataPelajaranMaster","Reorder:MataPelajaranMaster","ViewAny:PekanUjian","View:PekanUjian","Create:PekanUjian","Update:PekanUjian","Delete:PekanUjian","Restore:PekanUjian","ForceDelete:PekanUjian","ForceDeleteAny:PekanUjian","RestoreAny:PekanUjian","Replicate:PekanUjian","Reorder:PekanUjian","ViewAny:PengaturanPendaftaran","View:PengaturanPendaftaran","Create:PengaturanPendaftaran","Update:PengaturanPendaftaran","Delete:PengaturanPendaftaran","Restore:PengaturanPendaftaran","ForceDelete:PengaturanPendaftaran","ForceDeleteAny:PengaturanPendaftaran","RestoreAny:PengaturanPendaftaran","Replicate:PengaturanPendaftaran","Reorder:PengaturanPendaftaran","ViewAny:PertemuanKelas","View:PertemuanKelas","Create:PertemuanKelas","Update:PertemuanKelas","Delete:PertemuanKelas","Restore:PertemuanKelas","ForceDelete:PertemuanKelas","ForceDeleteAny:PertemuanKelas","RestoreAny:PertemuanKelas","Replicate:PertemuanKelas","Reorder:PertemuanKelas","ViewAny:ReferenceOption","View:ReferenceOption","Create:ReferenceOption","Update:ReferenceOption","Delete:ReferenceOption","Restore:ReferenceOption","ForceDelete:ReferenceOption","ForceDeleteAny:ReferenceOption","RestoreAny:ReferenceOption","Replicate:ReferenceOption","Reorder:ReferenceOption","ViewAny:RiwayatPendidikan","View:RiwayatPendidikan","Create:RiwayatPendidikan","Update:RiwayatPendidikan","Delete:RiwayatPendidikan","Restore:RiwayatPendidikan","ForceDelete:RiwayatPendidikan","ForceDeleteAny:RiwayatPendidikan","RestoreAny:RiwayatPendidikan","Replicate:RiwayatPendidikan","Reorder:RiwayatPendidikan","ViewAny:SiswaDataLJK","View:SiswaDataLJK","Create:SiswaDataLJK","Update:SiswaDataLJK","Delete:SiswaDataLJK","Restore:SiswaDataLJK","ForceDelete:SiswaDataLJK","ForceDeleteAny:SiswaDataLJK","RestoreAny:SiswaDataLJK","Replicate:SiswaDataLJK","Reorder:SiswaDataLJK","ViewAny:SiswaDataOrangTua","View:SiswaDataOrangTua","Create:SiswaDataOrangTua","Update:SiswaDataOrangTua","Delete:SiswaDataOrangTua","Restore:SiswaDataOrangTua","ForceDelete:SiswaDataOrangTua","ForceDeleteAny:SiswaDataOrangTua","RestoreAny:SiswaDataOrangTua","Replicate:SiswaDataOrangTua","Reorder:SiswaDataOrangTua","ViewAny:SiswaDataPendaftar","View:SiswaDataPendaftar","Create:SiswaDataPendaftar","Update:SiswaDataPendaftar","Delete:SiswaDataPendaftar","Restore:SiswaDataPendaftar","ForceDelete:SiswaDataPendaftar","ForceDeleteAny:SiswaDataPendaftar","RestoreAny:SiswaDataPendaftar","Replicate:SiswaDataPendaftar","Reorder:SiswaDataPendaftar","ViewAny:SiswaData","View:SiswaData","Create:SiswaData","Update:SiswaData","Delete:SiswaData","Restore:SiswaData","ForceDelete:SiswaData","ForceDeleteAny:SiswaData","RestoreAny:SiswaData","Replicate:SiswaData","Reorder:SiswaData","ViewAny:TahunAkademik","View:TahunAkademik","Create:TahunAkademik","Update:TahunAkademik","Delete:TahunAkademik","Restore:TahunAkademik","ForceDelete:TahunAkademik","ForceDeleteAny:TahunAkademik","RestoreAny:TahunAkademik","Replicate:TahunAkademik","Reorder:TahunAkademik","ViewAny:User","View:User","Create:User","Update:User","Delete:User","Restore:User","ForceDelete:User","ForceDeleteAny:User","RestoreAny:User","Replicate:User","Reorder:User","ViewAny:Role","View:Role","Create:Role","Update:Role","Delete:Role","Restore:Role","ForceDelete:Role","ForceDeleteAny:Role","RestoreAny:Role","Replicate:Role","Reorder:Role","DeleteAny:AbsensiSiswa","DeleteAny:AkademikKrs","DeleteAny:DosenData","DeleteAny:Fakultas","DeleteAny:Jurusan","DeleteAny:Kelas","DeleteAny:Kurikulum","DeleteAny:MataPelajaranKelasDistribusi","DeleteAny:MataPelajaranKelas","DeleteAny:MataPelajaranKurikulum","DeleteAny:MataPelajaranMaster","DeleteAny:PekanUjian","DeleteAny:PengaturanPendaftaran","DeleteAny:PertemuanKelas","DeleteAny:ReferenceOption","DeleteAny:RiwayatPendidikan","DeleteAny:SiswaDataLJK","DeleteAny:SiswaDataOrangTua","DeleteAny:SiswaDataPendaftar","DeleteAny:SiswaData","DeleteAny:TahunAkademik","DeleteAny:User","DeleteAny:Role","ViewAny:TaPengajuanJudul","View:TaPengajuanJudul","Create:TaPengajuanJudul","Update:TaPengajuanJudul","Delete:TaPengajuanJudul","DeleteAny:TaPengajuanJudul","Restore:TaPengajuanJudul","ForceDelete:TaPengajuanJudul","ForceDeleteAny:TaPengajuanJudul","RestoreAny:TaPengajuanJudul","Replicate:TaPengajuanJudul","Reorder:TaPengajuanJudul","ViewAny:TaSeminarProposal","View:TaSeminarProposal","Create:TaSeminarProposal","Update:TaSeminarProposal","Delete:TaSeminarProposal","DeleteAny:TaSeminarProposal","Restore:TaSeminarProposal","ForceDelete:TaSeminarProposal","ForceDeleteAny:TaSeminarProposal","RestoreAny:TaSeminarProposal","Replicate:TaSeminarProposal","Reorder:TaSeminarProposal","ViewAny:TaSkripsi","View:TaSkripsi","Create:TaSkripsi","Update:TaSkripsi","Delete:TaSkripsi","DeleteAny:TaSkripsi","Restore:TaSkripsi","ForceDelete:TaSkripsi","ForceDeleteAny:TaSkripsi","RestoreAny:TaSkripsi","Replicate:TaSkripsi","Reorder:TaSkripsi","ViewAny:Ulasan","View:Ulasan","Create:Ulasan","Update:Ulasan","Delete:Ulasan","DeleteAny:Ulasan","Restore:Ulasan","ForceDelete:Ulasan","ForceDeleteAny:Ulasan","RestoreAny:Ulasan","Replicate:Ulasan","Reorder:Ulasan","ViewAny:PengajuanSurat","View:PengajuanSurat","Create:PengajuanSurat","Update:PengajuanSurat","Delete:PengajuanSurat","DeleteAny:PengajuanSurat","Restore:PengajuanSurat","ForceDelete:PengajuanSurat","ForceDeleteAny:PengajuanSurat","RestoreAny:PengajuanSurat","Replicate:PengajuanSurat","Reorder:PengajuanSurat","ViewAny:ReferalCode","View:ReferalCode","Create:ReferalCode","Update:ReferalCode","Delete:ReferalCode","DeleteAny:ReferalCode","Restore:ReferalCode","ForceDelete:ReferalCode","ForceDeleteAny:ReferalCode","RestoreAny:ReferalCode","Replicate:ReferalCode","Reorder:ReferalCode","View:Dashboard","View:SiswaOverviewStats","View:JurusanStudentStats","View:AcademicStatsWidget","View:RangkingMahasiswaWidget","ViewAny:PeriodeWisuda","View:PeriodeWisuda","Create:PeriodeWisuda","Update:PeriodeWisuda","Delete:PeriodeWisuda","DeleteAny:PeriodeWisuda","Restore:PeriodeWisuda","ForceDelete:PeriodeWisuda","ForceDeleteAny:PeriodeWisuda","RestoreAny:PeriodeWisuda","Replicate:PeriodeWisuda","Reorder:PeriodeWisuda","ViewAny:WisudaMahasiswa","View:WisudaMahasiswa","Create:WisudaMahasiswa","Update:WisudaMahasiswa","Delete:WisudaMahasiswa","DeleteAny:WisudaMahasiswa","Restore:WisudaMahasiswa","ForceDelete:WisudaMahasiswa","ForceDeleteAny:WisudaMahasiswa","RestoreAny:WisudaMahasiswa","Replicate:WisudaMahasiswa","Reorder:WisudaMahasiswa","View:WisudaMahasiswaPage","ViewAny:LibraryAuthor","View:LibraryAuthor","Create:LibraryAuthor","Update:LibraryAuthor","Delete:LibraryAuthor","DeleteAny:LibraryAuthor","Restore:LibraryAuthor","ForceDelete:LibraryAuthor","ForceDeleteAny:LibraryAuthor","RestoreAny:LibraryAuthor","Replicate:LibraryAuthor","Reorder:LibraryAuthor","ViewAny:LibraryBook","View:LibraryBook","Create:LibraryBook","Update:LibraryBook","Delete:LibraryBook","DeleteAny:LibraryBook","Restore:LibraryBook","ForceDelete:LibraryBook","ForceDeleteAny:LibraryBook","RestoreAny:LibraryBook","Replicate:LibraryBook","Reorder:LibraryBook","ViewAny:LibraryCategory","View:LibraryCategory","Create:LibraryCategory","Update:LibraryCategory","Delete:LibraryCategory","DeleteAny:LibraryCategory","Restore:LibraryCategory","ForceDelete:LibraryCategory","ForceDeleteAny:LibraryCategory","RestoreAny:LibraryCategory","Replicate:LibraryCategory","Reorder:LibraryCategory","ViewAny:LibraryLoan","View:LibraryLoan","Create:LibraryLoan","Update:LibraryLoan","Delete:LibraryLoan","DeleteAny:LibraryLoan","Restore:LibraryLoan","ForceDelete:LibraryLoan","ForceDeleteAny:LibraryLoan","RestoreAny:LibraryLoan","Replicate:LibraryLoan","Reorder:LibraryLoan","ViewAny:LibraryProcurement","View:LibraryProcurement","Create:LibraryProcurement","Update:LibraryProcurement","Delete:LibraryProcurement","DeleteAny:LibraryProcurement","Restore:LibraryProcurement","ForceDelete:LibraryProcurement","ForceDeleteAny:LibraryProcurement","RestoreAny:LibraryProcurement","Replicate:LibraryProcurement","Reorder:LibraryProcurement","ViewAny:LibraryPublisher","View:LibraryPublisher","Create:LibraryPublisher","Update:LibraryPublisher","Delete:LibraryPublisher","DeleteAny:LibraryPublisher","Restore:LibraryPublisher","ForceDelete:LibraryPublisher","ForceDeleteAny:LibraryPublisher","RestoreAny:LibraryPublisher","Replicate:LibraryPublisher","Reorder:LibraryPublisher","ViewAny:LibraryVisit","View:LibraryVisit","Create:LibraryVisit","Update:LibraryVisit","Delete:LibraryVisit","DeleteAny:LibraryVisit","Restore:LibraryVisit","ForceDelete:LibraryVisit","ForceDeleteAny:LibraryVisit","RestoreAny:LibraryVisit","Replicate:LibraryVisit","Reorder:LibraryVisit","View:LibraryStatistics","View:LibraryLoanChart","View:LibraryOverviewStats","View:LibraryProcurementChart","View:LibraryVisitorChart"]},{"name":"pengajar","guard_name":"web","permissions":["ViewAny:AbsensiSiswa","View:AbsensiSiswa","Create:AbsensiSiswa","Update:AbsensiSiswa","Delete:AbsensiSiswa","Restore:AbsensiSiswa","ForceDelete:AbsensiSiswa","ForceDeleteAny:AbsensiSiswa","RestoreAny:AbsensiSiswa","Replicate:AbsensiSiswa","Reorder:AbsensiSiswa","ViewAny:AkademikKrs","View:AkademikKrs","Create:AkademikKrs","Update:AkademikKrs","Delete:AkademikKrs","ViewAny:DosenData","View:DosenData","Update:DosenData","ViewAny:MataPelajaranKelas","View:MataPelajaranKelas","Update:MataPelajaranKelas","Restore:MataPelajaranKelas","ForceDelete:MataPelajaranKelas","RestoreAny:MataPelajaranKelas","Replicate:MataPelajaranKelas","Reorder:MataPelajaranKelas","ViewAny:SiswaDataLJK","View:SiswaDataLJK","Update:SiswaDataLJK","Restore:SiswaDataLJK","ForceDelete:SiswaDataLJK","RestoreAny:SiswaDataLJK","Replicate:SiswaDataLJK","Reorder:SiswaDataLJK","ViewAny:TaPengajuanJudul","View:TaPengajuanJudul","Create:TaPengajuanJudul","Update:TaPengajuanJudul","Delete:TaPengajuanJudul","DeleteAny:TaPengajuanJudul","Restore:TaPengajuanJudul","ForceDelete:TaPengajuanJudul","ForceDeleteAny:TaPengajuanJudul","RestoreAny:TaPengajuanJudul","Replicate:TaPengajuanJudul","Reorder:TaPengajuanJudul","ViewAny:TaSeminarProposal","View:TaSeminarProposal","Create:TaSeminarProposal","Update:TaSeminarProposal","Delete:TaSeminarProposal","DeleteAny:TaSeminarProposal","Restore:TaSeminarProposal","ForceDelete:TaSeminarProposal","ForceDeleteAny:TaSeminarProposal","RestoreAny:TaSeminarProposal","Replicate:TaSeminarProposal","Reorder:TaSeminarProposal","ViewAny:TaSkripsi","View:TaSkripsi","Create:TaSkripsi","Update:TaSkripsi","Delete:TaSkripsi","DeleteAny:TaSkripsi","Restore:TaSkripsi","ForceDelete:TaSkripsi","ForceDeleteAny:TaSkripsi","RestoreAny:TaSkripsi","Replicate:TaSkripsi","Reorder:TaSkripsi"]},{"name":"murid","guard_name":"web","permissions":["ViewAny:AbsensiSiswa","View:AbsensiSiswa","ViewAny:AkademikKrs","View:AkademikKrs","Update:AkademikKrs","Restore:AkademikKrs","View:Kelas","View:Kurikulum","View:MataPelajaranKelasDistribusi","ViewAny:MataPelajaranKelas","View:MataPelajaranKelas","Delete:MataPelajaranKelas","Restore:MataPelajaranKelas","ForceDelete:MataPelajaranKelas","ForceDeleteAny:MataPelajaranKelas","RestoreAny:MataPelajaranKelas","Replicate:MataPelajaranKelas","Reorder:MataPelajaranKelas","ViewAny:PekanUjian","View:PekanUjian","ViewAny:SiswaDataLJK","View:SiswaDataLJK","ViewAny:SiswaDataOrangTua","View:SiswaDataOrangTua","Update:SiswaDataOrangTua","ViewAny:SiswaDataPendaftar","View:SiswaDataPendaftar","Update:SiswaDataPendaftar","ViewAny:SiswaData","View:SiswaData","Update:SiswaData","DeleteAny:MataPelajaranKelas","ViewAny:TaPengajuanJudul","View:TaPengajuanJudul","Create:TaPengajuanJudul","ViewAny:TaSeminarProposal","View:TaSeminarProposal","Create:TaSeminarProposal","ViewAny:TaSkripsi","View:TaSkripsi","Create:TaSkripsi","ViewAny:Ulasan","View:Ulasan","Create:Ulasan","Update:Ulasan","Delete:Ulasan","DeleteAny:Ulasan","Restore:Ulasan","ForceDelete:Ulasan","ForceDeleteAny:Ulasan","RestoreAny:Ulasan","Replicate:Ulasan","Reorder:Ulasan","View:Dashboard","View:SiswaOverviewStats","View:JurusanStudentStats","View:AcademicStatsWidget","View:RangkingMahasiswaWidget","View:WisudaMahasiswaPage"]},{"name":"admin_jenjang_s1","guard_name":"web","permissions":["ViewAny:AbsensiSiswa","View:AbsensiSiswa","Create:AbsensiSiswa","Update:AbsensiSiswa","Delete:AbsensiSiswa","Restore:AbsensiSiswa","ForceDelete:AbsensiSiswa","ForceDeleteAny:AbsensiSiswa","RestoreAny:AbsensiSiswa","Replicate:AbsensiSiswa","Reorder:AbsensiSiswa","ViewAny:AkademikKrs","View:AkademikKrs","Create:AkademikKrs","Update:AkademikKrs","Delete:AkademikKrs","Restore:AkademikKrs","ForceDelete:AkademikKrs","ForceDeleteAny:AkademikKrs","RestoreAny:AkademikKrs","Replicate:AkademikKrs","Reorder:AkademikKrs","ViewAny:DosenData","View:DosenData","Create:DosenData","Update:DosenData","Delete:DosenData","Restore:DosenData","ForceDelete:DosenData","ForceDeleteAny:DosenData","RestoreAny:DosenData","Replicate:DosenData","Reorder:DosenData","ViewAny:Fakultas","View:Fakultas","Create:Fakultas","Update:Fakultas","Delete:Fakultas","Restore:Fakultas","ForceDelete:Fakultas","ForceDeleteAny:Fakultas","RestoreAny:Fakultas","Replicate:Fakultas","Reorder:Fakultas","ViewAny:JenjangPendidikan","View:JenjangPendidikan","Create:JenjangPendidikan","Update:JenjangPendidikan","Delete:JenjangPendidikan","Restore:JenjangPendidikan","ForceDelete:JenjangPendidikan","ForceDeleteAny:JenjangPendidikan","RestoreAny:JenjangPendidikan","Replicate:JenjangPendidikan","Reorder:JenjangPendidikan","ViewAny:Jurusan","View:Jurusan","Create:Jurusan","Update:Jurusan","Delete:Jurusan","Restore:Jurusan","ForceDelete:Jurusan","ForceDeleteAny:Jurusan","RestoreAny:Jurusan","Replicate:Jurusan","Reorder:Jurusan","ViewAny:Kelas","View:Kelas","Create:Kelas","Update:Kelas","Delete:Kelas","Restore:Kelas","ForceDelete:Kelas","ForceDeleteAny:Kelas","RestoreAny:Kelas","Replicate:Kelas","Reorder:Kelas","ViewAny:Kurikulum","View:Kurikulum","Create:Kurikulum","Update:Kurikulum","Delete:Kurikulum","Restore:Kurikulum","ForceDelete:Kurikulum","ForceDeleteAny:Kurikulum","RestoreAny:Kurikulum","Replicate:Kurikulum","Reorder:Kurikulum","ViewAny:MataPelajaranKelasDistribusi","View:MataPelajaranKelasDistribusi","Create:MataPelajaranKelasDistribusi","Update:MataPelajaranKelasDistribusi","Delete:MataPelajaranKelasDistribusi","Restore:MataPelajaranKelasDistribusi","ForceDelete:MataPelajaranKelasDistribusi","ForceDeleteAny:MataPelajaranKelasDistribusi","RestoreAny:MataPelajaranKelasDistribusi","Replicate:MataPelajaranKelasDistribusi","Reorder:MataPelajaranKelasDistribusi","ViewAny:MataPelajaranKelas","View:MataPelajaranKelas","Create:MataPelajaranKelas","Update:MataPelajaranKelas","Delete:MataPelajaranKelas","Restore:MataPelajaranKelas","ForceDelete:MataPelajaranKelas","ForceDeleteAny:MataPelajaranKelas","RestoreAny:MataPelajaranKelas","Replicate:MataPelajaranKelas","Reorder:MataPelajaranKelas","ViewAny:MataPelajaranKurikulum","View:MataPelajaranKurikulum","Create:MataPelajaranKurikulum","Update:MataPelajaranKurikulum","Delete:MataPelajaranKurikulum","Restore:MataPelajaranKurikulum","ForceDelete:MataPelajaranKurikulum","ForceDeleteAny:MataPelajaranKurikulum","RestoreAny:MataPelajaranKurikulum","Replicate:MataPelajaranKurikulum","Reorder:MataPelajaranKurikulum","ViewAny:MataPelajaranMaster","View:MataPelajaranMaster","Create:MataPelajaranMaster","Update:MataPelajaranMaster","Delete:MataPelajaranMaster","Restore:MataPelajaranMaster","ForceDelete:MataPelajaranMaster","ForceDeleteAny:MataPelajaranMaster","RestoreAny:MataPelajaranMaster","Replicate:MataPelajaranMaster","Reorder:MataPelajaranMaster","ViewAny:PekanUjian","View:PekanUjian","Create:PekanUjian","Update:PekanUjian","Delete:PekanUjian","Restore:PekanUjian","ForceDelete:PekanUjian","ForceDeleteAny:PekanUjian","RestoreAny:PekanUjian","Replicate:PekanUjian","Reorder:PekanUjian","ViewAny:PengaturanPendaftaran","View:PengaturanPendaftaran","Create:PengaturanPendaftaran","Update:PengaturanPendaftaran","Delete:PengaturanPendaftaran","Restore:PengaturanPendaftaran","ForceDelete:PengaturanPendaftaran","ForceDeleteAny:PengaturanPendaftaran","RestoreAny:PengaturanPendaftaran","Replicate:PengaturanPendaftaran","Reorder:PengaturanPendaftaran","ViewAny:PertemuanKelas","View:PertemuanKelas","Create:PertemuanKelas","Update:PertemuanKelas","Delete:PertemuanKelas","Restore:PertemuanKelas","ForceDelete:PertemuanKelas","ForceDeleteAny:PertemuanKelas","RestoreAny:PertemuanKelas","Replicate:PertemuanKelas","Reorder:PertemuanKelas","ViewAny:ReferenceOption","View:ReferenceOption","Create:ReferenceOption","Update:ReferenceOption","Delete:ReferenceOption","Restore:ReferenceOption","ForceDelete:ReferenceOption","ForceDeleteAny:ReferenceOption","RestoreAny:ReferenceOption","Replicate:ReferenceOption","Reorder:ReferenceOption","ViewAny:RiwayatPendidikan","View:RiwayatPendidikan","Create:RiwayatPendidikan","Update:RiwayatPendidikan","Delete:RiwayatPendidikan","Restore:RiwayatPendidikan","ForceDelete:RiwayatPendidikan","ForceDeleteAny:RiwayatPendidikan","RestoreAny:RiwayatPendidikan","Replicate:RiwayatPendidikan","Reorder:RiwayatPendidikan","ViewAny:SiswaDataLJK","View:SiswaDataLJK","Create:SiswaDataLJK","Update:SiswaDataLJK","Delete:SiswaDataLJK","Restore:SiswaDataLJK","ForceDelete:SiswaDataLJK","ForceDeleteAny:SiswaDataLJK","RestoreAny:SiswaDataLJK","Replicate:SiswaDataLJK","Reorder:SiswaDataLJK","ViewAny:SiswaDataOrangTua","View:SiswaDataOrangTua","Create:SiswaDataOrangTua","Update:SiswaDataOrangTua","Delete:SiswaDataOrangTua","Restore:SiswaDataOrangTua","ForceDelete:SiswaDataOrangTua","ForceDeleteAny:SiswaDataOrangTua","RestoreAny:SiswaDataOrangTua","Replicate:SiswaDataOrangTua","Reorder:SiswaDataOrangTua","ViewAny:SiswaDataPendaftar","View:SiswaDataPendaftar","Create:SiswaDataPendaftar","Update:SiswaDataPendaftar","Delete:SiswaDataPendaftar","Restore:SiswaDataPendaftar","ForceDelete:SiswaDataPendaftar","ForceDeleteAny:SiswaDataPendaftar","RestoreAny:SiswaDataPendaftar","Replicate:SiswaDataPendaftar","Reorder:SiswaDataPendaftar","ViewAny:SiswaData","View:SiswaData","Create:SiswaData","Update:SiswaData","Delete:SiswaData","Restore:SiswaData","ForceDelete:SiswaData","ForceDeleteAny:SiswaData","RestoreAny:SiswaData","Replicate:SiswaData","Reorder:SiswaData","ViewAny:TahunAkademik","View:TahunAkademik","Create:TahunAkademik","Update:TahunAkademik","Delete:TahunAkademik","Restore:TahunAkademik","ForceDelete:TahunAkademik","ForceDeleteAny:TahunAkademik","RestoreAny:TahunAkademik","Replicate:TahunAkademik","Reorder:TahunAkademik","ViewAny:User","View:User","Create:User","Update:User","Delete:User","Restore:User","ForceDelete:User","ForceDeleteAny:User","RestoreAny:User","Replicate:User","Reorder:User","DeleteAny:AbsensiSiswa","DeleteAny:AkademikKrs","DeleteAny:DosenData","DeleteAny:Fakultas","DeleteAny:JenjangPendidikan","DeleteAny:Jurusan","DeleteAny:Kelas","DeleteAny:Kurikulum","DeleteAny:MataPelajaranKelasDistribusi","DeleteAny:MataPelajaranKelas","DeleteAny:MataPelajaranKurikulum","DeleteAny:MataPelajaranMaster","DeleteAny:PekanUjian","DeleteAny:PengaturanPendaftaran","DeleteAny:PertemuanKelas","DeleteAny:ReferenceOption","DeleteAny:RiwayatPendidikan","DeleteAny:SiswaDataLJK","DeleteAny:SiswaDataOrangTua","DeleteAny:SiswaDataPendaftar","DeleteAny:SiswaData","DeleteAny:TahunAkademik","DeleteAny:User"]},{"name":"admin_jenjang_ma","guard_name":"web","permissions":["ViewAny:AbsensiSiswa","View:AbsensiSiswa","Create:AbsensiSiswa","Update:AbsensiSiswa","Delete:AbsensiSiswa","Restore:AbsensiSiswa","ForceDelete:AbsensiSiswa","ForceDeleteAny:AbsensiSiswa","RestoreAny:AbsensiSiswa","Replicate:AbsensiSiswa","Reorder:AbsensiSiswa","ViewAny:AkademikKrs","View:AkademikKrs","Create:AkademikKrs","Update:AkademikKrs","Delete:AkademikKrs","Restore:AkademikKrs","ForceDelete:AkademikKrs","ForceDeleteAny:AkademikKrs","RestoreAny:AkademikKrs","Replicate:AkademikKrs","Reorder:AkademikKrs","ViewAny:DosenData","View:DosenData","Create:DosenData","Update:DosenData","Delete:DosenData","Restore:DosenData","ForceDelete:DosenData","ForceDeleteAny:DosenData","RestoreAny:DosenData","Replicate:DosenData","Reorder:DosenData","ViewAny:Fakultas","View:Fakultas","Create:Fakultas","Update:Fakultas","Delete:Fakultas","Restore:Fakultas","ForceDelete:Fakultas","ForceDeleteAny:Fakultas","RestoreAny:Fakultas","Replicate:Fakultas","Reorder:Fakultas","ViewAny:JenjangPendidikan","View:JenjangPendidikan","Create:JenjangPendidikan","Update:JenjangPendidikan","Delete:JenjangPendidikan","Restore:JenjangPendidikan","ForceDelete:JenjangPendidikan","ForceDeleteAny:JenjangPendidikan","RestoreAny:JenjangPendidikan","Replicate:JenjangPendidikan","Reorder:JenjangPendidikan","ViewAny:Jurusan","View:Jurusan","Create:Jurusan","Update:Jurusan","Delete:Jurusan","Restore:Jurusan","ForceDelete:Jurusan","ForceDeleteAny:Jurusan","RestoreAny:Jurusan","Replicate:Jurusan","Reorder:Jurusan","ViewAny:Kelas","View:Kelas","Create:Kelas","Update:Kelas","Delete:Kelas","Restore:Kelas","ForceDelete:Kelas","ForceDeleteAny:Kelas","RestoreAny:Kelas","Replicate:Kelas","Reorder:Kelas","ViewAny:Kurikulum","View:Kurikulum","Create:Kurikulum","Update:Kurikulum","Delete:Kurikulum","Restore:Kurikulum","ForceDelete:Kurikulum","ForceDeleteAny:Kurikulum","RestoreAny:Kurikulum","Replicate:Kurikulum","Reorder:Kurikulum","ViewAny:MataPelajaranKelasDistribusi","View:MataPelajaranKelasDistribusi","Create:MataPelajaranKelasDistribusi","Update:MataPelajaranKelasDistribusi","Delete:MataPelajaranKelasDistribusi","Restore:MataPelajaranKelasDistribusi","ForceDelete:MataPelajaranKelasDistribusi","ForceDeleteAny:MataPelajaranKelasDistribusi","RestoreAny:MataPelajaranKelasDistribusi","Replicate:MataPelajaranKelasDistribusi","Reorder:MataPelajaranKelasDistribusi","ViewAny:MataPelajaranKelas","View:MataPelajaranKelas","Create:MataPelajaranKelas","Update:MataPelajaranKelas","Delete:MataPelajaranKelas","Restore:MataPelajaranKelas","ForceDelete:MataPelajaranKelas","ForceDeleteAny:MataPelajaranKelas","RestoreAny:MataPelajaranKelas","Replicate:MataPelajaranKelas","Reorder:MataPelajaranKelas","ViewAny:MataPelajaranKurikulum","View:MataPelajaranKurikulum","Create:MataPelajaranKurikulum","Update:MataPelajaranKurikulum","Delete:MataPelajaranKurikulum","Restore:MataPelajaranKurikulum","ForceDelete:MataPelajaranKurikulum","ForceDeleteAny:MataPelajaranKurikulum","RestoreAny:MataPelajaranKurikulum","Replicate:MataPelajaranKurikulum","Reorder:MataPelajaranKurikulum","ViewAny:MataPelajaranMaster","View:MataPelajaranMaster","Create:MataPelajaranMaster","Update:MataPelajaranMaster","Delete:MataPelajaranMaster","Restore:MataPelajaranMaster","ForceDelete:MataPelajaranMaster","ForceDeleteAny:MataPelajaranMaster","RestoreAny:MataPelajaranMaster","Replicate:MataPelajaranMaster","Reorder:MataPelajaranMaster","ViewAny:PekanUjian","View:PekanUjian","Create:PekanUjian","Update:PekanUjian","Delete:PekanUjian","Restore:PekanUjian","ForceDelete:PekanUjian","ForceDeleteAny:PekanUjian","RestoreAny:PekanUjian","Replicate:PekanUjian","Reorder:PekanUjian","ViewAny:PengaturanPendaftaran","View:PengaturanPendaftaran","Create:PengaturanPendaftaran","Update:PengaturanPendaftaran","Delete:PengaturanPendaftaran","Restore:PengaturanPendaftaran","ForceDelete:PengaturanPendaftaran","ForceDeleteAny:PengaturanPendaftaran","RestoreAny:PengaturanPendaftaran","Replicate:PengaturanPendaftaran","Reorder:PengaturanPendaftaran","ViewAny:PertemuanKelas","View:PertemuanKelas","Create:PertemuanKelas","Update:PertemuanKelas","Delete:PertemuanKelas","Restore:PertemuanKelas","ForceDelete:PertemuanKelas","ForceDeleteAny:PertemuanKelas","RestoreAny:PertemuanKelas","Replicate:PertemuanKelas","Reorder:PertemuanKelas","ViewAny:ReferenceOption","View:ReferenceOption","Create:ReferenceOption","Update:ReferenceOption","Delete:ReferenceOption","Restore:ReferenceOption","ForceDelete:ReferenceOption","ForceDeleteAny:ReferenceOption","RestoreAny:ReferenceOption","Replicate:ReferenceOption","Reorder:ReferenceOption","ViewAny:RiwayatPendidikan","View:RiwayatPendidikan","Create:RiwayatPendidikan","Update:RiwayatPendidikan","Delete:RiwayatPendidikan","Restore:RiwayatPendidikan","ForceDelete:RiwayatPendidikan","ForceDeleteAny:RiwayatPendidikan","RestoreAny:RiwayatPendidikan","Replicate:RiwayatPendidikan","Reorder:RiwayatPendidikan","ViewAny:SiswaDataLJK","View:SiswaDataLJK","Create:SiswaDataLJK","Update:SiswaDataLJK","Delete:SiswaDataLJK","Restore:SiswaDataLJK","ForceDelete:SiswaDataLJK","ForceDeleteAny:SiswaDataLJK","RestoreAny:SiswaDataLJK","Replicate:SiswaDataLJK","Reorder:SiswaDataLJK","ViewAny:SiswaDataOrangTua","View:SiswaDataOrangTua","Create:SiswaDataOrangTua","Update:SiswaDataOrangTua","Delete:SiswaDataOrangTua","Restore:SiswaDataOrangTua","ForceDelete:SiswaDataOrangTua","ForceDeleteAny:SiswaDataOrangTua","RestoreAny:SiswaDataOrangTua","Replicate:SiswaDataOrangTua","Reorder:SiswaDataOrangTua","ViewAny:SiswaDataPendaftar","View:SiswaDataPendaftar","Create:SiswaDataPendaftar","Update:SiswaDataPendaftar","Delete:SiswaDataPendaftar","Restore:SiswaDataPendaftar","ForceDelete:SiswaDataPendaftar","ForceDeleteAny:SiswaDataPendaftar","RestoreAny:SiswaDataPendaftar","Replicate:SiswaDataPendaftar","Reorder:SiswaDataPendaftar","ViewAny:SiswaData","View:SiswaData","Create:SiswaData","Update:SiswaData","Delete:SiswaData","Restore:SiswaData","ForceDelete:SiswaData","ForceDeleteAny:SiswaData","RestoreAny:SiswaData","Replicate:SiswaData","Reorder:SiswaData","ViewAny:TahunAkademik","View:TahunAkademik","Create:TahunAkademik","Update:TahunAkademik","Delete:TahunAkademik","Restore:TahunAkademik","ForceDelete:TahunAkademik","ForceDeleteAny:TahunAkademik","RestoreAny:TahunAkademik","Replicate:TahunAkademik","Reorder:TahunAkademik","ViewAny:User","View:User","Create:User","Update:User","Delete:User","Restore:User","ForceDelete:User","ForceDeleteAny:User","RestoreAny:User","Replicate:User","Reorder:User","DeleteAny:AbsensiSiswa","DeleteAny:AkademikKrs","DeleteAny:DosenData","DeleteAny:Fakultas","DeleteAny:JenjangPendidikan","DeleteAny:Jurusan","DeleteAny:Kelas","DeleteAny:Kurikulum","DeleteAny:MataPelajaranKelasDistribusi","DeleteAny:MataPelajaranKelas","DeleteAny:MataPelajaranKurikulum","DeleteAny:MataPelajaranMaster","DeleteAny:PekanUjian","DeleteAny:PengaturanPendaftaran","DeleteAny:PertemuanKelas","DeleteAny:ReferenceOption","DeleteAny:RiwayatPendidikan","DeleteAny:SiswaDataLJK","DeleteAny:SiswaDataOrangTua","DeleteAny:SiswaDataPendaftar","DeleteAny:SiswaData","DeleteAny:TahunAkademik","DeleteAny:User"]},{"name":"admin_jenjang_smp","guard_name":"web","permissions":["ViewAny:User","View:User","Create:User","Update:User","Delete:User","Restore:User","ForceDelete:User","ForceDeleteAny:User","RestoreAny:User","Replicate:User","Reorder:User","DeleteAny:User"]},{"name":"kaprodi","guard_name":"web","permissions":["ViewAny:TaPengajuanJudul","View:TaPengajuanJudul","Create:TaPengajuanJudul","Update:TaPengajuanJudul","Delete:TaPengajuanJudul","DeleteAny:TaPengajuanJudul","Restore:TaPengajuanJudul","ForceDelete:TaPengajuanJudul","ForceDeleteAny:TaPengajuanJudul","RestoreAny:TaPengajuanJudul","Replicate:TaPengajuanJudul","Reorder:TaPengajuanJudul","ViewAny:TaSeminarProposal","View:TaSeminarProposal","Create:TaSeminarProposal","Update:TaSeminarProposal","Delete:TaSeminarProposal","DeleteAny:TaSeminarProposal","Restore:TaSeminarProposal","ForceDelete:TaSeminarProposal","ForceDeleteAny:TaSeminarProposal","RestoreAny:TaSeminarProposal","Replicate:TaSeminarProposal","Reorder:TaSeminarProposal","ViewAny:TaSkripsi","View:TaSkripsi","Create:TaSkripsi","Update:TaSkripsi","Delete:TaSkripsi","DeleteAny:TaSkripsi","Restore:TaSkripsi","ForceDelete:TaSkripsi","ForceDeleteAny:TaSkripsi","RestoreAny:TaSkripsi","Replicate:TaSkripsi","Reorder:TaSkripsi"]},{"name":"pendaftar","guard_name":"web","permissions":["ViewAny:SiswaDataPendaftar","View:SiswaDataPendaftar","Create:SiswaDataPendaftar","Update:SiswaDataPendaftar","Delete:SiswaDataPendaftar","Restore:SiswaDataPendaftar","ForceDelete:SiswaDataPendaftar","ForceDeleteAny:SiswaDataPendaftar","RestoreAny:SiswaDataPendaftar","Replicate:SiswaDataPendaftar","Reorder:SiswaDataPendaftar","DeleteAny:SiswaDataPendaftar"]}]';
        $directPermissions = '{"336":{"name":"ViewAny:SuratCuti","guard_name":"web"},"337":{"name":"View:SuratCuti","guard_name":"web"},"338":{"name":"Create:SuratCuti","guard_name":"web"},"339":{"name":"Update:SuratCuti","guard_name":"web"},"340":{"name":"Delete:SuratCuti","guard_name":"web"},"341":{"name":"DeleteAny:SuratCuti","guard_name":"web"},"342":{"name":"Restore:SuratCuti","guard_name":"web"},"343":{"name":"ForceDelete:SuratCuti","guard_name":"web"},"344":{"name":"ForceDeleteAny:SuratCuti","guard_name":"web"},"345":{"name":"RestoreAny:SuratCuti","guard_name":"web"},"346":{"name":"Replicate:SuratCuti","guard_name":"web"},"347":{"name":"Reorder:SuratCuti","guard_name":"web"}}';

        // 1. Seed tenants first (if present)
        if (! blank($tenants) && $tenants !== '[]') {
            static::seedTenants($tenants);
        }

        // 2. Seed roles with permissions
        static::makeRolesWithPermissions($rolesWithPermissions);

        // 3. Seed direct permissions
        static::makeDirectPermissions($directPermissions);

        // 4. Seed users with their roles/permissions (if present)
        if (! blank($users) && $users !== '[]') {
            static::seedUsers($users);
        }

        // 5. Seed user-tenant pivot (if present)
        if (! blank($userTenantPivot) && $userTenantPivot !== '[]') {
            static::seedUserTenantPivot($userTenantPivot);
        }

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function seedTenants(string $tenants): void
    {
        if (blank($tenantData = json_decode($tenants, true))) {
            return;
        }

        $tenantModel = '';
        if (blank($tenantModel)) {
            return;
        }

        foreach ($tenantData as $tenant) {
            $tenantModel::firstOrCreate(
                ['id' => $tenant['id']],
                $tenant
            );
        }
    }

    protected static function seedUsers(string $users): void
    {
        if (blank($userData = json_decode($users, true))) {
            return;
        }

        $userModel = 'App\Models\User';
        $tenancyEnabled = false;

        foreach ($userData as $data) {
            // Extract role/permission data before creating user
            $roles = $data['roles'] ?? [];
            $permissions = $data['permissions'] ?? [];
            $tenantRoles = $data['tenant_roles'] ?? [];
            $tenantPermissions = $data['tenant_permissions'] ?? [];
            unset($data['roles'], $data['permissions'], $data['tenant_roles'], $data['tenant_permissions']);

            $user = $userModel::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Handle tenancy mode - sync roles/permissions per tenant
            if ($tenancyEnabled && (! empty($tenantRoles) || ! empty($tenantPermissions))) {
                foreach ($tenantRoles as $tenantId => $roleNames) {
                    $contextId = $tenantId === '_global' ? null : $tenantId;
                    setPermissionsTeamId($contextId);
                    $user->syncRoles($roleNames);
                }

                foreach ($tenantPermissions as $tenantId => $permissionNames) {
                    $contextId = $tenantId === '_global' ? null : $tenantId;
                    setPermissionsTeamId($contextId);
                    $user->syncPermissions($permissionNames);
                }
            } else {
                // Non-tenancy mode
                if (! empty($roles)) {
                    $user->syncRoles($roles);
                }

                if (! empty($permissions)) {
                    $user->syncPermissions($permissions);
                }
            }
        }
    }

    protected static function seedUserTenantPivot(string $pivot): void
    {
        if (blank($pivotData = json_decode($pivot, true))) {
            return;
        }

        $pivotTable = '';
        if (blank($pivotTable)) {
            return;
        }

        foreach ($pivotData as $row) {
            $uniqueKeys = [];

            if (isset($row['user_id'])) {
                $uniqueKeys['user_id'] = $row['user_id'];
            }

            $tenantForeignKey = 'team_id';
            if (! blank($tenantForeignKey) && isset($row[$tenantForeignKey])) {
                $uniqueKeys[$tenantForeignKey] = $row[$tenantForeignKey];
            }

            if (! empty($uniqueKeys)) {
                DB::table($pivotTable)->updateOrInsert($uniqueKeys, $row);
            }
        }
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            return;
        }

        /** @var \Illuminate\Database\Eloquent\Model $roleModel */
        $roleModel = Utils::getRoleModel();
        /** @var \Illuminate\Database\Eloquent\Model $permissionModel */
        $permissionModel = Utils::getPermissionModel();

        $tenancyEnabled = false;
        $teamForeignKey = 'team_id';

        foreach ($rolePlusPermissions as $rolePlusPermission) {
            $tenantId = $rolePlusPermission[$teamForeignKey] ?? null;

            // Set tenant context for role creation and permission sync
            if ($tenancyEnabled) {
                setPermissionsTeamId($tenantId);
            }

            $roleData = [
                'name' => $rolePlusPermission['name'],
                'guard_name' => $rolePlusPermission['guard_name'],
            ];

            // Include tenant ID in role data (can be null for global roles)
            if ($tenancyEnabled && ! blank($teamForeignKey)) {
                $roleData[$teamForeignKey] = $tenantId;
            }

            $role = $roleModel::firstOrCreate($roleData);

            if (! blank($rolePlusPermission['permissions'])) {
                $permissionModels = collect($rolePlusPermission['permissions'])
                    ->map(fn ($permission) => $permissionModel::firstOrCreate([
                        'name' => $permission,
                        'guard_name' => $rolePlusPermission['guard_name'],
                    ]))
                    ->all();

                $role->syncPermissions($permissionModels);
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (blank($permissions = json_decode($directPermissions, true))) {
            return;
        }

        /** @var \Illuminate\Database\Eloquent\Model $permissionModel */
        $permissionModel = Utils::getPermissionModel();

        foreach ($permissions as $permission) {
            if ($permissionModel::whereName($permission['name'])->doesntExist()) {
                $permissionModel::create([
                    'name' => $permission['name'],
                    'guard_name' => $permission['guard_name'],
                ]);
            }
        }
    }
}
