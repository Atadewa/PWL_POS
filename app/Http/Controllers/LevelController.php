<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LevelController extends Controller
{
    public function index()
    {
        //DB::insert('INSERT INTO m_level(level_kode, level_nama, created_at) VALUES (?, ?, ?)', ['CUS', 'Pelanggan', now()]);
        //return 'Insert data baru berhasil';

        $row = DB::update('UPDATE m_level SET level_nama = ? WHERE level_kode = ?', ['Customer', 'CUS']);
        return 'Update data berhasil. Jumlah data yang diupdate: ' . $row . ' baris';
    }
}
