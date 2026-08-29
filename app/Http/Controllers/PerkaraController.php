<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ECourtPerkara;
use App\Models\Hakim;
use App\Models\RuangSidang;
use App\Models\JadwalSidang;
use App\Models\BerkasPutusan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class PerkaraController extends Controller
{
    public function index()
    {
        $perkaras = ECourtPerkara::where('penggugat', auth()->user()->name)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('perkara.index', compact('perkaras'));
    }

    public function createMandiri()
    {
        return view('perkara.register');
    }

    public function storeMandiri(Request $request)
    {
        $request->validate([
            'kategori' => 'required|in:perdata_gugatan,perdata_permohonan,phi',
            'posisi_pihak' => 'required|in:penggugat,kuasa_hukum',
            'nik_penggugat' => 'required|string|max:30',
            'no_wa_penggugat' => 'required|string|max:30',
            'alamat_penggugat' => 'required|string',
            'nama_tergugat' => 'required|string|max:255',
            'alamat_tergugat' => 'required|string',
            'file_ktp' => 'required|file|image|max:5120', // Max 5MB
            'dokumen_gugatan' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        // Upload files
        $ktpPath = null;
        if ($request->hasFile('file_ktp')) {
            $ktpPath = $request->file('file_ktp')->store('ktp', 'public');
        }

        $gugatanPath = null;
        if ($request->hasFile('dokumen_gugatan')) {
            $gugatanPath = $request->file('dokumen_gugatan')->store('gugatan', 'public');
        }

        // Map jenis perdata
        $jenisPerdata = match($request->kategori) {
            'perdata_gugatan' => 'Gugatan',
            'perdata_permohonan' => 'Permohonan',
            'phi' => 'PHI',
            default => 'Gugatan',
        };

        // Create case in e_court_perkaras
        $perkara = ECourtPerkara::create([
            'nomor_register' => 'REG-EC-' . now()->format('Ymd') . '-' . rand(1000, 9999),
            'tanggal_daftar' => now(),
            'jenis_perdata' => $jenisPerdata,
            'penggugat' => auth()->user()->name,
            'tergugat' => $request->nama_tergugat,
            'status' => 'Diajukan',
            'nominal_panjar' => 150000.00,
            'status_pembayaran' => 'Belum Dibayar',
            'link_litigasi_online' => $gugatanPath ? asset('storage/' . $gugatanPath) : null,
            'nik_penggugat' => $request->nik_penggugat,
            'no_wa_penggugat' => $request->no_wa_penggugat,
            'alamat_penggugat' => $request->alamat_penggugat,
            'file_ktp' => $ktpPath ? asset('storage/' . $ktpPath) : null,
            'file_gugatan' => $gugatanPath ? asset('storage/' . $gugatanPath) : null,
            'status_bayar' => 'belum_dibayar',
            'status_verifikasi' => 'draft',
        ]);

        // Generate Virtual Account
        $perkara->update([
            'nomor_va' => '88708' . $perkara->id
        ]);

        return redirect()->route('perkara.index')->with('success', 'Registrasi perkara mandiri Anda berhasil diajukan dengan Nomor Register: ' . $perkara->nomor_register);
    }

    // Admin index
    public function adminIndex()
    {
        $perkaras = ECourtPerkara::orderBy('created_at', 'desc')->get();
        $hakims = Hakim::all();
        $ruangSidangs = RuangSidang::all();

        return view('admin.verifikasi', compact('perkaras', 'hakims', 'ruangSidangs'));
    }

    // Admin confirm payment
    public function adminConfirmPembayaran($id)
    {
        $perkara = ECourtPerkara::findOrFail($id);
        $perkara->update([
            'status_bayar' => 'lunas',
            'status_pembayaran' => 'Lunas'
        ]);

        return redirect()->back()->with('success', 'Konfirmasi pembayaran panjar lunas berhasil disimpan untuk Register: ' . $perkara->nomor_register);
    }

    // Admin verify case & schedule hearing
    public function adminVerify(Request $request, $id)
    {
        $request->validate([
            'nomor_perkara_resmi' => 'required|string|unique:e_court_perkaras,nomor_register|unique:jadwal_sidangs,nomor_perkara',
            'tanggal_sidang_pertama' => 'required|date',
            'ruang_sidang_id' => 'required|exists:ruang_sidangs,id',
            'hakim_id' => 'required|exists:hakims,id',
        ]);

        $perkara = ECourtPerkara::findOrFail($id);

        // Update perkara status
        $perkara->update([
            'nomor_register' => $request->nomor_perkara_resmi,
            'status_verifikasi' => 'terverifikasi',
            'status' => 'Sidang',
        ]);

        // Create new JadwalSidang record
        JadwalSidang::create([
            'nomor_perkara' => $request->nomor_perkara_resmi,
            'waktu_mulai' => Carbon::parse($request->tanggal_sidang_pertama),
            'waktu_selesai' => Carbon::parse($request->tanggal_sidang_pertama)->addHour(),
            'ruang_sidang_id' => $request->ruang_sidang_id,
            'hakim_id' => $request->hakim_id,
            'status' => 'TERJADWAL',
            'status_relaas' => 'Belum Dipanggil',
            'status_penggugat' => 'belum_hadir',
            'status_tergugat' => 'belum_hadir',
            'status_kelengkapan' => 'belum_lengkap',
        ]);

        return redirect()->back()->with('success', 'Perkara berhasil diverifikasi dengan Nomor Perkara Resmi: ' . $request->nomor_perkara_resmi . ' dan Jadwal Sidang Pertama telah diterbitkan.');
    }

    // Hakim Index
    public function hakimIndex()
    {
        $hakimId = auth()->user()->hakim_id;

        if (!$hakimId) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda tidak terhubung dengan data Hakim.');
        }

        $schedules = JadwalSidang::where('hakim_id', $hakimId)
            ->with(['ruangSidang'])
            ->orderBy('waktu_mulai', 'desc')
            ->get();

        return view('hakim.jadwal_sidang', compact('schedules'));
    }

    // Hakim Upload Decision
    public function hakimPutusan(Request $request, $id)
    {
        $request->validate([
            'file_putusan' => 'required|file|mimes:pdf|max:15360', // Max 15MB
        ]);

        $schedule = JadwalSidang::findOrFail($id);

        $filePath = null;
        if ($request->hasFile('file_putusan')) {
            $filePath = $request->file('file_putusan')->store('putusan', 'public');
        }

        // Create BerkasPutusan
        BerkasPutusan::create([
            'nomor_perkara' => $schedule->nomor_perkara,
            'file_asli' => asset('storage/' . $filePath),
            'is_anonim_selesai' => false,
        ]);

        // Update JadwalSidang
        $schedule->update([
            'status' => 'PUTUS',
        ]);

        // Update ECourtPerkara status
        $perkara = ECourtPerkara::where('nomor_register', $schedule->nomor_perkara)->first();
        if ($perkara) {
            $perkara->update([
                'status' => 'Putus',
            ]);
        }

        return redirect()->back()->with('success', 'Draf putusan berhasil diunggah dan status perkara telah diperbarui menjadi PUTUS.');
    }
}
