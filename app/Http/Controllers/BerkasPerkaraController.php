<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BerkasPerkaraController extends Controller
{
    public function index()
    {
        $berkas = \App\Models\BerkasPutusan::all();
        return view('berkas.index', compact('berkas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_perkara' => 'required',
            'file_asli' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // 10MB Max
        ]);

        $path = $request->file('file_asli')->store('putusan', 'public');

        $berkas = \App\Models\BerkasPutusan::create([
            'nomor_perkara' => $request->nomor_perkara,
            'file_asli' => $path,
            'is_anonim_selesai' => false,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diunggah.',
                'data' => $berkas
            ]);
        }

        return redirect()->back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function update(Request $request, $id)
    {
        $berkas = \App\Models\BerkasPutusan::findOrFail($id);
        
        if ($request->has('is_anonim_selesai')) {
            $berkas->is_anonim_selesai = filter_var($request->is_anonim_selesai, FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->hasFile('file_asli')) {
            $request->validate([
                'file_asli' => 'file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            ]);
            
            if ($berkas->file_asli && \Illuminate\Support\Facades\Storage::disk('public')->exists($berkas->file_asli)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($berkas->file_asli);
            }
            
            $path = $request->file('file_asli')->store('putusan', 'public');
            $berkas->file_asli = $path;
        }

        $berkas->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data berkas berhasil diperbarui.',
                'data' => $berkas
            ]);
        }

        return redirect()->back()->with('success', 'Data berkas berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $berkas = \App\Models\BerkasPutusan::findOrFail($id);
        
        if ($berkas->file_asli && \Illuminate\Support\Facades\Storage::disk('public')->exists($berkas->file_asli)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($berkas->file_asli);
        }
        
        $berkas->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Berkas berhasil dihapus.'
            ]);
        }

        return redirect()->back()->with('success', 'Berkas berhasil dihapus.');
    }

    public function downloadAnonim($id)
    {
        $berkas = \App\Models\BerkasPutusan::findOrFail($id);
        
        if (!$berkas->file_asli || !\Illuminate\Support\Facades\Storage::disk('public')->exists($berkas->file_asli)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // Simulasi anonimisasi sederhana (untuk teks / metadata)
        // Karena kita tidak benar-benar membaca isi PDF mentah, kita bisa menyimulasikannya dengan mengembalikan string atau file draf
        // Dalam dunia nyata, Anda membutuhkan parser PDF (seperti smalot/pdfparser) untuk mengganti nama pihak secara Regex (preg_replace).
        // Di sini kita kembalikan Response download dengan nama file yang diubah
        
        $path = storage_path('app/public/' . $berkas->file_asli);
        
        return response()->download($path, 'ANONIM_' . basename($berkas->file_asli));
    }
}
