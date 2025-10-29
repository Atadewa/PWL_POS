<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PenjualanDetailModel;
use Illuminate\Support\Facades\Validator;

class PenjualanDetailController extends Controller
{
    public function index()
    {
        $penjualanDetails = PenjualanDetailModel::with(['penjualan', 'barang'])->get();
        return response()->json($penjualanDetails);
    }

    public function show($id)
    {
        $penjualanDetails = PenjualanDetailModel::with(['penjualan', 'barang'])
            ->where('penjualan_id', $id)
            ->get();
    
        if ($penjualanDetails->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data detail penjualan tidak ditemukan'
            ], 404);
        }
    
        $response = [
            'penjualan' => $penjualanDetails->first()->penjualan, // Ambil data penjualan dari salah satu detail
            'details' => $penjualanDetails // Semua detail barang
        ];
    
        return response()->json($response, 200);
    }
}