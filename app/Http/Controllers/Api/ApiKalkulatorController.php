<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

class ApiKalkulatorController extends Controller {
    public function index() {
        return response()->json(['biaya_pendaftaran' => 30000, 'biaya_proses' => 50000, 'biaya_panggilan' => 150000]);
    }
}
