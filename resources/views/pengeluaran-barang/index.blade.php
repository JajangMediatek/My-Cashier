@extends('layouts.app')
@section('content_title', 'Pengeluaran Barang / Transaksi')
@section('content')
<div class="card">
    <x-alert :errors="$errors" />
    <form action="{{ route('pengeluaran-barang.store') }}" method="POST" id="form-pengeluaran-barang">
        @csrf
        <div id="data-hidden"></div>
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
        <h4 class="h5">Pengeluaran Barang / Transaksi</h4>
    </div>
    <div class="card-body">
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
                <label for="">Harga</label>
                <input type="number" id="harga_jual" class="form-control mx-1" style="width: 100px" readonly>
            </div>
            <div>
                <label for="">Qty</label>
                <input type="number" id="qty" class="form-control mx-1" style="width: 100px" min="1">
            </div>
            <div style="padding-top: 32px">
                <button type="button" class="btn btn-dark" id="btn-add">Tambahkan</button>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-9">
        <div class="card">
            <div class="card-body">
                <table class="table table-sm" id="table-produk">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Sub total</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody></tbody>
                    </table>
            </div>
        </div>
    </div>
        <div class="col-3">
            <div class="card">
                <div class="card-body">
                <div>
                    <label for="">Total</label>
                    <input type="number" class="form-control" id="total" readonly>
                </div>
                <div>
                    <label for="">Kembalian</label>
                    <input type="number" class="form-control" id="kembalian" readonly>
                </div>
                <div>
                    <label for="">Jumlah Bayar</label>
                    <input type="number" name="bayar" class="form-control" id="bayar" min="1">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary w-100 mt-2">Simpan Transaksi</button>
                </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            let selectedProduk = {};

            function hitungTotal(){
                let total = 0;

                $('#table-produk tbody tr').each(function(){
                    const subTotal = parseInt($(this).find('td:eq(3)').text()) || 0;
                    total += subTotal;
                });

                $("#total").val(total);
            }

        
          $('#select2').select2({
        theme:'bootstrap',
        placeholder:'Cari Produk...',
        ajax:{
            url:"{{ route('get-data.produk') }}",
            dataType:'json',
            delay:250,
            data:(params) => ({ search: params.term }),
            processResults:(data)=>{
                // simpan object produk (as-is) ke selectedProduk bila ada
                data.forEach(item => {
                    selectedProduk[item.id] = item;
                });

                return {
                    results: data.map((item)=>{
                        return { id:item.id, text:item.nama_produk }
                    })
                }
            },
            cache:true
                },

                minimumInputLength:3
            });

 // ketika user pilih produk di select2 => ambil stok & harga dan simpan ke selectedProduk
    $("#select2").on("change", function (e) {
        let id = $(this).val();

        if (!id) {
            // clear fields
            $("#current_stok").val('');
            $("#harga_jual").val('');
            return;
        }


        // cek stok
        $.ajax({
            type: "GET",
            url: "{{ route('get-data.cek-stok') }}",
            data: { id: id },
            dataType: "json",
            success: function (response) {
                // response diharapkan angka stok
                const stok = parseInt(response) || 0;
                $("#current_stok").val(stok);

                // simpan ke selectedProduk agar selalu tersedia saat validasi
                if (selectedProduk[id]) selectedProduk[id].stok = stok;
                else selectedProduk[id] = { id: id, stok: stok };
            },
            error: function () {
                $("#current_stok").val('');
            }
        });

 // cek harga
        $.ajax({
            type: "GET",
            url: "{{ route('get-data.cek-harga') }}",
            data: { id: id },
            dataType: "json",
            success: function (response) {
                const harga = parseInt(response) || 0;
                $("#harga_jual").val(harga);

                if (selectedProduk[id]) selectedProduk[id].harga_jual = harga;
                else selectedProduk[id] = { id: id, harga_jual: harga };
            },
            error: function () {
                $("#harga_jual").val('');
            }
        });
    });

              // tombol tambah
    $("#btn-add").on("click", function () {
        const selectedId = $("#select2").val();
        const qtyRaw = $("#qty").val();
        const qty = parseInt(qtyRaw);
        // prefer stok dari selectedProduk, fallback ke field current_stok
        const currentStokField = $("#current_stok").val();
        const currentStok = selectedProduk[selectedId] && selectedProduk[selectedId].stok !== undefined
            ? parseInt(selectedProduk[selectedId].stok)
            : (parseInt(currentStokField) || 0);
        const hargaJual = selectedProduk[selectedId] && selectedProduk[selectedId].harga_jual !== undefined
            ? parseInt(selectedProduk[selectedId].harga_jual)
            : (parseInt($("#harga_jual").val()) || 0);

                 if (!selectedId || isNaN(qty) || qty <= 0) {
            alert('Harap pilih produk dan tentukan jumlah yang valid!');
            return;
        }

                // jika stok tidak tersedia / stok = 0
        if (isNaN(currentStok) || currentStok <= 0) {
            alert('Stok produk tidak tersedia atau 0.');
            return;
        }

                 // cek qty vs stok
        // jika produk sudah ada di tabel, kita harus menjumlahkan current qty di tabel + qty yang akan ditambahkan
        const produk = selectedProduk[selectedId] || {};
        let sudahAda = false;
        let tableCurrentQty = 0;

        $('#table-produk tbody tr').each(function(){
            const rowId = $(this).data('id')?.toString();
            if (rowId === selectedId.toString()) {
                sudahAda = true;
                tableCurrentQty = parseInt($(this).find("td:eq(1)").text()) || 0;
            }
        });

        const newQty = tableCurrentQty + qty;
        const productStock = currentStok;

        if (newQty > productStock) {
            alert(`Stok tidak cukup! Tersedia hanya ${productStock} (sudah menambahkan ${tableCurrentQty}).`);
            return;
        }

        // jika item sudah ada, update qty di tabel
        if (sudahAda) {
            $('#table-produk tbody tr').each(function(){
                const rowId = $(this).data('id')?.toString();
                if (rowId === selectedId.toString()) {
                    $(this).find("td:eq(1)").text(newQty);
                    // update subtotal
                    const subTotal = newQty * hargaJual;
                    $(this).find("td:eq(3)").text(subTotal);
                }
            });
            hitungTotal();
            // reset input
            $("#select2").val(null).trigger("change");
            $("#qty").val(null);
            $("#current_stok").val(null);
            $("#harga_jual").val(null);
            return;
        }

        // kalau belum ada, tambahkan baris baru
        const subTotal = qty * hargaJual;
        const row = `
            <tr data-id="${produk.id}">
                <td>${produk.nama_produk ?? ''}</td>
                <td>${qty}</td>
                <td>${hargaJual}</td>
                <td>${subTotal}</td>
                <td>
                    <button class="btn btn-danger btn-sm btn-remove">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $("#table-produk tbody").append(row);

        // reset input
        $("#select2").val(null).trigger("change");
        $("#qty").val(null);
        $("#current_stok").val(null);
        $("#harga_jual").val(null);
        hitungTotal();
    });

    // hapus baris
    $("#table-produk").on("click",".btn-remove", function () {
        $(this).closest('tr').remove();
        hitungTotal();
    });

            $("#form-pengeluaran-barang").on("submit", function () {
                $("#data-hidden").html("");

                $("#table-produk tbody tr").each(function(index, row){
                    const namaProduk = $(row).find("td:eq(0)").text();
                    const qty = $(row).find("td:eq(1)").text();
                    const produkId = $(row).data("id");
                    const hargaJual = $(row).find("td:eq(2)").text();
                    const subTotal = $(row).find("td:eq(3)").text();


                    const inputProduk = `<input type="hidden" name="produk[${index}][nama_produk]"  value="${namaProduk}" />`;
                    const inputQty = `<input type="hidden" name="produk[${index}][qty]" value="${qty}" />`;
                    const inputProdukId = `<input type="hidden" name="produk[${index}][produk_id]" value="${produkId}" />`;
                    const inputHargaJual = `<input type="hidden" name="produk[${index}][harga_jual]" value="${hargaJual}" />`;
                    const inputSubTotal = `<input type="hidden" name="produk[${index}][sub_total]" value="${subTotal}" />`;

                    $("#data-hidden").append(inputProduk).append(inputQty).append(inputProdukId).append(inputSubTotal).append(inputHargaJual);
                });

            });

            $("#bayar").on("input", function () {
                const total = parseInt($("#total").val()) || 0;
                const bayar = parseInt($(this).val()) || 0;
                const kembalian = bayar - total;

                $("#kembalian").val(kembalian);
            });

        });
    </script>
@endpush