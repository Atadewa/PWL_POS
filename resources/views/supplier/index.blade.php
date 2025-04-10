@extends('layouts.template') 
 
@section('content') 
    <div class="card"> 
        <div class="card-header"> 
            <h3 class="card-title">{{ $page->title }}</h3> 
            <div class="card-tools"> 
                <button onclick="modalAction('{{ url('/supplier/import') }}')" class="btn btn-info">Import Supplier</button> 
                <a href="{{ url('/supplier/create') }}" class="btn btn-primary">Tambah Data</a> 
                <button onclick="modalAction('{{ url('/supplier/create_ajax') }}')" class="btn btn-success">Tambah Data (Ajax)</button> 
            </div> 
        </div> 
        <div class="card-body">  
          @if(session('success')) 
              <div class="alert alert-success">{{ session('success') }}</div> 
          @endif 
          @if(session('error')) 
              <div class="alert alert-danger">{{ session('error') }}</div> 
          @endif
          <table class="table table-bordered table-sm table-striped table-hover" id="table-supplier"> 
            <thead> 
              <tr>
                <th>ID</th>
                <th>Kode Supplier</th>
                <th>Nama Supplier</th>
                <th>Alamat Supplier</th>
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
 
var tableSupplier; 
$(document).ready(function(){ 
    tableSupplier = $('#table-supplier').DataTable({ 
        processing: true, 
        serverSide: true, 
        ajax: { 
            "url": "{{ url('supplier/list') }}", 
            "dataType": "json", 
            "type": "POST", 
            "data": function (d) { 
                d.filter_kategori = $('.filter_kategori').val(); 
            } 
        }, 
        columns: [{ 
                data: "supplier_id",  
                className: "text-center", 
                width: "5%", 
                orderable: false, 
                searchable: false 
            },{ 
                data: "supplier_kode",  
                className: "", 
                width: "15%",
                orderable: true, 
                searchable: true 
            },{ 
                data: "supplier_nama",  
                className: "", 
                width: "25%",
                orderable: true, 
                searchable: true 
            },{ 
                data: "supplier_alamat",  
                className: "", 
                width: "35%",
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
 
    $('#table-supplier_filter input').unbind().bind().on('keyup', function(e){ 
        if(e.keyCode == 13){ // enter key 
            tableSupplier.search(this.value).draw(); 
        } 
    }); 
}); 
</script> 
@endpush