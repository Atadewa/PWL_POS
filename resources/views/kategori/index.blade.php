@extends('layouts.template') 
 
@section('content') 
    <div class="card"> 
        <div class="card-header"> 
            <h3 class="card-title">{{ $page->title }}</h3> 
            <div class="card-tools"> 
                <button onclick="modalAction('{{ url('/kategori/import') }}')" class="btn btn-info">Import Kategori</button> 
                <a href="{{ url('/kategori/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"> Export Kategori</i></a>  
                <a href="{{ url('/kategori/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"> Export Kategori</i></a> 
                <button onclick="modalAction('{{ url('/kategori/create_ajax') }}')" class="btn btn-success">Tambah Data (Ajax)</button> 
            </div> 
        </div> 
        <div class="card-body">  
          @if(session('success')) 
              <div class="alert alert-success">{{ session('success') }}</div> 
          @endif 
          @if(session('error')) 
              <div class="alert alert-danger">{{ session('error') }}</div> 
          @endif
          <table class="table table-bordered table-sm table-striped table-hover" id="table-kategori"> 
            <thead> 
              <tr>
                <th>No</th>
                <th>Kode Kategori</th>
                <th>Nama Kategori</th>
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
 
var dataKategori; 
$(document).ready(function(){ 
    dataKategori = $('#table-kategori').DataTable({ 
        processing: true, 
        serverSide: true, 
        ajax: { 
            "url": "{{ url('kategori/list') }}", 
            "dataType": "json", 
            "type": "POST", 
            "data": function (d) { 
                d.filter_kategori = $('.filter_kategori').val(); 
            } 
        }, 
        columns: [{ 
                data: "DT_RowIndex",  
                className: "text-center", 
                width: "5%", 
                orderable: false, 
                searchable: false 
            },{ 
                data: "kategori_kode",  
                className: "", 
                width: "20%",
                orderable: true, 
                searchable: true 
            },{ 
                data: "kategori_nama",  
                className: "", 
                width: "55%",
                orderable: true, 
                searchable: true, 
            },{ 
                data: "aksi",  
                className: "text-center", 
                width: "20%", 
                orderable: false, 
                searchable: false 
            } 
        ] 
    }); 
 
    $('#table-kategori_filter input').unbind().bind().on('keyup', function(e){ 
        if(e.keyCode == 13){ // enter key 
            dataKategori.search(this.value).draw(); 
        } 
    }); 
}); 
</script> 
@endpush