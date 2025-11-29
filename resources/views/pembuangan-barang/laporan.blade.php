@extends('layouts.app')
@section('content_title', 'Laporan Pembuangan Barang')
@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Laporan Pembuangan Barang</h4>
    </div>
    <div class="card-body">
        <table class="table table-sm" id="table2">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nomor Pembuangan</th>
                    <th>Tanggal Pembuangan</th>
                    <th>Nama Petugas</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->nomor_pembuangan }}</td>
                        <td>{{ $item->tanggal_transaksi }}</td>
                        <td>{{ ucwords($item->nama_petugas) }}</td>
                        <td>
                            <a href="{{ route('laporan.pembuangan-barang.detail-laporan', $item->nomor_pembuangan) }}">
                                Detail
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection