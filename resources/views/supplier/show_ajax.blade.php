@empty($supplier)
  <div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
      <div class="modal-body">
          <div class="alert alert-danger">
              <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
              Data yang anda cari tidak ditemukan
          </div>
      </div>
      <a href="{{ url('/supplier') }}" class="btn btn-warning">Kembali</a>
    </div>
  </div>
@else
  @csrf
  <div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Detail Supplier</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
      <div class="modal-body">
          <table class="table table-sm table-bordered table-striped">
            <tr>
              <th class="text-left col-3">ID</th>
              <td class="col-9">{{ $supplier->supplier_id }}</td>
            </tr>
            <tr>
              <th class="text-left col-3">Kode Supplier</th>
              <td class="col-9">{{ $supplier->supplier_kode }}</td>
            </tr>
            <tr>
              <th class="text-left col-3">Nama Supplier</th>
              <td class="col-9">{{ $supplier->supplier_nama }}</td>
            </tr>
            <tr>
              <th class="text-left col-3">Alamat Supplier</th>
              <td class="col-9">{{ $supplier->supplier_alamat }}</td>
            </tr>
          </table>
      </div>
      <div class="modal-footer">
          <button type="button" data-dismiss="modal" class="btn btn-warning">Kembali</button>
      </div>
    </div>
  </div>
@endempty 