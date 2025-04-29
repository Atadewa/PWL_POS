<html> 
<head> 
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 
  <style> 
    body{ 
      font-family: "Times New Roman", Times, serif; 
      margin: 6px 20px 5px 20px; 
      line-height: 15px; 
    } 
    table { 
      width:100%;  
      border-collapse: collapse; 
    } 
    td, th { 
      padding: 4px 3px; 
    } 
    th{ 
      text-align: left; 
    } 
    .d-block{ 
      display: block; 
    } 
    img.image{ 
      width: auto; 
      height: 80px; 
      max-width: 150px; 
      max-height: 150px; 
    } 
    .text-right { 
      text-align: right; 
    }
    .text-center { 
      text-align: center; 
    } 
    .p-1{ 
      padding: 5px 1px 5px 1px; 
    } 
    .font-10{ 
      font-size: 10pt; 
    } 
    .font-11{ 
      font-size: 11pt; 
    } 
    .font-12{ 
      font-size: 12pt; 
    } 
    .font-13{ 
      font-size: 13pt; 
    } 
    .border-bottom-header{ 
      border-bottom: 1px solid; 
    } 
    .border-all, .border-all th, .border-all td{ 
      border: 1px solid; 
    } 
    .table-barang {
      width: 100%;
      border-collapse: collapse;
    }
    .table-barang td {
      border: none;
      border-bottom: 1px solid black;
      padding: 2x 4px;
    }
    .table-barang tr:last-child td {
      border-bottom: none;
    }
    .border-all td:nth-child(5),
    .border-all td:nth-child(6),
    .border-all td:nth-child(7),
    .border-all td:nth-child(8) {
      padding: 1px 0;
    }
    .border-bottom-header td:nth-child(2) {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }
    .border-bottom-header {
      margin: 0 auto;
      width: 100%;
      border-collapse: collapse;
    }
    .border-bottom-header td:first-child {
      vertical-align: middle;
    }
  </style> 
</head> 
<body> 
  <table class="border-bottom-header"> 
    <tr> 
      <td width="15%" class="text-center"><img src="{{ asset('img/polinema-bw.png') }}" class="image"></td> 
      <td width="85%"> 
        <span class="text-center d-block font-11 font-bold mb-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</span> 
        <span class="text-center d-block font-13 font-bold mb-1">POLITEKNIK NEGERI MALANG</span> 
        <span class="text-center d-block font-10">Jl. Soekarno-Hatta No. 9 Malang 65141</span> 
        <span class="text-center d-block font-10">Telepon (0341) 404424 Pes. 101-105, 0341-404420, Fax. (0341) 404420</span> 
        <span class="text-center d-block font-10">Laman: www.polinema.ac.id</span> 
      </td> 
    </tr> 
  </table> 

  <h3 class="text-center">LAPORAN DATA PENJUALAN</h4> 
  <table class="border-all"> 
    <thead> 
      <tr> 
        <th class="text-center">No</th> 
        <th class="text-center">Kode Penjualan</th> 
        <th class="text-center">Nama Kasir</th> 
        <th class="text-center">Pembeli</th> 
        <th class="text-center">Barang</th> 
        <th class="text-center">Kuantitas</th> 
        <th class="text-center">Harga</th> 
        <th class="text-center">SubTotal</th> 
        <th class="text-center">Total</th>
        <th class="text-center">Tanggal Penjualan</th> 
      </tr> 
    </thead> 
    <tbody> 
      @foreach($penjualan as $p) 
      <tr> 
        <td class="text-center">{{ $loop->iteration }}</td> 
        <td>{{ $p->penjualan_kode }}</td> 
        <td>{{ $p->user->nama }}</td> 
        <td>{{ $p->pembeli }}</td> 
        <td>
          <table class="table-barang"> 
            @foreach ($detail as $d)
                @if ($d->penjualan_id === $p->penjualan_id)
                  <tr>
                    <td>{{ $d->barang->barang_nama }}</td>
                  </tr>
                @endif
            @endforeach
          </table>
        </td>
        <td>
          <table class="table-barang">  
            @foreach ($detail as $d)
                @if ($d->penjualan_id === $p->penjualan_id)
                  <tr>
                    <td class="text-center">{{ $d->jumlah }}</td>
                  </tr>
                @endif
            @endforeach
          </table>
        </td> 
        <td>
          <table class="table-barang"> 
            @foreach ($detail as $d)
                @if ($d->penjualan_id === $p->penjualan_id)
                  <tr>
                    <td>{{ number_format($d->barang->harga_jual, 0, ',', '.') }}</td>
                  </tr>f
                @endif
            @endforeach
          </table>
        </td> 
        <td>
          <table class="table-barang">  
            @foreach ($detail as $d)
                @if ($d->penjualan_id === $p->penjualan_id)
                @php
                  $subtotal = 0;
                  $subtotal = ($d->barang->harga_jual*$d->jumlah)
                @endphp
                  <tr>
                    <td>{{ number_format($subtotal, 0, ',', '.') }}</td>
                  </tr>
                @endif
            @endforeach
          </table>
        </td>  
        <td class="text-center">
          @php
            $total = 0;
            foreach ($detail as $d) {
                if ($d->penjualan_id === $p->penjualan_id) {
                    $total += $d->barang->harga_jual * $d->jumlah;
                }
            }
          @endphp
          {{ number_format($total, 0, ',', '.') }}
        </td>        
        <td class="text-center">{{ $p->penjualan_tanggal }}</td> 
      </tr> 
      @endforeach 
    </tbody> 
  </table> 
</body> 
</html> 