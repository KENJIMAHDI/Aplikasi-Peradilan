<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
