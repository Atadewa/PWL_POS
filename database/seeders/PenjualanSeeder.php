<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['user_id' => 1, 'pembeli' => 'Andi', 'penjualan_kode' => 'PNJ0001', 'penjualan_tanggal' => now()],
            ['user_id' => 2, 'pembeli' => 'Budi', 'penjualan_kode' => 'PNJ0002', 'penjualan_tanggal' => now()],
            ['user_id' => 3, 'pembeli' => 'Citra', 'penjualan_kode' => 'PNJ0003', 'penjualan_tanggal' => now()],
            ['user_id' => 1, 'pembeli' => 'Dewi', 'penjualan_kode' => 'PNJ0004', 'penjualan_tanggal' => now()],
            ['user_id' => 2, 'pembeli' => 'Eka', 'penjualan_kode' => 'PNJ0005', 'penjualan_tanggal' => now()],
            ['user_id' => 3, 'pembeli' => 'Fajar', 'penjualan_kode' => 'PNJ0006', 'penjualan_tanggal' => now()],
            ['user_id' => 1, 'pembeli' => 'Gita', 'penjualan_kode' => 'PNJ0007', 'penjualan_tanggal' => now()],
            ['user_id' => 2, 'pembeli' => 'Hendra', 'penjualan_kode' => 'PNJ0008', 'penjualan_tanggal' => now()],
            ['user_id' => 3, 'pembeli' => 'Indah', 'penjualan_kode' => 'PNJ0009', 'penjualan_tanggal' => now()],
            ['user_id' => 1, 'pembeli' => 'Joko', 'penjualan_kode' => 'PNJ0010', 'penjualan_tanggal' => now()],
        ];

        DB::table('t_penjualan')->insert($data);
    }
}
