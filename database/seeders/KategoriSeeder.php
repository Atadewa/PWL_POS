<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kategori_kode' => 'MKN',
                'kategori_nama' => 'Makanan'
            ],
            [
                'kategori_kode' => 'MNM',
                'kategori_nama' => 'Minuman'
            ],
            [
                'kategori_kode' => 'ELK',
                'kategori_nama' => 'Elektronik'
            ],
            [
                'kategori_kode' => 'PKN',
                'kategori_nama' => 'Pakaian'
            ],
            [
                'kategori_kode' => 'ATK',
                'kategori_nama' => 'Alat Tulis Kantor'
            ],
        ];

        DB::table('m_kategori')->insert($data);
    }
}
