<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\LevelModel;
use App\Models\StokModel;
use App\Models\SupplierModel;
use App\Models\UserModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Yajra\DataTables\Facades\DataTables;

class StokController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Stok Barang',
            'list'  => ['Home', 'Stok']
        ];

        $page = (object) [
            'title' => 'Daftar stok barang yang terdaftar dalam sistem'
        ];

        $activeMenu = 'stok';

        $stok = StokModel::all();
        $supplier = SupplierModel::select('supplier_id', 'supplier_nama')->get();
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();
        $level = LevelModel::select('level_id', 'level_nama')->get();
        $user = UserModel::select('user_id', 'nama')->get();
        $barang = BarangModel::select('barang_id', 'barang_nama')->get();

        return view('stok.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'stok' => $stok,'supplier' => $supplier, 'kategori' => $kategori, 'level' => $level, 'user' => $user, 'barang' => $barang, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $stoks = StokModel::select('stok_id', 'supplier_id','barang_id', 'user_id', 'stok_tanggal','stok_jumlah')
            ->with(['supplier', 'user.level', 'barang.kategori']);
    
        // Filter Supplier
        $supplier_id = $request->input('filter_supplier');    
        if (!empty($request->filter_supplier)) {
            $stoks->where('supplier_id', $supplier_id);
        }
    
        // Filter Kategori Barang
        if (!empty($request->filter_kategori)) {
            $stoks->whereHas('barang', function ($query) use ($request) {
                $query->where('kategori_id', $request->filter_kategori);
            });
        }
    
        // Filter Level User
        if (!empty($request->filter_level)) {
            $stoks->whereHas('user', function ($query) use ($request) {
                $query->where('level_id', $request->filter_level);
            });
        }

        // Filter User
        $user_id = $request->input('filter_user');    
        if (!empty($request->filter_user)) {
            $stoks->where('user_id', $user_id);
        }

        // Filter Barang
        $barang_id = $request->input('filter_barang');    
        if (!empty($request->filter_barang)) {
            $stoks->where('barang_id', $barang_id);
        }
    
        return DataTables::of($stoks)
            ->addIndexColumn()
            ->addColumn('aksi', function ($stok) {
                $btn = '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id . '/show_ajax').'\')" class="btn btn-info btn-sm mr-1">Detail</button>';
                $btn .= '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm mr-1">Edit</button>';
                $btn .= '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    

    public function create_ajax()
    {
        $supplier = SupplierModel::select('supplier_id', 'supplier_nama')->get();
        $barang = BarangModel::select('barang_id', 'barang_nama')->get();
        $user = UserModel::select('user_id', 'username', 'nama')->get();

        return view('stok.create_ajax', ['supplier' => $supplier, 'barang' => $barang, 'user' => $user]);
    }

    public function store_ajax (Request $request) 
    {
        if($request->ajax() || $request->wantsJson()){
            $rules = [
                'barang_id'    => 'required|integer|exists:m_barang,barang_id',
                'supplier_id'  => 'required|integer|exists:m_supplier,supplier_id',
                'stok_tanggal' => 'required|date',
                'stok_jumlah'  => 'required|integer|min:0',
            ]; 
    
            $validator = Validator::make($request->all(), $rules);
    
            if($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }
    
            $data = $request->all();
            $data['user_id'] = auth()->id();

            StokModel::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Data stok berhasil disimpan'
            ]);
        }
        redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $stok = StokModel::find($id);
        $barang = BarangModel::select('barang_id', 'barang_nama')->get();
        $supplier = SupplierModel::select('supplier_id', 'supplier_nama')->get();

        return view ('stok.edit_ajax', ['stok' => $stok, 'barang' => $barang, 'supplier' => $supplier]);
    }

    public function update_ajax(Request $request, $id){ 
        if ($request->ajax() || $request->wantsJson()) { 
            $rules = [ 
                'barang_id'    => 'required|integer|exists:m_barang,barang_id',
                'supplier_id'  => 'required|integer|exists:m_supplier,supplier_id',
                'stok_tanggal' => 'required|date',
                'stok_jumlah'  => 'required|integer|min:0', 
            ]; 
         
            $validator = Validator::make($request->all(), $rules); 
    
            if ($validator->fails()) { 
                return response()->json([ 
                    'status'   => false,
                    'message'  => 'Validasi gagal.', 
                    'msgField' => $validator->errors()
                ]); 
            } 
    
            $data = $request->all();
            $data['user_id'] = auth()->id();

            $check = StokModel::find($id); 
            if ($check) { 
                $check->update($data); 
                return response()->json([ 
                    'status'  => true, 
                    'message' => 'Data berhasil diupdate' 
                ]); 
            } else{ 
                return response()->json([ 
                    'status'  => false, 
                    'message' => 'Data tidak ditemukan' 
                ]); 
            } 
        } 
        return redirect('/'); 
    } 

    public function show_ajax(string $id)
    {
        $stok = StokModel::find($id);

        return view('stok.show_ajax', ['stok' => $stok]);
    }

    public function confirm_ajax(string $id)
    {
        $stok = StokModel::find($id);

        return view('stok.confirm_ajax', ['stok' => $stok]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $stok = StokModel::find($id);
            if ($stok) {
                try {
                    $stok->delete();

                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini'
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

    public function import() 
    { 
        return view('stok.import'); 
    } 

    public function import_ajax(Request $request) 
    { 
        if($request->ajax() || $request->wantsJson()){ 
            $rules = [ 
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024'] 
            ]; 
     
            $validator = Validator::make($request->all(), $rules); 
            if($validator->fails()){ 
                return response()->json([ 
                    'status' => false, 
                    'message' => 'Validasi Gagal', 
                    'msgField' => $validator->errors() 
                ]); 
            } 
     
            $file = $request->file('file_stok'); 
     
            $reader = IOFactory::createReader('Xlsx');  
            $reader->setReadDataOnly(true); 
            $spreadsheet = $reader->load($file->getRealPath());  
            $sheet = $spreadsheet->getActiveSheet(); 
     
            $data = $sheet->toArray(null, false, true, true); // ambil data excel 
     
            $insert = []; 
            if(count($data) > 1){ 
                foreach ($data as $baris => $value) { 
                    if($baris > 1){ 
                        // Konversi tanggal dari Excel ke format Y-m-d H:i:s
                        $excelDate = $value['D'];
                        $stokTanggal = null;
                        if (!empty($excelDate)) {
                            if (is_numeric($excelDate)) {
                                $stokTanggal = Date::excelToDateTimeObject($excelDate)
                                    ->format('Y-m-d H:i:s');
                            } else {
                                try {
                                    $stokTanggal = \DateTime::createFromFormat('m/d/Y h:i:s A', $excelDate)
                                        ->format('Y-m-d H:i:s');
                                } catch (\Exception $e) {
                                    $stokTanggal = null;
                                }
                            }
                        }
    
                        $insert[] = [ 
                            'barang_id' => $value['A'], 
                            'supplier_id' => $value['B'], 
                            'user_id' => $value['C'], 
                            'stok_tanggal' => $stokTanggal, 
                            'stok_jumlah' => $value['E'], 
                            'created_at' => now(), 
                        ]; 
                    } 
                } 
     
                if(count($insert) > 0){ 
                    // insert data ke database, jika data sudah ada, maka diabaikan 
                    StokModel::insertOrIgnore($insert);    
                } 
     
                return response()->json([ 
                    'status' => true, 
                    'message' => 'Data berhasil diimport' 
                ]); 
            } else { 
                return response()->json([ 
                    'status' => false, 
                    'message' => 'Tidak ada data yang diimport' 
                ]); 
            } 
        } 
        return redirect('/'); 
    }

    public function export_excel()
    {
        $stok = StokModel::select('supplier_id', 'barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
                ->orderBy('supplier_id')
                ->with(['supplier', 'barang', 'user'])
                ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Supplier');
        $sheet->setCellValue('C1', 'Barang');
        $sheet->setCellValue('D1', 'Nama User');
        $sheet->setCellValue('E1', 'Tanggal Stok');
        $sheet->setCellValue('F1', 'Jumlah Stok');

        $no = 1;
        $baris = 2;

        foreach ($stok as $key => $value) {
            $sheet->setCellValue('A' .$baris, $no);
            $sheet->setCellValue('B' .$baris, $value->supplier->supplier_nama);
            $sheet->setCellValue('C' .$baris, $value->barang->barang_nama);
            $sheet->setCellValue('D' .$baris, $value->user->nama);
            $sheet->setCellValue('E' .$baris, $value->stok_tanggal);
            $sheet->setCellValue('F' .$baris, $value->stok_jumlah);
            $no++;
            $baris++;
        }

        foreach(range('A', 'F') as $columnID){
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->getStyle('A1' . ':F' . ($baris-1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        $sheet->getStyle('A1:A'.($baris-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E1:F'.($baris-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $sheet->setTitle('Data Stok');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Stok ' .date('Y-m-d H:i:s').'.xlsx';

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
        $stok = StokModel::select('supplier_id', 'barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
                ->orderBy('supplier_id')
                ->with(['supplier', 'barang', 'user'])
                ->get();

        $pdf = Pdf::loadView('stok.export_pdf', ['stok' => $stok]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->render();

        return $pdf->stream('Data Stok '.date('Y-m-d H:i:s').'.pdf');
    }
}
