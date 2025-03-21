<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    public function update(Request $request)
    {
        try {
            Log::info("Log diperbarui: " . now());
            return response()->json([
                'message' => 'Log diperbarui setiap 30 menit',
                'time' => now()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan'], 500);
        }
    }
}