<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EBerpadu;

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
