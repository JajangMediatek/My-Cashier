@extends('layouts.app')
@section('content_title', 'Laporan Pengeluaran Barang')
@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Laporan Pengeluaran Barang (Transaksi)</h4>
    </div>
    <div class="card-body">
        <table class="table table-sm" id="table2">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nomor Pengeluaran</th>
                    <th>Tanggal Transaksi</th>
                    <th>Total Harga</th>
                    <th>Total Bayar</th>
                    <th>Kembalian</th>
                    <th>Nama Petugas</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->nomor_pengeluaran }}</td>
                        <td>{{ $item->tanggal_transaksi }}</td>
                        <td>{{ number_format($item->total_harga) }}</td>
                        <td>{{ number_format($item->bayar) }}</td>
                        <td>{{ number_format($item->kembalian) }}</td>
                        <td>{{ ucwords($item->nama_petugas) }}</td>
                        <td>
                            <a href="{{ route('laporan.pengeluaran-barang.detail-laporan', $item->nomor_pengeluaran) }}">
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