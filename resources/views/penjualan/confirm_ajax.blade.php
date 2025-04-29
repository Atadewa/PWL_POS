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
    <form action="{{ url('/penjualan/'. $penjualan->penjualan_id.'/delete_ajax') }}" method="POST" id="form-delete">
        @csrf
        @method('DELETE')
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Data Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-ban"></i> Konfirmasi !!!</h5>
                        Apakah Anda ingin menghapus data seperti di bawah ini?
                    </div>
                    <table class="table table-sm table-bordered table-striped">
                        <tr>
                            <th class="text-right col-3">Kode Penjualan : </th>
                            <td class="col-9">{{ $penjualan->penjualan_kode}}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">Nama Kasir : </th>
                            <td class="col-9">{{ $penjualan->user->nama }}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">Pembeli : </th>
                            <td class="col-9">{{ $penjualan->pembeli }}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">Tanggal Penjualan : </th>
                            <td class="col-9">{{ $penjualan->penjualan_tanggal }}</td>
                        </tr>
                    </table>
                    <p>Barang yang dibeli:</p>
                    <table class="table table-sm table-bordered table-striped">
                      @php
                        $total = 0;
                        $baris = 1;
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
                        <td class="text-center">{{ $baris }}</td>
                        <td class="">{{ $item->barang->barang_nama }}</td>
                        <td class="text-center">{{ number_format($item->barang->harga_jual, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $item->jumlah }}</td>
                        <td class="text-center">{{ number_format($item->barang->harga_jual*$item->jumlah, 0, ',', '.') }}</td>
                      </tr>
                      @php
                        $total += ($item->barang->harga_jual*$item->jumlah);
                        $baris++;
                      @endphp
                    @endforeach
                      <tr>
                        <th class="text-center" colspan="4">Total</th>
                        <td class="text-center">{{ number_format($total, 0, ',', '.') }}</td>
                      </tr>
                  </table>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-primary">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </form>
    <script>
        $(document).ready(function() {
          $("#form-delete").validate({
              rules: {},
              submitHandler: function (form) {
                  $.ajax({
                      url: form.action, 
                      type: form.method, 
                      data: $(form).serialize(), 
                      success: function(response) { 
                        if(response.status){ 
                          $('#myModal').modal('hide'); 
                          Swal.fire({ 
                              icon: 'success', 
                              title: 'Berhasil', 
                              text: response.message 
                          }); 
                          dataPenjualan.ajax.reload(null, false); 
                        } else { 
                          $('.error-text').text(''); 
                          $.each(response.msgField, function(prefix, val) { 
                            $('#error-'+prefix).text(val[0]); 
                          }); 
                          Swal.fire({ 
                              icon: 'error', 
                              title: 'Terjadi Kesalahan', 
                              text: response.message 
                          }); 
                        } 
                      }             
                    }); 
                  return false; 
                }, 
                errorElement: 'span', 
                errorPlacement: function (error, element) { 
                    error.addClass('invalid-feedback'); 
                    element.closest('.form-group').append(error); 
                }, 
                highlight: function (element, errorClass, validClass) { 
                    $(element).addClass('is-invalid'); 
                }, 
                unhighlight: function (element, errorClass, validClass) { 
                    $(element).removeClass('is-invalid'); 
                } 
            }); 
        }); 
    </script> 
@endempty 