@empty($penjualan)
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
      <a href="{{ url('/penjualan') }}" class="btn btn-warning">Kembali</a>
    </div>
  </div>
@else
  @csrf
  <div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Detail Penjualan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
      <div class="modal-body">
          <table class="table table-sm table-bordered table-striped">
            <tr>
              <th class="text-left col-3">ID</th>
              <td class="col-9">{{ $penjualan->penjualan_id }}</td>
            </tr>
            <tr>
              <th class="text-left col-3">Kode Penjualan</th>
              <td class="col-9">{{ $penjualan->penjualan_kode }}</td>
            </tr>
            <tr>
              <th class="text-left col-3">Nama Kasir</th>
              <td class="col-9">{{ $penjualan->user->nama }}</td>
            </tr>
            <tr>
              <th class="text-left col-3">Pembeli</th>
              <td class="col-9">{{ $penjualan->pembeli }}</td>
            </tr>
            <tr>
              <th class="text-left col-3">Tanggal Penjualan</th>
              <td class="col-9">{{ $penjualan->penjualan_tanggal }}</td>
            </tr>
          </table>
          <p>Barang yang dibeli:</p>
          <table class="table table-sm table-bordered table-striped">
            @php
              $total = 0;
            @endphp
            <tr>
              <th class="text-center">No</th>
              <th class="text-center">Nama Barang</th>
              <th class="text-center">Harga</th>
              <th class="text-center">Jumlah</th>
              <th class="text-center">SubTotal</th>
            </tr>
          @foreach ($detail as $item)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td class="">{{ $item->barang->barang_nama }}</td>
              <td class="text-center">{{ number_format($item->barang->harga_jual, 0, ',', '.') }}</td>
              <td class="text-center">{{ $item->jumlah }}</td>
              <td class="text-center">{{ number_format($item->barang->harga_jual*$item->jumlah, 0, ',', '.') }}</td>
            </tr>
            @php
              $total += ($item->barang->harga_jual*$item->jumlah);
            @endphp
          @endforeach
            <tr>
              <th class="text-center" colspan="4">Total</th>
              <td class="text-center">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </table>
      </div>
      <div class="modal-footer">
          <button type="button" data-dismiss="modal" class="btn btn-warning">Kembali</button>
      </div>
    </div>
  </div>
@endempty