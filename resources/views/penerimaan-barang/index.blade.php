@extends('layouts.app')
@section('content_title', 'Penerimaan Barang')
@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Penerimaan Barang</h4>
    </div>
    <div class="card-body">
        <div class="d-flex">
            <div class="w-100">
                <label for="">Produk</label>
                <select name="select2" id="select2" class="form-control"></select>
            </div>
            <div>
                <label for="">Stok Tersedia</label>
                <input type="number" name="current_stock" id="current_stok" class="form-control mx-1" style="width: 100px" readonly>
            </div>
            <div>
                <label for="">Qty</label>
                <input type="number" name="qty" id="qty" class="form-control mx-1" style="width: 100px" min="1">
            </div>
            <div style="padding-top: 32px">
                <button class="btn btn-dark" id="btn-add">Tambahkan</button>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <table class="table table-sm" id="table-produk">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Qty</th>
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
                minimumInputLength:5
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
                    <tr>
                        <td>${produk.nama_produk}</td>
                        <td>${qty}</td>
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
                $("#current_stok").val(null);

            });

            $("#table-produk").on("click",".btn-remove", function () {
                $(this).closest('tr').remove();
            });

        });
    </script>
@endpush