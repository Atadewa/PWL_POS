@empty($penjualan)
    <div id="modal-edit-penjualan" class="modal-dialog modal-lg" role="document"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5> 
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button> 
            </div> 
            <div class="modal-body"> 
                <div class="alert alert-danger"> 
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5> 
                    Data penjualan yang anda cari tidak ditemukan
                </div> 
                <a href="{{ url('/penjualan') }}" class="btn btn-warning">Kembali</a> 
            </div> 
        </div> 
    </div> 
@else
    <form id="form-edit-penjualan" action="{{ url('/penjualan/' . $penjualan->penjualan_id . '/update_ajax') }}" method="POST">
        @csrf
        @method('PUT')
        <div id="modal-edit-penjualan" class="modal-dialog modal-lg" role="document"> 
            <div class="modal-content"> 
                <div class="modal-header"> 
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data Penjualan</h5> 
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button> 
                </div> 
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pembeli</label>
                        <input type="text" name="pembeli" class="form-control" value="{{ $penjualan->pembeli }}" required>
                        <small class="text-danger error-text" id="error-pembeli"></small>
                    </div>
                
                    <div class="form-group">
                        <label>Tanggal Penjualan</label>
                        <input type="datetime-local" name="penjualan_tanggal" id="penjualan_tanggal_edit" class="form-control" value="{{ \Carbon\Carbon::parse($penjualan->penjualan_tanggal)->format('Y-m-d\TH:i') }}" required>
                        <small class="text-danger error-text" id="error-penjualan_tanggal"></small>
                    </div>
                
                    <label>Detail Barang</label>
                    <table class="table table-bordered" id="tabel-barang-edit">
                        <thead>
                            <tr>
                                <th style="width: 30%;">Barang</th>
                                <th style="width: 12%;">Jumlah</th>
                                <th style="width: 12%;">Stok</th>
                                <th style="width: 20%;">Harga</th>
                                <th style="width: 20%;">Subtotal</th>
                                <th style="width: 6%;"><button type="button" class="btn btn-sm btn-success" id="tambah-baris-edit">+</button></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detail as $d)
                                <tr>
                                    <td>
                                        <select name="barang_id[]" class="form-control barang-select-edit" required>
                                            <option value="">- Pilih -</option>
                                            @foreach($barang as $b)
                                                <option value="{{ $b->barang_id }}" {{ $b->barang_id == $d->barang_id ? 'selected' : '' }}>
                                                    {{ $b->barang_kode }} - {{ $b->barang_nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="jumlah[]" class="form-control jumlah-edit" min="1" value="{{ $d->jumlah }}" required></td>
                                    <td><input type="text" class="form-control stok-edit" value="{{ $stok->where('barang_id', $d->barang_id)->first()->stok_jumlah ?? 0 }}" readonly></td>
                                    <td><input type="text" class="form-control harga-edit" value="Rp {{ number_format($d->harga, 0, ',', '.') }}" data-harga="{{ $d->harga }}" readonly></td>
                                    <td><input type="text" class="form-control subtotal-edit" value="Rp {{ number_format($d->jumlah * $d->harga, 0, ',', '.') }}" readonly></td>
                                    <td><button type="button" class="btn btn-sm btn-danger btn-hapus-edit">X</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total</th>
                                <th id="total-harga-edit"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div> 
                <div class="modal-footer"> 
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button> 
                    <button type="submit" class="btn btn-primary">Simpan</button> 
                </div> 
            </div> 
        </div> 
    </form>

    <script>
        if (typeof barangDataEdit === 'undefined') {
            var barangDataEdit = @json($barang);
            var stokDataEdit = @json($stok);
            var barangMapEdit = {};

            barangDataEdit.forEach(barang => {
                const stokBarang = stokDataEdit.find(s => s.barang_id == barang.barang_id);
                barangMapEdit[barang.barang_id] = {
                    nama: barang.barang_nama,
                    stok: stokBarang ? stokBarang.stok_jumlah : 0,
                    harga: barang.harga_jual ?? 10000,
                };
            });
        }

        function formatRupiah(number) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        }

        function updateBarangSelectEdit() {
            let usedBarang = [];
            $('#tabel-barang-edit tbody tr select[name="barang_id[]"]').each(function () {
                usedBarang.push($(this).val());
            });

            $('#tabel-barang-edit tbody tr').each(function () {
                const select = $(this).find('.barang-select-edit');
                const currentValue = select.val();

                const barangOptions = barangDataEdit
                    .filter(b => !usedBarang.includes(String(b.barang_id)) || String(b.barang_id) === currentValue)
                    .map(b => `<option value="${b.barang_id}">${b.barang_kode} - ${b.barang_nama}</option>`)
                    .join('');

                select.html(`<option value="">- Pilih -</option>${barangOptions}`);
                select.val(currentValue);
            });
        }

        function updateStokTabelEdit() {
            $('#tabel-barang-edit tbody tr').each(function () {
                const row = $(this);
                const barangId = row.find('.barang-select-edit').val();
                if (barangId && barangMapEdit[barangId]) {
                    row.find('.stok-edit').val(barangMapEdit[barangId].stok);
                    row.find('.jumlah-edit').attr('max', barangMapEdit[barangId].stok);
                }
            });
        }

        function hitungTotalEdit() {
            let total = 0;
            $('#tabel-barang-edit tbody tr').each(function () {
                const subtotal = parseInt($(this).find('.subtotal-edit').val().replace(/[^0-9]/g, '')) || 0;
                total += subtotal;
            });
            $('#total-harga-edit').text(formatRupiah(total));
        }

        function tambahBarisEdit() {
            let usedBarang = [];
            $('#tabel-barang-edit tbody tr select[name="barang_id[]"]').each(function () {
                usedBarang.push($(this).val());
            });

            const barangOptions = barangDataEdit
                .filter(b => !usedBarang.includes(String(b.barang_id)))
                .map(b => `<option value="${b.barang_id}">${b.barang_kode} - ${b.barang_nama}</option>`)
                .join('');

            if (barangOptions === '') {
                alert("Semua barang sudah dipilih!");
                return;
            }

            const row = `
                <tr>
                    <td>
                        <select name="barang_id[]" class="form-control barang-select-edit" required>
                            <option value="">- Pilih -</option>
                            ${barangOptions}
                        </select>
                    </td>
                    <td><input type="number" name="jumlah[]" class="form-control jumlah-edit" min="1" required></td>
                    <td><input type="text" class="form-control stok-edit" readonly></td>
                    <td><input type="text" class="form-control harga-edit" readonly></td>
                    <td><input type="text" class="form-control subtotal-edit" readonly></td>
                    <td><button type="button" class="btn btn-sm btn-danger btn-hapus-edit">X</button></td>
                </tr>`;
                
            $('#tabel-barang-edit tbody').append(row);
        }

        $(document).off('click', '#tambah-baris-edit').on('click', '#tambah-baris-edit', tambahBarisEdit);

        $(document).on('click', '.btn-hapus-edit', function () {
            $(this).closest('tr').remove();
            updateBarangSelectEdit();
            hitungTotalEdit();
        });

        $(document).on('change', '.barang-select-edit', function () {
            const barangId = $(this).val();
            const row = $(this).closest('tr');

            if (!barangId) return;

            const data = barangMapEdit[barangId];
            row.find('.stok-edit').val(data.stok);
            row.find('.harga-edit').val(formatRupiah(data.harga)).attr('data-harga', data.harga);
            row.find('.jumlah-edit').attr('max', data.stok);
            row.find('.jumlah-edit').val(1).trigger('input');
        });

        $(document).on('input', '.jumlah-edit', function () {
            const row = $(this).closest('tr');
            const jumlah = parseInt($(this).val()) || 0;
            const barangId = row.find('.barang-select-edit').val();
            const harga = parseInt(row.find('.harga-edit').attr('data-harga')) || 0;
            const subtotal = jumlah * harga;
            row.find('.subtotal-edit').val(formatRupiah(subtotal));
            hitungTotalEdit();
        });

        $(document).ready(function () {
            updateBarangSelectEdit();
            updateStokTabelEdit();
            hitungTotalEdit();

            $('#form-edit-penjualan').on('submit', function (e) {
                e.preventDefault();
                const form = this;

                $.ajax({
                    url: form.action,
                    method: form.method,
                    data: $(form).serialize(),
                    success: function (res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Data penjualan berhasil diperbarui.',
                            }).then(() => {
                                $('#myModal').modal('hide');
                                dataPenjualan.ajax.reload(null, false);
                            });
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                            $('.error-text').text('');
                            $.each(res.msgField, function (key, val) {
                                $('#error-' + key).text(val[0]);
                            });
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
                    }
                });
            });
        });
    </script>
@endempty