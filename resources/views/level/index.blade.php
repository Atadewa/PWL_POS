@extends('layouts.template') 
 
@section('content') 
    <div class="card"> 
        <div class="card-header"> 
            <h3 class="card-title">{{ $page->title }}</h3> 
            <div class="card-tools"> 
                <button onclick="modalAction('{{ url('/level/import') }}')" class="btn btn-info">Import Level</button> 
                <a href="{{ url('/level/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"> Export Level</i></a>  
                <a href="{{ url('/level/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"> Export Level</i></a> 
                <button onclick="modalAction('{{ url('/level/create_ajax') }}')" class="btn btn-success">Tambah Data (Ajax)</button> 
            </div> 
        </div> 
        <div class="card-body">  
          @if(session('success')) 
              <div class="alert alert-success">{{ session('success') }}</div> 
          @endif 
          @if(session('error')) 
              <div class="alert alert-danger">{{ session('error') }}</div> 
          @endif
          <table class="table table-bordered table-sm table-striped table-hover" id="table-level"> 
            <thead> 
              <tr>
                <th>No</th>
                <th>Kode Level</th>
                <th>Level</th>
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
 
var dataLevel; 
$(document).ready(function(){ 
    dataLevel = $('#table-level').DataTable({ 
        processing: true, 
        serverSide: true, 
        ajax: { 
            "url": "{{ url('level/list') }}", 
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
                data: "level_kode",  
                className: "", 
                width: "25%",
                orderable: true, 
                searchable: true 
            },{ 
                data: "level_nama",  
                className: "", 
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
 
    $('#table-level_filter input').unbind().bind().on('keyup', function(e){ 
        if(e.keyCode == 13){ // enter key 
            dataLevel.search(this.value).draw(); 
        } 
    }); 
}); 
</script> 
@endpush