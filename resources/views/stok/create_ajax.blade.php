<form action="{{ url('/stok/ajax') }}" method="POST" id="form-tambah"> 
  @csrf 
  <div id="modal-master" class="modal-dialog modal-lg" role="document"> 
      <div class="modal-content"> 
          <div class="modal-header"> 
              <h5 class="modal-title" id="exampleModalLabel">Tambah Data Stok</h5> 
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
          </div> 
          <div class="modal-body"> 
              <div class="form-group"> 
                  <label>Supplier</label> 
                  <select name="supplier_id" id="supplier_id" class="form-control" required> 
                      <option value="">- Pilih Supplier -</option> 
                      @foreach($supplier as $i) 
                          <option value="{{ $i->supplier_id }}">{{ $i->supplier_nama }}</option> 
                      @endforeach 
                  </select> 
                  <small id="error-supplier_id" class="error-text form-text text-danger"></small> 
              </div> 
              <div class="form-group"> 
                  <label>Barang</label> 
                  <select name="barang_id" id="barang_id" class="form-control" required> 
                      <option value="">- Pilih Barang -</option> 
                      @foreach($barang as $i) 
                          <option value="{{ $i->barang_id }}">{{ $i->barang_nama }}</option> 
                      @endforeach 
                  </select> 
                  <small id="error-barang_id" class="error-text form-text text-danger"></small> 
              </div> 
              <div class="form-group">
                <label>Tanggal Stok</label>
                <input type="datetime-local" name="stok_tanggal" id="stok_tanggal" class="form-control" required>
                <small id="error-stok_tanggal" class="error-text form-text text-danger"></small>
              </div>
              <div class="form-group"> 
                <label>Jumlah Stok</label> 
                <input value="" type="number" name="stok_jumlah" id="stok_jumlah" class="form-control" required min="0"> 
                <small id="error-stok_jumlah" class="error-text form-text text-danger"></small> 
              </div> 
          </div> 
          <div class="modal-footer"> 
            <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button> 
            <button type="submit" class="btn btn-primary">Simpan</button> 
        </div> 
    </div> 
</div> 
</form> 

<script> 
    $(document).ready(function() {
        const now = new Date();
        now.setMinutes(now.getMinutes() + now.getTimezoneOffset() + 420);

        // Format tanggal manual ke input type datetime-local (yyyy-MM-ddTHH:mm)
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        const formatted = `${year}-${month}-${day}T${hours}:${minutes}`;
        $('#stok_tanggal').val(formatted);

        $("#form-tambah").validate({ 
            rules: { 
                supplier_id: {required: true, number: true}, 
                barang_id: {required: true, number: true}, 
                stok_tanggal: {required: true, date:true}, 
                stok_jumlah: {required: true, number: true, min: 0}, 
            }, 
            submitHandler: function(form) { 
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
                            dataStok.ajax.reload(null, false); 
                        }else{ 
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