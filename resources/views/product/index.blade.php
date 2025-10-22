@extends('layouts.app')
@section('content_title', 'Data Produk')
@section('content')

    <div class="card">
        <div class="card-title">
            <h4 class="card-header">Data produk</h4>
        </div>
        <div class="card-body">
            <table class="table table-sm" id="table2">
                <div class="d-flex justify-content-end mb-2">
                    <x-product.form-product/>
                </div>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>SKU</th>
                        <th>Nama produk</th>
                        <th>Harga Jual</th>
                        <th>Harga Beli</th>
                        <th>Stok</th>
                        <th>Aktif</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->nama_produk }}</td>
                            <td>Rp. {{ number_format($product->harga_jual) }}</td>
                            <td>Rp. {{ number_format($product->harga_beli) }}</td>
                            <td>{{ number_format($product->stok) }}</td>
                            <td>{{ $product->is_actives }}</td>
                            <td>
                                <div>
                                    <x-product.form-product :id="$product-id"/>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection