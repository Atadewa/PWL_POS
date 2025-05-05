<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    public function index()
    {
        return BarangModel::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id'  => 'required|integer',
            'barang_kode'  => 'required|string',
            'barang_nama'  => 'required|string',
            'harga_beli'   => 'required|numeric',
            'harga_jual'   => 'required|numeric',
            'image'        => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $image = $request->file('image');
        $image->storeAs('public/barang', $image->hashName());
    
        $barang = BarangModel::create([
            'kategori_id'  => $request->kategori_id,
            'barang_kode'  => $request->barang_kode,
            'barang_nama'  => $request->barang_nama,
            'harga_beli'   => $request->harga_beli,
            'harga_jual'   => $request->harga_jual,
            'image'        => $image->hashName(),
        ]);
    
        return response()->json($barang, 201);
    }    

    public function show(BarangModel $barang)
    {
        return $barang;
    }

    public function update(Request $request, BarangModel $barang)
    {
        $request->validate([
            'kategori_id'  => 'sometimes|required|integer',
            'barang_kode'  => 'sometimes|required|string',
            'barang_nama'  => 'sometimes|required|string',
            'harga_beli'   => 'sometimes|required|numeric',
            'harga_jual'   => 'sometimes|required|numeric',
            'image'        => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($request->hasFile('image')) {
            Storage::delete('public/barang/' . $barang->image);
    
            $image = $request->file('image');
            $image->storeAs('public/barang', $image->hashName());
    
            $barang->update(array_merge($request->all(), [
                'image' => $image->hashName(),
            ]));
        } else {
            $barang->update($request->all());
        }
    
        return response()->json($barang);
    }
    

    public function destroy(BarangModel $barang)
    {
        $barang->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Data terhapus',
        ]);
    }
}
