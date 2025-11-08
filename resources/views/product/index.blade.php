@extends('layouts.app')
@section('content_title', 'Data Produk')
@section('content')

    <div class="card">
        <div class="p-2 d-flex justify-content-between border">
            <h4 class="h5">Data Produk</h4>
            <div class="d-flex justify-content-end mb-2">
                    <x-product.form-product/>
            </div>
        </div>
        <div class="card-body">
            @if($stokMenipis->count() > 0)
            <div class="alert alert-warning">
                <strong>Perhatian!</strong> Ada {{ $stokMenipis->count() }} produk dengan stok menipis:
                <ul>
                    @foreach($stokMenipis as $item)
                        <li>
                            <strong>{{ $item->nama_produk }}</strong> —
                            Sisa {{ $item->stok }} dari minimal {{ $item->stok_minimal }}
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
            <table class="table table-sm" id="table2">
                <x-alert :errors="$errors" />
                <thead>
                    <tr>
                        <th class="text-center" style="width: 29px" >No.</th>
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
                        <tr
                        @if($product->stok <= $product->stok_minimal)
                            style="background-color: #ffe6e6;" {{-- warna merah muda biar menonjol --}}
                        @endif>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->nama_produk }}</td>
                            <td>Rp. {{ number_format($product->harga_jual) }}</td>
                            <td>Rp. {{ number_format($product->harga_beli_pokok) }}</td>
                            <td>{{ number_format($product->stok) }}</td>
                            <td>
                                <p class="badge {{ $product->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </p>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-product.form-product :id="$product->id"/>
                                        <a href="{{ route('master-data.product.destroy', $product->id) }}" class="btn btn-danger mx-1" data-confirm-delete="true">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection