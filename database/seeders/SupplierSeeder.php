<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'supplier_kode' => 'SP001',
                'supplier_nama' => 'PT Sumber Rezeki',
                'supplier_alamat' => 'Jl. Merdeka No. 10, Jakarta'
            ],
            [
                'supplier_kode' => 'SP002',
                'supplier_nama' => 'CV Maju Jaya',
                'supplier_alamat' => 'Jl. Raya Sudirman No. 25, Bandung'
            ],
            [
                'supplier_kode' => 'SP003',
                'supplier_nama' => 'UD Berkah Sejahtera',
                'supplier_alamat' => 'Jl. Ahmad Yani No. 45, Surabaya'
            ],
        ];

        DB::table('m_supplier')->insert($data);
    }
}
