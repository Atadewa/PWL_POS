<form id="form-tambah" action="{{ url('penjualan/ajax') }}" method="POST">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Stok</h5> 
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button> 
            </div> 
            <div class="modal-body">
                <div class="form-group">
                    <label>Pembeli</label>
                    <input type="text" name="pembeli" class="form-control" required>
                    <small class="text-danger error-text" id="error-pembeli"></small>
                </div>
            
                <div class="form-group">
                    <label>Tanggal Penjualan</label>
                    <input type="datetime-local" name="penjualan_tanggal" id="penjualan_tanggal" class="form-control" required>
                    <small class="text-danger error-text" id="error-penjualan_tanggal"></small>
                </div>
            
                <label>Detail Barang</label>
                <table class="table table-bordered" id="tabel-barang">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Barang</th>
                            <th style="width: 12%;">Jumlah</th>
                            <th style="width: 12%;">Stok</th>
                            <th style="width: 20%;">Harga</th>
                            <th style="width: 20%;">Subtotal</th>
                            <th style="width: 6%;"><button type="button" class="btn btn-sm btn-success" id="tambah-baris">+</button></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Total</th>
                            <th id="total-harga">Rp 0</th>
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

<style>
    #tabel-barang {
        table-layout: fixed;
        width: 100%;
    }

    #tabel-barang td, #tabel-barang th {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .bg-readonly {
        background-color: #e9ecef; /* Warna abu-abu seperti readonly */
        padding: 5px 10px;
        display: block;
        border-radius: 4px;
        white-space: nowrap; /* Mencegah teks membungkus ke baris baru */
        overflow: hidden; /* Memotong teks yang terlalu panjang */
        text-overflow: ellipsis; /* Menambahkan elipsis (...) pada teks yang terpotong */
    }
</style>

<script>
    if (typeof barangData === 'undefined') {
        var barangData = @json($barang);
        var stokData = @json($stok);
        var barangMap = {};

        barangData.forEach(barang => {
            const stokBarang = stokData.find(s => s.barang_id == barang.barang_id);
            barangMap[barang.barang_id] = {
                nama: barang.barang_nama,
                stok: stokBarang ? stokBarang.stok_jumlah : 0,
                harga: barang.harga_jual ?? 10000,
            };
        });
    }

    function formatRupiah(number) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
    }

    function ambilStokBaru() {
        $.get('{{ url('/penjualan/stok-json') }}', function (res) {
            const { barang, stok } = res;

            // Kosongkan array global
            barangData.splice(0, barangData.length);
            stokData.splice(0, stokData.length);

            // Perbarui array global
            barangData.push(...barang);
            stokData.push(...stok);

            // Perbarui barangMap
            Object.keys(barangMap).forEach(key => delete barangMap[key]); // Kosongkan barangMap
            barangData.forEach(barang => {
                const stokBarang = stok.find(s => s.barang_id == barang.barang_id);
                barangMap[barang.barang_id] = {
                    nama: barang.barang_nama,
                    stok: stokBarang ? stokBarang.stok_jumlah : 0,
                    harga: barang.harga_jual ?? 10000,
                };
            });

            // Perbarui UI (dropdown barang dan stok di tabel)
            updateBarangSelect();
            updateStokTabel();
        }).fail(function (xhr) {
            console.error('Gagal mengambil stok baru:', xhr.responseText);
            Swal.fire('Error', 'Gagal mengambil stok terbaru.', 'error');
        });
    }

    function updateBarangSelect() {
        // Ambil barang yang sudah digunakan
        let usedBarang = [];
        $('#tabel-barang tbody tr select[name="barang_id[]"]').each(function () {
            usedBarang.push($(this).val());
        });

        // Perbarui semua dropdown
        $('#tabel-barang tbody tr').each(function () {
            const select = $(this).find('.barang-select');
            const currentValue = select.val();

            // Buat opsi baru
            const barangOptions = barangData
                .filter(b => !usedBarang.includes(String(b.barang_id)) || String(b.barang_id) === currentValue)
                .map(b => `<option value="${b.barang_id}">${b.barang_kode} - ${b.barang_nama}</option>`)
                .join('');

            // Perbarui dropdown
            select.html(`<option value="">- Pilih -</option>${barangOptions}`);
            select.val(currentValue); // Kembalikan nilai yang dipilih sebelumnya
        });
    }

    function updateStokTabel() {
        // Perbarui kolom stok di tabel
        $('#tabel-barang tbody tr').each(function () {
            const row = $(this);
            const barangId = row.find('.barang-select').val();
            if (barangId && barangMap[barangId]) {
                row.find('.stok').val(barangMap[barangId].stok);
                row.find('.jumlah').attr('max', barangMap[barangId].stok);
            }
        });
    }

    function hitungTotal() {
        let total = 0;
        $('#tabel-barang tbody tr').each(function () {
            const subtotal = parseInt($(this).find('.subtotal').attr('data-subtotal')) || 0;
            total += subtotal;
        });
        $('#total-harga').text(formatRupiah(total));
    }

    function tambahBaris() {
        let usedBarang = [];
        $('#tabel-barang tbody tr select[name="barang_id[]"]').each(function () {
            usedBarang.push($(this).val());
        });

        const barangOptions = barangData
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
                    <select name="barang_id[]" class="form-control barang-select" required>
                        <option value="">- Pilih -</option>
                        ${barangOptions}
                    </select>
                </td>
                <td><input type="number" name="jumlah[]" class="form-control jumlah" min="1" required></td>
                <td><input type="text" class="form-control stok" readonly value="0"></td>
                <td><input type="text" class="form-control harga" readonly value="0"></td>
                <td><span class="subtotal form-control bg-readonly" data-subtotal="0">Rp 0</span></td>
                <td><button type="button" class="btn btn-sm btn-danger btn-hapus">X</button></td>
            </tr>`;
            
        $('#tabel-barang tbody').append(row);
    }

    $(document).off('click', '#tambah-baris').on('click', '#tambah-baris', tambahBaris);

    $(document).on('click', '.btn-hapus', function () {
        $(this).closest('tr').remove();
        hitungTotal();
    });

    $(document).on('change', '.barang-select', function () {
        const barangId = $(this).val();
        const row = $(this).closest('tr');

        if (!barangId) return;

        const data = barangMap[barangId];
        row.find('.stok').val(data.stok);
        row.find('.harga').val(formatRupiah(data.harga));
        row.find('.jumlah').attr('max', data.stok);
        row.find('.jumlah').val(1).trigger('input');
    });

    $(document).on('input', '.jumlah', function () {
        const row = $(this).closest('tr');
        const jumlah = parseInt($(this).val()) || 0;
        const barangId = row.find('.barang-select').val();
        const harga = barangMap[barangId]?.harga || 0;
        const subtotal = jumlah * harga;
        const formattedSubtotal = formatRupiah(subtotal);
        row.find('.subtotal').text(formattedSubtotal).attr('data-subtotal', subtotal);
        hitungTotal();
    });

    $(document).ready(function () {
        const now = new Date();
        now.setMinutes(now.getMinutes() + now.getTimezoneOffset() + 420);

        // Format tanggal manual ke input type datetime-local (yyyy-MM-ddTHH:mm)
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        const formatted = `${year}-${month}-${day}T${hours}:${minutes}`;
        $('#penjualan_tanggal').val(formatted);

        $('#form-tambah').on('submit', function (e) {
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
                        text: 'Data penjualan berhasil ditambahkan.',
                        }).then(() => {
                        $('#myModal').modal('hide');
                        dataPenjualan.ajax.reload(null, false); // reload datatable tanpa reload page
                        ambilStokBaru();
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