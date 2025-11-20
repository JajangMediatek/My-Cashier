<?php

namespace App\Http\Controllers;

use App\Models\ItemPengeluaranBarang;
use App\Models\PengeluaranBarang;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengeluaranBarangController extends Controller
{
    public function index(){
        return view('pengeluaran-barang.index');
    }

    public function store(Request $request){    
        if(empty($request->produk)){
            toast()->error('Tidak ada produk yang dipilih');
            return redirect()->back();
        }

        $request->validate([
            'produk' => 'required|array|min:1',
            'bayar'  => 'required|numeric|min:1',
        ],[
            'produk.required' => 'Produk harus dipilih',
            'bayar.required'  => 'Bayar harus diisi',
            'bayar.numeric'   => 'Bayar harus berupa angka',
            'bayar.min'       => 'Bayar minimal 1'
        ]);

        $produk = collect($request->produk);
        $bayar = $request->bayar;
        $total = $produk->sum('sub_total');
        $kembalian = intval($bayar) - intval($total);

        if ($bayar < $total){
            toast()->error('Pembayaran kurang');
            return redirect()->back()->withInput([
                'produk' => $produk,
                'bayar' => $bayar,
                'total' => $total,
                'kembalian' => $kembalian,
            ]);
        }

   // VALIDASI STOK (ingat!!!!)
    foreach ($produk as $item) {
        // produk_id kudu ayaan
        if (!isset($item['produk_id'])) {
            toast()->error('Data produk tidak lengkap');
            return redirect()->back()->withInput();
        }

        $product = Product::find($item['produk_id']);
        if (!$product) {
            toast()->error("Produk dengan ID {$item['produk_id']} tidak ditemukan");
            return redirect()->back()->withInput();
        }

        $qty = intval($item['qty']);
        if ($product->stok < $qty) {
            toast()->error("Stok untuk produk {$product->nama_produk} tidak cukup. Tersisa: {$product->stok}");
            return redirect()->back()->withInput();
        }
    }

        $data = PengeluaranBarang::create([
            'nomor_pengeluaran' => PengeluaranBarang::nomorPengeluaran(),
            'nama_petugas' => Auth::user()->name,
            'total_harga' => $total,
            'bayar' => $bayar,
            'kembalian' => $kembalian,
        ]);

        foreach ($produk as $item){
            ItemPengeluaranBarang::create([
                'nomor_pengeluaran' => $data->nomor_pengeluaran,
                'nama_produk' => $item['nama_produk'],
                'qty' => $item['qty'],
                'harga' => $item['harga_jual'],
                'sub_total' => $item['sub_total'],
            ]);

            Product::where('id', $item['produk_id'])->decrement('stok', $item['qty']);
        }

        toast()->success('Transaksi Tersimpan');
        return redirect()->route('pengeluaran-barang.index');
    }

    public function laporan(){
        $data = PengeluaranBarang::orderBy('created_at', 'desc')->get()->map(function ($item){
            $item->tanggal_transaksi = Carbon::parse($item->created_at)->locale('id')->translatedFormat('l, d F Y');
            return $item;
        });

        return view('pengeluaran-barang.laporan', compact('data'));
    }

    public function detailLaporan(String $nomorPengeluaran){
        $data = PengeluaranBarang::with('items')->where('nomor_pengeluaran', $nomorPengeluaran)->first();
        $data->total_harga = $data->items->sum('sub_total');
        $data->tanggal_transaksi = Carbon::parse($data->created_at)->locale('id')->translatedFormat('l,d F Y');
        return view('pengeluaran-barang.detail', compact('data'));
    }
}
