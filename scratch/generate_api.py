import os

base_path = r"c:\Aplikasi Pengadilan"
controllers_dir = os.path.join(base_path, "app", "Http", "Controllers", "Api")
os.makedirs(controllers_dir, exist_ok=True)

controllers = {
    "ApiAuthController.php": """<?php
namespace App\\Http\\Controllers\\Api;
use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Auth;
use Illuminate\\Support\\Facades\\Hash;
use App\\Models\\User;

class ApiAuthController extends Controller {
    public function login(Request $request) {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['access_token' => $token, 'user' => $user]);
    }
    public function register(Request $request) {
        $request->validate(['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required|min:8', 'role' => 'required']);
        $user = User::create(['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password), 'role' => $request->role]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['access_token' => $token, 'user' => $user], 201);
    }
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
    public function me(Request $request) {
        return response()->json(['user' => $request->user()->load('hakim')]);
    }
}
""",
    "ApiDashboardController.php": """<?php
namespace App\\Http\\Controllers\\Api;
use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;
use App\\Models\\JadwalSidang;
use App\\Models\\ECourtPerkara;
use App\\Models\\PerkaraPidana;

class ApiDashboardController extends Controller {
    public function index(Request $request) {
        $masuk = ECourtPerkara::count();
        $putus = ECourtPerkara::where('status', 'Selesai')->count();
        $stats = [
            'masuk' => $masuk,
            'putus' => $putus,
            'sisa' => max(0, $masuk - $putus)
        ];
        $jadwalHariIni = JadwalSidang::with(['hakim', 'ruangSidang'])->whereDate('waktu_mulai', today())->orderBy('waktu_mulai', 'asc')->get();
        return response()->json(compact('stats', 'jadwalHariIni'));
    }
}
""",
    "ApiPerkaraController.php": """<?php
namespace App\\Http\\Controllers\\Api;
use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;
use App\\Models\\ECourtPerkara;
use App\\Models\\JadwalSidang;
use App\\Models\\BerkasPutusan;
use Illuminate\\Support\\Carbon;

class ApiPerkaraController extends Controller {
    public function index() {
        $perkaras = ECourtPerkara::where('penggugat', auth()->user()->name)->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $perkaras]);
    }
    public function storeMandiri(Request $request) {
        // Simplified for mobile
        $perkara = ECourtPerkara::create([
            'nomor_register' => 'REG-EC-' . now()->format('Ymd') . '-' . rand(1000, 9999),
            'tanggal_daftar' => now(),
            'jenis_perdata' => $request->kategori ?? 'Gugatan',
            'penggugat' => auth()->user()->name,
            'tergugat' => $request->nama_tergugat,
            'status' => 'Diajukan',
            'nominal_panjar' => 150000.00,
            'status_pembayaran' => 'Belum Dibayar',
        ]);
        return response()->json(['message' => 'Berhasil', 'data' => $perkara], 201);
    }
    public function adminIndex() {
        return response()->json(['data' => ECourtPerkara::orderBy('created_at', 'desc')->get()]);
    }
    public function adminConfirmPembayaran($id) {
        $perkara = ECourtPerkara::findOrFail($id);
        $perkara->update(['status_bayar' => 'lunas', 'status_pembayaran' => 'Lunas']);
        return response()->json(['message' => 'Pembayaran lunas', 'data' => $perkara]);
    }
    public function adminVerify(Request $request, $id) {
        $perkara = ECourtPerkara::findOrFail($id);
        $perkara->update(['nomor_register' => $request->nomor_perkara_resmi, 'status' => 'Sidang', 'status_verifikasi' => 'terverifikasi']);
        JadwalSidang::create([
            'nomor_perkara' => $request->nomor_perkara_resmi,
            'waktu_mulai' => Carbon::parse($request->tanggal_sidang_pertama),
            'waktu_selesai' => Carbon::parse($request->tanggal_sidang_pertama)->addHour(),
            'ruang_sidang_id' => $request->ruang_sidang_id,
            'hakim_id' => $request->hakim_id,
            'status' => 'TERJADWAL',
        ]);
        return response()->json(['message' => 'Terverifikasi', 'data' => $perkara]);
    }
    public function hakimIndex() {
        $schedules = JadwalSidang::where('hakim_id', auth()->user()->hakim_id)->with('ruangSidang')->orderBy('waktu_mulai', 'desc')->get();
        return response()->json(['data' => $schedules]);
    }
    public function hakimPutusan(Request $request, $id) {
        $schedule = JadwalSidang::findOrFail($id);
        $schedule->update(['status' => 'PUTUS']);
        return response()->json(['message' => 'Putusan diunggah', 'data' => $schedule]);
    }
}
""",
    "ApiSippController.php": """<?php
namespace App\\Http\\Controllers\\Api;
use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;
use App\\Models\\ECourtPerkara;
use App\\Models\\PerkaraPidana;

class ApiSippController extends Controller {
    public function perdataUmum() {
        return response()->json(['data' => ECourtPerkara::whereIn('jenis_perdata', ['Gugatan', 'Permohonan'])->latest('id')->get()]);
    }
    public function perdataKhusus() {
        return response()->json(['data' => ECourtPerkara::whereIn('jenis_perdata', ['PHI', 'Niaga'])->latest('id')->get()]);
    }
    public function storePerdata(Request $request) {
        $perkara = ECourtPerkara::create($request->all());
        return response()->json(['message' => 'Berhasil ditambahkan', 'data' => $perkara], 201);
    }
    public function updatePerdata(Request $request, $id) {
        $perkara = ECourtPerkara::findOrFail($id);
        $perkara->update($request->all());
        return response()->json(['message' => 'Berhasil diperbarui', 'data' => $perkara]);
    }
    public function destroyPerdata($id) {
        ECourtPerkara::findOrFail($id)->delete();
        return response()->json(['message' => 'Berhasil dihapus']);
    }
    public function pidanaBiasa() {
        return response()->json(['data' => PerkaraPidana::where('status', '!=', 'Khusus')->get()]);
    }
    public function pidanaKhusus() {
        return response()->json(['data' => PerkaraPidana::where('status', 'Khusus')->get()]);
    }
    public function storePidana(Request $request) {
        $perkara = PerkaraPidana::create($request->all());
        return response()->json(['message' => 'Berhasil', 'data' => $perkara], 201);
    }
    public function updatePidana(Request $request, $id) {
        $perkara = PerkaraPidana::findOrFail($id);
        $perkara->update($request->all());
        return response()->json(['message' => 'Berhasil', 'data' => $perkara]);
    }
    public function destroyPidana($id) {
        PerkaraPidana::findOrFail($id)->delete();
        return response()->json(['message' => 'Berhasil']);
    }
}
""",
    "ApiJadwalSidangController.php": """<?php
namespace App\\Http\\Controllers\\Api;
use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;
use App\\Models\\JadwalSidang;
use App\\Services\\JadwalSidangService;

class ApiJadwalSidangController extends Controller {
    public function index() {
        return response()->json(['data' => JadwalSidang::with(['hakim', 'ruangSidang'])->orderBy('waktu_mulai', 'asc')->get()]);
    }
    public function store(Request $request, JadwalSidangService $service) {
        try {
            $service->cekKonflik($request->waktu_mulai, $request->waktu_selesai, $request->ruang_sidang_id, $request->hakim_id);
            $jadwal = JadwalSidang::create($request->all());
            return response()->json(['message' => 'Berhasil', 'data' => $jadwal], 201);
        } catch (\\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
    public function update(Request $request, $id, JadwalSidangService $service) {
        $jadwal = JadwalSidang::findOrFail($id);
        try {
            $service->cekKonflik($request->waktu_mulai, $request->waktu_selesai, $request->ruang_sidang_id, $request->hakim_id, $id);
            $jadwal->update($request->all());
            return response()->json(['message' => 'Berhasil', 'data' => $jadwal]);
        } catch (\\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
    public function destroy($id) {
        JadwalSidang::findOrFail($id)->delete();
        return response()->json(['message' => 'Berhasil']);
    }
    public function panggil($id) {
        // Mock blast
        return response()->json(['message' => 'Panggilan dikirim']);
    }
}
""",
    "ApiKehadiranHakimController.php": """<?php
namespace App\\Http\\Controllers\\Api;
use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;
use App\\Models\\PresensiHakim;

class ApiKehadiranHakimController extends Controller {
    public function index() {
        return response()->json(['data' => PresensiHakim::with('hakim')->latest()->get()]);
    }
    public function store(Request $request) {
        $presensi = PresensiHakim::create($request->all());
        return response()->json(['message' => 'Berhasil', 'data' => $presensi], 201);
    }
    public function update(Request $request, $id) {
        $presensi = PresensiHakim::findOrFail($id);
        $presensi->update($request->all());
        return response()->json(['message' => 'Berhasil', 'data' => $presensi]);
    }
    public function destroy($id) {
        PresensiHakim::findOrFail($id)->delete();
        return response()->json(['message' => 'Berhasil']);
    }
}
""",
    "ApiAntreanController.php": """<?php
namespace App\\Http\\Controllers\\Api;
use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;
use App\\Models\\JadwalSidang;

class ApiAntreanController extends Controller {
    public function index() {
        return response()->json(['data' => JadwalSidang::whereDate('waktu_mulai', today())->orderBy('waktu_mulai', 'asc')->get()]);
    }
    public function store(Request $request) {
        $jadwal = JadwalSidang::firstOrCreate(['nomor_perkara' => $request->no_perkara], [
            'waktu_mulai' => now(), 'waktu_selesai' => now()->addHour(), 'ruang_sidang_id' => 1, 'hakim_id' => 1, 'status' => 'TERJADWAL'
        ]);
        if (in_array($request->peran, ['penggugat', 'kuasa_hukum'])) {
            $jadwal->status_penggugat = 'Hadir';
        } else {
            $jadwal->status_tergugat = 'Hadir';
        }
        $jadwal->save();
        return response()->json(['message' => 'Checkin berhasil', 'data' => $jadwal]);
    }
}
""",
    "ApiBerkasPerkaraController.php": """<?php
namespace App\\Http\\Controllers\\Api;
use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;
use App\\Models\\BerkasPutusan;

class ApiBerkasPerkaraController extends Controller {
    public function index() {
        return response()->json(['data' => BerkasPutusan::all()]);
    }
    public function store(Request $request) {
        $berkas = BerkasPutusan::create($request->all());
        return response()->json(['message' => 'Berhasil', 'data' => $berkas], 201);
    }
    public function update(Request $request, $id) {
        $berkas = BerkasPutusan::findOrFail($id);
        $berkas->update($request->all());
        return response()->json(['message' => 'Berhasil', 'data' => $berkas]);
    }
    public function destroy($id) {
        BerkasPutusan::findOrFail($id)->delete();
        return response()->json(['message' => 'Berhasil']);
    }
    public function downloadAnonim($id) {
        return response()->json(['message' => 'URL download anonim disiapkan', 'url' => 'mock_url']);
    }
}
""",
    "ApiKalkulatorController.php": """<?php
namespace App\\Http\\Controllers\\Api;
use App\\Http\\Controllers\\Controller;

class ApiKalkulatorController extends Controller {
    public function index() {
        return response()->json(['biaya_pendaftaran' => 30000, 'biaya_proses' => 50000, 'biaya_panggilan' => 150000]);
    }
}
""",
    "ApiDelegasiController.php": """<?php
namespace App\\Http\\Controllers\\Api;
use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;
use App\\Models\\DelegasiPerkara;

class ApiDelegasiController extends Controller {
    public function index() {
        return response()->json(['data' => DelegasiPerkara::latest()->get()]);
    }
    public function store(Request $request) {
        $delegasi = DelegasiPerkara::create($request->all());
        return response()->json(['message' => 'Berhasil', 'data' => $delegasi], 201);
    }
    public function update(Request $request, $id) {
        $delegasi = DelegasiPerkara::findOrFail($id);
        $delegasi->update($request->all());
        return response()->json(['message' => 'Berhasil', 'data' => $delegasi]);
    }
    public function destroy($id) {
        DelegasiPerkara::findOrFail($id)->delete();
        return response()->json(['message' => 'Berhasil']);
    }
}
""",
    "ApiRelaasController.php": """<?php
namespace App\\Http\\Controllers\\Api;
use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;
use App\\Models\\JadwalSidang;

class ApiRelaasController extends Controller {
    public function index() {
        $daftarRelaas = JadwalSidang::with(['hakim', 'ruangSidang'])->orderBy('waktu_mulai', 'desc')->get();
        return response()->json(['data' => $daftarRelaas]);
    }
    public function updateStatus(Request $request, $id) {
        $jadwal = JadwalSidang::findOrFail($id);
        $jadwal->update(['status_relaas' => $request->status_relaas]);
        return response()->json(['message' => 'Berhasil', 'data' => $jadwal]);
    }
}
""",
    "ApiLaporanStatistikController.php": """<?php
namespace App\\Http\\Controllers\\Api;
use App\\Http\\Controllers\\Controller;
use App\\Models\\ECourtPerkara;
use App\\Models\\PerkaraPidana;

class ApiLaporanStatistikController extends Controller {
    public function index() {
        $gugatanMasuk = ECourtPerkara::where('jenis_perdata', 'Gugatan')->count();
        $gugatanPutus = ECourtPerkara::where('jenis_perdata', 'Gugatan')->where('status', 'Selesai')->count();
        $pidanaMasuk = PerkaraPidana::count();
        $pidanaPutus = PerkaraPidana::where('status', 'Putus')->count();
        $totalMasuk = $gugatanMasuk + $pidanaMasuk;
        $totalPutus = $gugatanPutus + $pidanaPutus;
        return response()->json([
            'totalMasuk' => $totalMasuk,
            'totalPutus' => $totalPutus,
            'clearanceRate' => $totalMasuk > 0 ? round(($totalPutus / $totalMasuk) * 100, 1) : 100.0
        ]);
    }
}
""",
    "ApiUserController.php": """<?php
namespace App\\Http\\Controllers\\Api;
use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;
use App\\Models\\User;
use Illuminate\\Support\\Facades\\Hash;

class ApiUserController extends Controller {
    public function index() {
        return response()->json(['data' => User::all()]);
    }
    public function store(Request $request) {
        $user = User::create(['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password), 'role' => $request->role]);
        return response()->json(['message' => 'Berhasil', 'data' => $user], 201);
    }
    public function update(Request $request, $id) {
        $user = User::findOrFail($id);
        $data = $request->only(['name', 'email', 'role']);
        if ($request->filled('password')) $data['password'] = Hash::make($request->password);
        $user->update($data);
        return response()->json(['message' => 'Berhasil', 'data' => $user]);
    }
    public function destroy($id) {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'Berhasil']);
    }
}
""",
    "ApiEBerpaduController.php": """<?php
namespace App\\Http\\Controllers\\Api;
use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;
use App\\Models\\EBerpadu;

class ApiEBerpaduController extends Controller {
    public function index() {
        return response()->json(['data' => EBerpadu::all()]);
    }
    public function store(Request $request) {
        $data = EBerpadu::create($request->all());
        return response()->json(['message' => 'Berhasil', 'data' => $data], 201);
    }
    public function update(Request $request, $id) {
        $data = EBerpadu::findOrFail($id);
        $data->update($request->all());
        return response()->json(['message' => 'Berhasil', 'data' => $data]);
    }
}
""",
    "ApiERaterangController.php": """<?php
namespace App\\Http\\Controllers\\Api;
use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;
use App\\Models\\ERaterang;

class ApiERaterangController extends Controller {
    public function index() {
        return response()->json(['data' => ERaterang::all()]);
    }
    public function store(Request $request) {
        $data = ERaterang::create($request->all());
        return response()->json(['message' => 'Berhasil', 'data' => $data], 201);
    }
    public function update(Request $request, $id) {
        $data = ERaterang::findOrFail($id);
        $data->update($request->all());
        return response()->json(['message' => 'Berhasil', 'data' => $data]);
    }
    public function show($id) {
        return response()->json(['data' => ERaterang::findOrFail($id)]);
    }
}
"""
}

for filename, content in controllers.items():
    filepath = os.path.join(controllers_dir, filename)
    with open(filepath, 'w') as f:
        f.write(content)
    print(f"Created {filename}")
