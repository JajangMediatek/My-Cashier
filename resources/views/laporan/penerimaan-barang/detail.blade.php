@extends('layouts.app')
@section('content_title', 'Laporan Penerimaan Barang')
    @section('content')    
    <div class="card">
        <div class="d-flex justify-content-between align-items-center p-3">
            <div>
                <h3 class="h3">My Cashier</h3>
                <h4 class="h6">Laporan Penerimaan Barang</h4>
            </div>
            <div>
                <small>{{ $data->tanggal_penerimaan }}</small>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-4">
                    <div class="d-flex align-content-center">
                        <h6 class="text-bold w-25">Distributor</h6>
                        <p>{{ $data->distributor }}</p>
                    </div>
                    <div class="d-flex align-content-center">
                        <h6 class="text-bold w-25">Nomor Faktur</h6>
                        <p>{{ $data->nomor_faktur }}</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="d-flex align-content-center">
                        <h6 class="text-bold w-25">Nama Petugas Penerima</h6>
                        <p>{{ $data->petugas_penerima }}</p>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 20px" class="text-center">No.</th>
                            <th>Nama Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data->items as $index=>$item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $item->nama_produk }}</td>
                                <td>{{ number_format($item->qty) }} <small>pcs</small></td>
                                <td>Rp. {{ number_format($item->harga_beli) }}</td>
                                <td>Rp. {{ number_format($item->sub_total) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4" class="text-bold text-right">Total Pembelian</td>
                            <td class="text-bold">Rp. {{ number_format($data->total) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection