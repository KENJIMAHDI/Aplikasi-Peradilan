<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ERaterang;

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
