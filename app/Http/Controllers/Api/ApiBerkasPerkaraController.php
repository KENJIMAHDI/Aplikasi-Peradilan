<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BerkasPutusan;

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
