@extends('layouts.app')
@section('content_title', 'Detail Pembuangan')
@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Laporan Pembuangan Barang #{{ $data->nomor_pembuangan }}</h4>
    </div>
    <div class="card-body">
        <p class="m-0">Tanggal : <strong>{{ $data->tanggal_pembuangan }}</strong></p>
        <p class="m-0">Nama Petugas : <strong>{{ $data->nama_petugas }}</strong></p>
        <table class="table table-sm table-bordered mt-3" id="id">
            <thead>
                <tr>
                    <th class="text-center" style="width: 20px">No.</th>
                    <th>Produk</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->nama_produk }}</td>
                        <td>{{ number_format($item->qty) }} <small>pcs</small></td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" class="text-right text-bold">
                        Total Qty
                    </td>
                    <td class="text-bold">
                        {{ number_format($data->total_qty) }} <small>pcs</small>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="white-space: normal; word-break: break-word;">
                        <strong>Keterangan:</strong> <br>
                        {{ $data->keterangan }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection