<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Hakim;
use App\Models\RuangSidang;
use App\Models\ECourtPerkara;
use App\Models\PerkaraPidana;
use App\Models\JadwalSidang;
use App\Models\PresensiHakim;
use App\Models\BerkasPutusan;
use App\Models\DelegasiPerkara;
use App\Models\EBerpadu;
use App\Models\ERaterang;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data Hakim
        $hakim1 = Hakim::create(['nama' => 'Budi Santoso, S.H., M.H.', 'nip' => '197001011995031001']);
        $hakim2 = Hakim::create(['nama' => 'Siti Aminah, S.H., M.H.', 'nip' => '197502021999032002']);
        $hakim3 = Hakim::create(['nama' => 'Agus Wijaya, S.H.', 'nip' => '198003032005011003']);

        // 1.1 Data User (RBAC)
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@pengadilan.go.id',
            'password' => Hash::make('password'),
            'role' => 'super_admin'
        ]);

        User::create([
            'name' => 'Budi Santoso, S.H., M.H.',
            'email' => 'hakim@pengadilan.go.id',
            'password' => Hash::make('password'),
            'role' => 'hakim',
            'hakim_id' => $hakim1->id
        ]);

        User::create([
            'name' => 'Warga Masyarakat',
            'email' => 'user@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'masyarakat'
        ]);

        // 2. Ruang Sidang
        $ruang1 = RuangSidang::create(['nama_ruangan' => 'Ruang Sidang Utama (Cakra)']);
        $ruang2 = RuangSidang::create(['nama_ruangan' => 'Ruang Sidang Anak (Tirta)']);
        $ruang3 = RuangSidang::create(['nama_ruangan' => 'Ruang Sidang Mediasi']);

        // 3. Perdata (ECourtPerkara)
        ECourtPerkara::create([
            'nomor_register' => '12/Pdt.G/2026/PN.Xyz',
            'penggugat' => 'PT Maju Mundur',
            'tergugat' => 'CV Sumber Makmur',
            'status' => 'Sedang Di Proses',
            'jenis_perdata' => 'Gugatan',
            'tanggal_daftar' => '2026-08-01'
        ]);
        ECourtPerkara::create([
            'nomor_register' => '15/Pdt.P/2026/PN.Xyz',
            'penggugat' => 'Ahmad Fulan',
            'tergugat' => '-',
            'status' => 'Diajukan',
            'jenis_perdata' => 'Permohonan',
            'tanggal_daftar' => '2026-08-05'
        ]);
        ECourtPerkara::create([
            'nomor_register' => '18/Pdt.Sus-PHI/2026/PN.Xyz',
            'penggugat' => 'Serikat Pekerja Sejahtera',
            'tergugat' => 'PT Tekstil Global',
            'status' => 'Selesai',
            'jenis_perdata' => 'PHI',
            'tanggal_daftar' => '2026-07-20'
        ]);

        // 4. Pidana (PerkaraPidana)
        PerkaraPidana::create([
            'nomor_perkara' => '101/Pid.B/2026/PN.Xyz',
            'terdakwa' => 'Joko alias Jek',
            'jaksa' => 'Iwan Setiawan, S.H.',
            'pasal' => 'Pasal 362 KUHP',
            'status' => 'Proses'
        ]);
        PerkaraPidana::create([
            'nomor_perkara' => '205/Pid.Sus/2026/PN.Xyz',
            'terdakwa' => 'Rina Melati',
            'jaksa' => 'Dina Mariana, S.H.',
            'pasal' => 'UU Narkotika No. 35 Tahun 2009',
            'status' => 'Khusus'
        ]);
        PerkaraPidana::create([
            'nomor_perkara' => '30/Pid.Pra/2026/PN.Xyz',
            'terdakwa' => 'Doni Setiawan',
            'jaksa' => 'KPK RI',
            'pasal' => 'Sah tidaknya penahanan',
            'status' => 'Pra Peradilan'
        ]);

        // 5. Jadwal Sidang
        JadwalSidang::create([
            'nomor_perkara' => '12/Pdt.G/2026/PN.Xyz',
            'hakim_id' => $hakim1->id,
            'ruang_sidang_id' => $ruang1->id,
            'waktu_mulai' => '2026-08-25 09:00:00',
            'waktu_selesai' => '2026-08-25 11:00:00',
            'status_relaas' => 'Relaas Siap/Patut'
        ]);
        JadwalSidang::create([
            'nomor_perkara' => '101/Pid.B/2026/PN.Xyz',
            'hakim_id' => $hakim2->id,
            'ruang_sidang_id' => $ruang2->id,
            'waktu_mulai' => '2026-08-26 13:00:00',
            'waktu_selesai' => '2026-08-26 15:00:00',
            'status_relaas' => 'Belum Dipanggil'
        ]);

        // 6. Kehadiran Hakim (PresensiHakim)
        PresensiHakim::create([
            'hakim_id' => $hakim1->id,
            'tanggal' => date('Y-m-d'),
            'status' => 'Hadir'
        ]);
        PresensiHakim::create([
            'hakim_id' => $hakim2->id,
            'tanggal' => date('Y-m-d'),
            'status' => 'Cuti'
        ]);

        // 7. Berkas Putusan
        BerkasPutusan::create([
            'nomor_perkara' => '18/Pdt.Sus-PHI/2026/PN.Xyz',
            'file_asli' => 'putusan/dummy1.pdf',
            'file_anonim' => 'putusan/anonim_dummy1.pdf',
            'is_anonim_selesai' => true
        ]);
        BerkasPutusan::create([
            'nomor_perkara' => '99/Pid.B/2026/PN.Xyz',
            'file_asli' => 'putusan/dummy2.pdf',
            'file_anonim' => null,
            'is_anonim_selesai' => false
        ]);

        // 8. Delegasi Perkara
        DelegasiPerkara::create([
            'nomor_perkara' => '50/Pdt.G/2026/PN.JktSel',
            'pengadilan_tujuan' => 'PN Jakarta Selatan',
            'status' => 'Proses',
            'file_surat_delegasi' => null
        ]);
        DelegasiPerkara::create([
            'nomor_perkara' => '55/Pdt.G/2026/PN.Bdg',
            'pengadilan_tujuan' => 'PN Bandung',
            'status' => 'Selesai',
            'file_surat_delegasi' => 'surat/delegasi1.pdf'
        ]);

        // 9. E-Berpadu
        EBerpadu::create([
            'nomor_surat' => 'BPD/2026/08/501',
            'instansi_pengaju' => 'Polres Metro',
            'jenis_permohonan' => 'Penyitaan',
            'nama_tersangka' => 'Hasanudin',
            'status_persetujuan_hakim' => 'Menunggu'
        ]);
        EBerpadu::create([
            'nomor_surat' => 'BPD/2026/08/502',
            'instansi_pengaju' => 'Kejaksaan Negeri',
            'jenis_permohonan' => 'Perpanjangan Penahanan',
            'nama_tersangka' => 'Siti Zubaidah',
            'status_persetujuan_hakim' => 'Disetujui'
        ]);

        // 10. E-Raterang
        ERaterang::create([
            'nomor_permohonan' => 'SK/2026/08/22/001',
            'nik_pemohon' => '3171234567890001',
            'nama_pemohon' => 'Agus Salim',
            'jenis_surat' => 'Tidak Pernah Dipidana',
            'status_verifikasi' => 'Belum Diverifikasi'
        ]);
        ERaterang::create([
            'nomor_permohonan' => 'SK/2026/08/22/002',
            'nik_pemohon' => '3171234567890002',
            'nama_pemohon' => 'Dwi Lestari',
            'jenis_surat' => 'Tidak Sedang Dicabut Hak Pilihnya',
            'status_verifikasi' => 'Selesai'
        ]);
    }
}
