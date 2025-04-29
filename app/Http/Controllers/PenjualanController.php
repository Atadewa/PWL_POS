<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\PenjualanDetailModel;
use App\Models\PenjualanModel;
use App\Models\StokModel;
use App\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Transaksi Penjualan',
            'list'  => ['Home', 'Penjualan']
        ];

        $page = (object) [
            'title' => 'Transaksi penjualan yang terdaftar dalam sistem'
        ];

        $activeMenu = 'penjualan';

        $penjualan = PenjualanModel::all();
        $user = UserModel::select('user_id', 'nama')->get();
        $tahun = PenjualanModel::select(DB::raw('YEAR(penjualan_tanggal) AS tahun'))
            ->groupBy(DB::raw('YEAR(penjualan_tanggal)'))
            ->orderBy('tahun', 'asc')
            ->get();

        return view('penjualan.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'penjualan' => $penjualan, 'user' => $user,  'tahun' => $tahun, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $penjualans = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
            ->with('user');
    
        // Filter User
        if (!empty($request->filter_user)) {
            $penjualans->whereHas('user', function ($query) use ($request) {
                $query->where('user_id', $request->filter_user);
            });
        }

        // Filter Tahun
        $tahun = $request->input('filter_tahun');    
        if (!empty($request->filter_tahun)) {
            $penjualans->whereYear('penjualan_tanggal', $tahun);
        }

        // Filter Tahun
        $bulan = $request->input('filter_bulan');    
        if (!empty($request->filter_bulan)) {
            $penjualans->whereMonth('penjualan_tanggal', $bulan);
        }

        // Filter Tanggal/Hari
        $tanggal_hari = $request->input('filter_tanggal_hari');    
        if (!empty($request->filter_tanggal_hari)) {
            $penjualans->whereDay('penjualan_tanggal', $tanggal_hari);
        }
    
        return DataTables::of($penjualans)
            ->addIndexColumn()
            ->addColumn('aksi', function ($penjualan) {
                $btn = '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax').'\')" class="btn btn-info btn-sm mr-1">Detail</button>';
                $btn .= '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm mr-1">Edit</button>';
                $btn .= '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $barang = BarangModel::select('barang_id', 'barang_kode', 'barang_nama', 'harga_jual')->get();
  
        $stok = DB::table('t_stok')
        ->select('t_stok.barang_id', DB::raw('COALESCE(SUM(t_stok.stok_jumlah), 0) - COALESCE((SELECT SUM(jumlah) FROM t_penjualan_detail WHERE t_penjualan_detail.barang_id = t_stok.barang_id), 0) AS stok_jumlah'))
        ->groupBy('t_stok.barang_id')
        ->get();      

        return view('penjualan.create_ajax', ['stok' => $stok, 'barang' => $barang]);
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            if (count($request->barang_id) !== count(array_unique($request->barang_id))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terdapat barang yang dipilih lebih dari satu kali.'
                ]);
            }
            
            $rules = [
                'pembeli'           => 'required|string|max:50',
                'penjualan_tanggal' => 'required|date',
                'barang_id'         => 'required|array|min:1',
                'barang_id.*'       => 'required|integer|exists:m_barang,barang_id',
                'jumlah'            => 'required|array|min:1',
                'jumlah.*'          => 'required|integer|min:1',
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }
    
            DB::beginTransaction();
            try {
                $now = Carbon::now('Asia/Jakarta');
                $pnj = 'PNJ';
                $tanggal = $now->format('Ymd'); 
                $jam = $now->format('His');
            
                $penjualanKode = $pnj . $tanggal . $jam;

                $penjualan = PenjualanModel::create([
                    'pembeli'            => $request->pembeli,
                    'penjualan_tanggal'  => $request->penjualan_tanggal,
                    'penjualan_kode'     => $penjualanKode,
                    'user_id'            => auth()->id()
                ]);
    
                foreach ($request->barang_id as $i => $barang_id) {
                    $jumlah = $request->jumlah[$i];

                    // Ambil stok dari DB
                    $stokTersedia = DB::table('t_stok')
                        ->where('barang_id', $barang_id)
                        ->sum('stok_jumlah') 
                        - DB::table('t_penjualan_detail')
                            ->where('barang_id', $barang_id)
                            ->sum('jumlah');
                
                    if ($jumlah > $stokTersedia) {
                        throw new \Exception("Jumlah melebihi stok untuk barang ID: $barang_id");
                    }
    
                    // Ambil harga dari database
                    $barang = BarangModel::find($barang_id);
    
                    PenjualanDetailModel::create([
                        'penjualan_id' => $penjualan->penjualan_id,
                        'barang_id'    => $barang_id,
                        'jumlah'       => $jumlah,
                        'harga'        => $barang->harga_jual,
                    ]);
                }
    
                DB::commit();
                return response()->json([
                    'status'  => true,
                    'message' => 'Penjualan berhasil disimpan',
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ]);
            }
        }
    
        return redirect('/');
    }    

    public function stokJson()
    {
        $barang = BarangModel::select('barang_id', 'barang_kode', 'barang_nama', 'harga_jual')->get();
  
        $stok = DB::table('t_stok')
        ->select('t_stok.barang_id', DB::raw('COALESCE(SUM(t_stok.stok_jumlah), 0) - COALESCE((SELECT SUM(jumlah) FROM t_penjualan_detail WHERE t_penjualan_detail.barang_id = t_stok.barang_id), 0) AS stok_jumlah'))
        ->groupBy('t_stok.barang_id')
        ->get();   
    
        return response()->json([
            'barang' => $barang,
            'stok' => $stok
        ]);
    }    

    public function edit_ajax(string $id)
    {
        $penjualan = PenjualanModel::find($id);
        $detail = PenjualanDetailModel::all()->where('penjualan_id', $id);
        $barang = BarangModel::select('barang_id', 'barang_kode', 'barang_nama', 'harga_jual')->get();
  
        $stok = DB::table('t_stok')
            ->select('t_stok.barang_id', DB::raw('COALESCE(SUM(t_stok.stok_jumlah), 0) - COALESCE((SELECT SUM(jumlah) FROM t_penjualan_detail WHERE t_penjualan_detail.barang_id = t_stok.barang_id), 0) AS stok_jumlah'))
            ->groupBy('t_stok.barang_id')
            ->get();
        
        return view ('penjualan.edit_ajax', ['penjualan' => $penjualan, 'detail' => $detail, 'barang' => $barang, 'stok' => $stok]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'pembeli'           => 'required|string|max:50',
                'penjualan_tanggal' => 'required|date',
                'barang_id'         => 'required|array|min:1',
                'barang_id.*'       => 'required|integer|exists:m_barang,barang_id',
                'jumlah'            => 'required|array|min:1',
                'jumlah.*'          => 'required|integer|min:1',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            DB::beginTransaction();
            try {
                $penjualan = PenjualanModel::findOrFail($id);

                // Update data penjualan
                $penjualan->update([
                    'pembeli'            => $request->pembeli,
                    'penjualan_tanggal'  => $request->penjualan_tanggal,
                    'user_id'            => auth()->id(),
                ]);

                // Ambil data detail penjualan yang ada
                $existingDetails = PenjualanDetailModel::where('penjualan_id', $penjualan->penjualan_id)
                    ->get()
                    ->keyBy('barang_id');

                // Daftar barang_id dari input
                $inputBarangIds = $request->barang_id;

                // Proses detail penjualan
                foreach ($request->barang_id as $i => $barang_id) {
                    $jumlah = $request->jumlah[$i];

                    // Validasi stok
                    $stok = DB::table('t_stok')
                        ->select(DB::raw('COALESCE(SUM(stok_jumlah), 0) - COALESCE((SELECT SUM(jumlah) FROM t_penjualan_detail WHERE t_penjualan_detail.barang_id = t_stok.barang_id AND t_penjualan_detail.penjualan_id != ' . $penjualan->penjualan_id . '), 0) AS stok_jumlah'))
                        ->where('barang_id', $barang_id)
                        ->groupBy('barang_id')
                        ->first();

                    if (!$stok || $jumlah > $stok->stok_jumlah) {
                        throw new \Exception("Jumlah untuk barang ID {$barang_id} melebihi stok tersedia ({$stok->stok_jumlah}).");
                    }

                    // Ambil harga dari database
                    $barang = BarangModel::find($barang_id);

                    if (isset($existingDetails[$barang_id])) {
                        // Update detail yang sudah ada
                        $existingDetails[$barang_id]->update([
                            'jumlah' => $jumlah,
                            'harga'  => $barang->harga_jual,
                        ]);
                        unset($existingDetails[$barang_id]); // Hapus dari daftar untuk mencegah penghapusan
                    } else {
                        // Tambah detail baru
                        PenjualanDetailModel::create([
                            'penjualan_id' => $penjualan->penjualan_id,
                            'barang_id'    => $barang_id,
                            'jumlah'       => $jumlah,
                            'harga'        => $barang->harga_jual,
                        ]);
                    }
                }

                // Hapus detail yang tidak ada di input
                foreach ($existingDetails as $detail) {
                    $detail->delete();
                }

                DB::commit();
                return response()->json([
                    'status'  => true,
                    'message' => 'Penjualan berhasil diperbarui',
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ]);
            }
        }

        return redirect('/');
    }

    public function show_ajax(string $id)
    {
        $penjualan = PenjualanModel::find($id);
        $detail = PenjualanDetailModel::where('penjualan_id', $id)->get();

        return view('penjualan.show_ajax', ['penjualan' => $penjualan, 'detail' => $detail]);
    }

    public function confirm_ajax(string $id)
    {
        $penjualan = PenjualanModel::find($id);
        $detail = PenjualanDetailModel::where('penjualan_id', $id)->get();

        return view('penjualan.confirm_ajax', ['penjualan' => $penjualan, 'detail' => $detail]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $penjualan = PenjualanModel::find($id);
            $detail = PenjualanDetailModel::where('penjualan_id', $id)->exists();

            if ($penjualan && $detail) {
                try {
                    PenjualanDetailModel::where('penjualan_id', $id)->delete();
                    $penjualan->delete();

                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data gagal dihapus'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function export_excel()
    {
        $penjualan = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
                ->orderBy('penjualan_tanggal')
                ->with(['user'])
                ->get();
        $detail = PenjualanDetailModel::select('penjualan_id', 'barang_id', 'harga', 'jumlah')
                ->with(['penjualan', 'barang'])
                ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Kasir');
        $sheet->setCellValue('C1', 'Pembeli');
        $sheet->setCellValue('D1', 'Barang');
        $sheet->setCellValue('E1', 'Kuantitas');
        $sheet->setCellValue('F1', 'Harga');
        $sheet->setCellValue('G1', 'SubTotal');
        $sheet->setCellValue('H1', 'Total');
        $sheet->setCellValue('I1', 'Tanggal Penjualan');

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $no = 1;
        $baris = 2;
        foreach ($penjualan as $key1 => $data) {
            $jumlahBarang = -1;
            $total = 0;
            $barisBaru = $baris;
    
            foreach ($detail as $key2 => $value) {
                if ($value->penjualan_id === $data->penjualan_id) {
                    $subtotal = $value->barang->harga_jual * $value->jumlah;

                    $sheet->setCellValue('D' .$barisBaru, $value->barang->barang_nama);
                    $sheet->setCellValue('E' .$barisBaru, $value->jumlah);
                    $sheet->setCellValue('F' .$barisBaru, $value->barang->harga_jual);
                    $sheet->setCellValue('G' .$barisBaru, $subtotal);
                    $jumlahBarang++;
                    $total += $subtotal;
                    $barisBaru++;
                }
            }

            $awal = $baris;
            $akhir =  $baris + $jumlahBarang;

            $sheet->setCellValue('A'.$awal, $no);
            $sheet->mergeCells('A'.$awal.':A'.$akhir);
            $sheet->setCellValue('B'.$awal, $data->user->nama);
            $sheet->mergeCells('B'.$awal.':B'.$akhir);
            $sheet->setCellValue('C'.$awal, $data->pembeli);
            $sheet->mergeCells('C'.$awal.':C'.$akhir);
            $sheet->setCellValue('H'.$awal, $total);
            $sheet->mergeCells('H'.$awal.':H'.$akhir);
            $sheet->setCellValue('I'.$awal, $data->penjualan_tanggal);
            $sheet->mergeCells('I'.$awal.':I'.$akhir);

            $no++;
            $baris+=($jumlahBarang+1);
        }

        $sheet->getStyle('A1' . ':I' . ($baris-1))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        foreach(range('A', 'I') as $columnID){
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Penjualan');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Penjualan ' .date('Y-m-d H:i:s').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $penjualan = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
                ->orderBy('penjualan_tanggal')
                ->with(['user'])
                ->get();

        $detail = PenjualanDetailModel::select('penjualan_id', 'barang_id', 'harga', 'jumlah')
                ->with(['penjualan', 'barang'])
                ->get();

        $pdf = Pdf::loadView('penjualan.export_pdf', ['penjualan' => $penjualan, 'detail' => $detail]);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->render();

        return $pdf->stream('Data Penjualan '.date('Y-m-d H:i:s').'.pdf');
    }
}
