<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kategori_id' => 1, 'barang_kode' => 'MKN001', 'barang_nama' => 'Roti', 'harga_beli' => 8000, 'harga_jual' => 10000],
            ['kategori_id' => 1, 'barang_kode' => 'MKN002', 'barang_nama' => 'Mie Goreng Instan', 'harga_beli' => 2500, 'harga_jual' => 4000],
            ['kategori_id' => 2, 'barang_kode' => 'MNM001', 'barang_nama' => 'Teh Botol', 'harga_beli' => 5000, 'harga_jual' => 8000],
            ['kategori_id' => 2, 'barang_kode' => 'MNM002', 'barang_nama' => 'Kopi Sachet', 'harga_beli' => 1000, 'harga_jual' => 2000],
            ['kategori_id' => 2, 'barang_kode' => 'MNM003', 'barang_nama' => 'Susu Kotak', 'harga_beli' => 6000, 'harga_jual' => 9000],
            ['kategori_id' => 3, 'barang_kode' => 'ELK001', 'barang_nama' => 'Blender', 'harga_beli' => 200000, 'harga_jual' => 250000],
            ['kategori_id' => 3, 'barang_kode' => 'ELK002', 'barang_nama' => 'Setrika Listrik', 'harga_beli' => 150000, 'harga_jual' => 180000],
            ['kategori_id' => 4, 'barang_kode' => 'PKN001', 'barang_nama' => 'Kaos Polos', 'harga_beli' => 50000, 'harga_jual' => 70000],
            ['kategori_id' => 4, 'barang_kode' => 'PKN002', 'barang_nama' => 'Celana Jeans', 'harga_beli' => 120000, 'harga_jual' => 150000],
            ['kategori_id' => 4, 'barang_kode' => 'PKN003', 'barang_nama' => 'Jaket Hoodie', 'harga_beli' => 180000, 'harga_jual' => 220000],
            ['kategori_id' => 5, 'barang_kode' => 'ATK001', 'barang_nama' => 'Bolpoin Hitam', 'harga_beli' => 2000, 'harga_jual' => 4000],
            ['kategori_id' => 5, 'barang_kode' => 'ATK002', 'barang_nama' => 'Buku Tulis', 'harga_beli' => 5000, 'harga_jual' => 7000],
            ['kategori_id' => 5, 'barang_kode' => 'ATK003', 'barang_nama' => 'Pensil HB', 'harga_beli' => 1500, 'harga_jual' => 3000],
            ['kategori_id' => 5, 'barang_kode' => 'ATK004', 'barang_nama' => 'Penghapus', 'harga_beli' => 1000, 'harga_jual' => 2500],
            ['kategori_id' => 5, 'barang_kode' => 'ATK005', 'barang_nama' => 'Stapler', 'harga_beli' => 15000, 'harga_jual' => 20000],
        ];

        DB::table('m_barang')->insert($data);
    }
}
