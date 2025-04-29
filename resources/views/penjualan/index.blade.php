@extends('layouts.template') 
 
@section('content') 
    <div class="card"> 
        <div class="card-header"> 
            <h3 class="card-title">{{ $page->title }}</h3> 
            <div class="card-tools"> 
                <a href="{{ url('/penjualan/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"> Export Penjualan</i></a>  
                <a href="{{ url('/penjualan/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"> Export Penjualan</i></a> 
                <button onclick="modalAction('{{ url('/penjualan/create_ajax') }}')" class="btn btn-success">Tambah Data (Ajax)</button> 
            </div> 
        </div> 
        <div class="card-body"> 
            <!-- untuk Filter data --> 
            <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2"> 
                <div class="row"> 
                    <div class="col-md-12"> 
                        <div class="form-group form-group-sm row text-sm mb-0"> 
                            <label for="filter_date" class="col-md-1 col-form-label">Filter</label>   
                            <div class="col-md-2"> 
                                <select name="filter_user" class="form-control form-control-sm filter_user"> 
                                    <option value="">- Nama Kasir -</option> 
                                    @foreach($user as $u) 
                                        <option value="{{ $u->user_id }}">{{ $u->nama }}</option> 
                                    @endforeach 
                                </select> 
                                <small class="form-text text-muted">Nama Kasir</small> 
                            </div>                               
                            <div class="col-md-2"> 
                                <select name="filter_tahun" class="form-control form-control-sm filter_tahun"> 
                                    <option value="">- Tahun Transaksi -</option> 
                                    @foreach($tahun as $t) 
                                        <option value="{{ $t->tahun }}">{{ $t->tahun }}</option> 
                                    @endforeach 
                                </select> 
                                <small class="form-text text-muted">Tahun Transaksi</small> 
                            </div>                               
                            <div class="col-md-2">
                                <select name="filter_bulan" class="form-control form-control-sm filter_bulan">
                                    <option value="">- Bulan Transaksi -</option>
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                                <small class="form-text text-muted">Bulan Transaksi</small>
                            </div>                               
                            <div class="col-md-2">
                                <select name="filter_tanggal_hari" class="form-control form-control-sm filter_tanggal_hari">
                                    <option value="">- Tanggal Transaksi -</option>
                                    @for($i = 1; $i <= 31; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <small class="form-text text-muted">Tanggal Transaksi</small>
                            </div>                               
                        </div> 
                    </div> 
                </div> 
            </div> 

            @if(session('success')) 
                <div class="alert alert-success">{{ session('success') }}</div> 
            @endif 
            @if(session('error')) 
                <div class="alert alert-danger">{{ session('error') }}</div> 
            @endif
            <table class="table table-bordered table-sm table-striped table-hover" id="table-penjualan"> 
            <thead> 
                <tr>
                  <th>No</th>
                  <th>Nama Kasir</th>
                  <th>Pembeli</th>
                  <th>Penjualan Kode</th>
                  <th>Tanggal Penjualan</th>
                  <th>Aksi</th>
                </tr> 
            </thead> 
            <tbody></tbody> 
            </table> 
        </div> 
    </div> 
    <div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" 
data-keyboard="false" data-width="75%"></div> 
     
@endsection 
 
@push('js') 
<script> 
    function modalAction(url = ''){ 
        $('#myModal').load(url,function(){ 
            $('#myModal').modal('show'); 
        }); 
    } 
 
var dataPenjualan; 
$(document).ready(function(){ 
    dataPenjualan = $('#table-penjualan').DataTable({ 
        processing: true, 
        serverSide: true, 
        ajax: { 
            "url": "{{ url('penjualan/list') }}", 
            "dataType": "json", 
            "type": "POST", 
            "data": function (d) { 
                d.filter_user = $('.filter_user').val(); 
                d.filter_tahun = $('.filter_tahun').val(); 
                d.filter_bulan = $('.filter_bulan').val(); 
                d.filter_tanggal_hari = $('.filter_tanggal_hari').val(); 
            } 
        }, 
        columns: [{ 
                data: "DT_RowIndex",  
                className: "text-center", 
                width: "", 
                orderable: false, 
                searchable: false 
            },{ 
                data: "user.nama",  
                className: "", 
                width: "", 
                orderable: false, 
                searchable: true 
            },{ 
                data: "pembeli",  
                className: "", 
                width: "", 
                orderable: true, 
                searchable: true, 
            },{ 
                data: "penjualan_kode",  
                className: "", 
                width: "", 
                orderable: true, 
                searchable: false 
            },{ 
                data: "penjualan_tanggal",  
                className: "", 
                width: "", 
                orderable: true, 
                searchable: false 
            },{  
                data: "aksi",  
                className: "text-center", 
                width: "", 
                orderable: false, 
                searchable: false 
            } 
        ] 
    }); 
 
    $('#table-penjualan_filter input').unbind().bind().on('keyup', function(e){ 
        if(e.keyCode == 13){ // enter key 
            dataPenjualan.search(this.value).draw(); 
        } 
    }); 
 
    $('.filter_user').change(function(){ 
        dataPenjualan.draw(); 
    }); 
    $('.filter_tahun').change(function(){ 
        dataPenjualan.draw(); 
    }); 
    $('.filter_bulan').change(function(){ 
        dataPenjualan.draw(); 
    }); 
    $('.filter_tanggal_hari').change(function(){ 
        dataPenjualan.draw(); 
    }); 
}); 
</script> 
@endpush