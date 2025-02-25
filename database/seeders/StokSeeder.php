<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StokSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Stok dari PT Sumber Rezeki
            ['supplier_id' => 1, 'barang_id' => 1, 'user_id' => 1, 'stok_tanggal' => now(), 'stok_jumlah' => 50],
            ['supplier_id' => 1, 'barang_id' => 2, 'user_id' => 1, 'stok_tanggal' => now(), 'stok_jumlah' => 40],
            ['supplier_id' => 1, 'barang_id' => 3, 'user_id' => 1, 'stok_tanggal' => now(), 'stok_jumlah' => 60],
            ['supplier_id' => 1, 'barang_id' => 4, 'user_id' => 1, 'stok_tanggal' => now(), 'stok_jumlah' => 55],
            ['supplier_id' => 1, 'barang_id' => 5, 'user_id' => 1, 'stok_tanggal' => now(), 'stok_jumlah' => 45],

            // Stok dari CV Maju Jaya
            ['supplier_id' => 2, 'barang_id' => 6, 'user_id' => 2, 'stok_tanggal' => now(), 'stok_jumlah' => 30],
            ['supplier_id' => 2, 'barang_id' => 7, 'user_id' => 2, 'stok_tanggal' => now(), 'stok_jumlah' => 25],
            ['supplier_id' => 2, 'barang_id' => 8, 'user_id' => 2, 'stok_tanggal' => now(), 'stok_jumlah' => 50],
            ['supplier_id' => 2, 'barang_id' => 9, 'user_id' => 2, 'stok_tanggal' => now(), 'stok_jumlah' => 35],
            ['supplier_id' => 2, 'barang_id' => 10, 'user_id' => 2, 'stok_tanggal' => now(), 'stok_jumlah' => 40],

            // Stok dari UD Berkah Sejahtera
            ['supplier_id' => 3, 'barang_id' => 11, 'user_id' => 3, 'stok_tanggal' => now(), 'stok_jumlah' => 80],
            ['supplier_id' => 3, 'barang_id' => 12, 'user_id' => 3, 'stok_tanggal' => now(), 'stok_jumlah' => 90],
            ['supplier_id' => 3, 'barang_id' => 13, 'user_id' => 3, 'stok_tanggal' => now(), 'stok_jumlah' => 100],
            ['supplier_id' => 3, 'barang_id' => 14, 'user_id' => 3, 'stok_tanggal' => now(), 'stok_jumlah' => 75],
            ['supplier_id' => 3, 'barang_id' => 15, 'user_id' => 3, 'stok_tanggal' => now(), 'stok_jumlah' => 65],
        ];

        DB::table('t_stok')->insert($data);
    }
}
