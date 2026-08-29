<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DelegasiPerkara;

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
