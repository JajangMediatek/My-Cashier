@extends('layouts.app')
@section('content_title', 'Penerimaan Barang')
@section('content')
<div class="card">
    <form action="{{ route('penerimaan-barang.store') }}" method="POST" id="form-penerimaan-barang">
        @csrf
        <div id="data-hidden"></div>
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
        <h4 class="h5">Penerimaan Barang</h4>
        <div>
            <button type="submit" class="btn btn-primary">Simpan Penerimaan Barang</button>
        </div>
    </div>
    <div class="card-body">
        <div class="w-50">
            <div class="form-group my-1">
                <label for="">Dsitributor</label>
                <input type="text" name="distributor" id="distributor" class="form-control" value="{{ old('distributor') }}">
                @error('distributor')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group  my-1">
                <label for="">Nomor Faktur</label>
                <input type="text" name="nomor_faktur" id="nomor_faktur" class="form-control" value="{{ old('nomor_faktur') }}">
                @error('nomor_faktur')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>
        <div class="d-flex">
            <div class="w-100">
                <label for="">Produk</label>
                <select name="select2" id="select2" class="form-control"></select>
            </div>
            <div>
                <label for="">Stok Tersedia</label>
                <input type="number" id="current_stok" class="form-control mx-1" style="width: 100px" readonly>
            </div>
            <div>
                <label for="">Qty</label>
                <input type="number" id="qty" class="form-control mx-1" style="width: 100px" min="1">
            </div>
            <div>
                <label for="">Harga Beli</label>
                <input type="number" id="harga_beli" class="form-control mx-1" style="width: 300px" min="1">
            </div>
            <div style="padding-top: 32px">
                <button type="button" class="btn btn-dark" id="btn-add">Tambahkan</button>
            </div>
        </div>
    </div>
    </form>
</div>
<div class="card">
    <div class="card-body">
        <table class="table table-sm" id="table-produk">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Qty</th>
                    <th>Harga Beli</th>
                    <th>Sub total</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            let selectedProduk = {};
            $('#select2').select2({
                theme:'bootstrap',
                placeholder:'Cari Produk...',
                ajax:{
                    url:"{{ route('get-data.produk') }}",
                    dataType:'json',
                    delay:250,
                    data:(params) => {
                       return {
                        search:params.term
                       }
                    },
                    processResults:(data)=>{
                        data.forEach(item => {
                            selectedProduk[item.id] = item;
                        })

                        return {
                            results:data.map((item)=>{
                                return {
                                    id:item.id,
                                    text:item.nama_produk
                                }
                            })
                        }
                    },
                    cache:true
                },
                minimumInputLength:3
            })  

            $("#select2").on("change", function (e) {
                let id = $(this).val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('get-data.cek-stok') }}",
                    data: {
                        id:id
                    },
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                        $("#current_stok").val(response);
                    }
                });
            });

            $("#btn-add").on("click", function () {
                const selectedId = $("#select2").val();
                const qty = $("#qty").val();
                const currentStok = $("#current_stok").val();
                const hargaBeli = $('#harga_beli').val();
                const subTotal = parseInt(qty) * parseInt(hargaBeli);

                if(!selectedId || !qty){
                    alert('Harap pilih produk dan tentukan jumlahnya');
                return;
                }

                const produk = selectedProduk[selectedId];
                let exist = false;
                $('#table-produk tbody tr').each(function(){
                    const rowProduk = $(this).find("td:first").text();

                    if (rowProduk === produk.nama_produk) {
                        let currentQty = parseInt($(this).find("td:eq(1)").text());
                        let newQty = currentQty + parseInt(qty);

                        $(this).find("td:eq(1)").text(newQty);
                        exist = true;
                        return false;
                    }
                })

                if (!exist) {
                    // tambahkan data baru
                    const row = `
                    <tr data-id="${produk.id}">
                        <td>${produk.nama_produk}</td>
                        <td>${qty}</td>
                        <td>${hargaBeli}</td>
                        <td>${subTotal}</td>
                        <td>
                            <button class="btn btn-danger btn-sm btn-remove">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    `
                    $("#table-produk tbody").append(row);
                }

                $("#select2").val(null).trigger("change");
                $("#qty").val(null);
                $("#harga_beli").val(null);
                $("#current_stok").val(null);

            });

            $("#table-produk").on("click",".btn-remove", function () {
                $(this).closest('tr').remove();
            });

            $("#form-penerimaan-barang").on("submit", function () {
                $("#data-hidden").html("");

                $("#table-produk tbody tr").each(function(index, row){
                    const namaProduk = $(row).find("td:eq(0)").text();
                    const qty = $(row).find("td:eq(1)").text();
                    const produkId = $(row).data("id");
                    const hargaBeli = $(row).find("td:eq(2)").text();
                    const subTotal = $(row).find("td:eq(3)").text();


                    const inputProduk = `<input type="hidden" name="produk[${index}][nama_produk]"  value="${namaProduk}" />`;
                    const inputQty = `<input type="hidden" name="produk[${index}][qty]" value="${qty}" />`;
                    const inputProdukId = `<input type="hidden" name="produk[${index}][produk_id]" value="${produkId}" />`;
                    const inputHargaBeli = `<input type="hidden" name="produk[${index}][harga_beli]" value="${hargaBeli}" />`;
                    const inputSubTotal = `<input type="hidden" name="produk[${index}][sub_total]" value="${subTotal}" />`;

                    $("#data-hidden").append(inputProduk).append(inputQty).append(inputProdukId).append(inputHargaBeli).append(inputSubTotal);
                });

            });

        });
    </script>
@endpush