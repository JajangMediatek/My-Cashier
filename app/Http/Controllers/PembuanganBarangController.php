<?php

namespace App\Http\Controllers;

use App\Models\ItemPembuanganBarang;
use App\Models\PembuanganBarang;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembuanganBarangController extends Controller
{
    public function index(){
        return view('pembuangan-barang.index');
    }

    public function store(Request $request){    
        if (!$request->has('produk') || !is_array($request->produk)) {
            toast()->error('Tidak ada produk yang dipilih');
            return redirect()->back();
        }

        $request->validate([
            'produk' => 'required|array|min:1',
            'keterangan'  => 'required',
        ],[
            'produk.required' => 'Produk harus dipilih',
            'keterangan.required' => 'Keterangan harus diisi',
        ]);

        $produk = collect($request->produk);
        $keterangan = $request->keterangan;


   // VALIDASI STOK (poin penting)
    foreach ($produk as $item) {
        // pastikan ada produk_id
        if (!isset($item['produk_id'])) {
            toast()->error('Data produk tidak lengkap');
            return redirect()->back()->withInput();
        }

        $product = Product::find($item['produk_id']);
        if (!$product) {
            toast()->error("Produk dengan ID {$item['produk_id']} tidak ditemukan");
            return redirect()->back()->withInput();
        }

        if (!isset($product->stok)) {
            toast()->error('Data stok tidak ditemukan.');
            return back()->withInput();
        }

        $qty = intval($item['qty']);
        if ($product->stok < $qty) {
            toast()->error("Masukkan stok produk {$product->nama_produk} dengan valid! Tersisa: {$product->stok}");
            return redirect()->back()->withInput();
        }

        if ($qty <= 0) {
            toast()->error("Qty tidak valid untuk produk {$product->nama_produk}");
            return back()->withInput();
        }
    }

        DB::beginTransaction();

    try {
        // semua create di sini
                $data = PembuanganBarang::create([
            'nomor_pembuangan' => PembuanganBarang::nomorPembuangan(),
            'nama_petugas' => Auth::user()->name,
            'keterangan' => $keterangan,
        ]);

        foreach ($produk as $item){
            ItemPembuanganBarang::create([
                'nomor_pembuangan' => $data->nomor_pembuangan,
                'nama_produk' => $item['nama_produk'],
                'qty' => $item['qty'],
            ]);

            Product::where('id', $item['produk_id'])->decrement('stok', $item['qty']);
        }

        DB::commit();
    } catch (\Throwable $e) {
        DB::rollBack();
        toast()->error('Terjadi kesalahan saat menyimpan');
        return back()->withInput();
    }
        toast()->success('Pembuangan Tersimpan');
        return redirect()->route('pembuangan-barang.index');
    }

    public function laporan(){
         $data = PembuanganBarang::orderBy('created_at', 'desc')->get()->map(function ($item){
            $item->tanggal_transaksi = Carbon::parse($item->created_at)->locale('id')->translatedFormat('l, d F Y');
            return $item;
        });

        return view('pembuangan-barang.laporan', compact('data'));
    }

        public function detailLaporan(String $nomorPembuangan){
        $data = PembuanganBarang::with('items')->where('nomor_pembuangan', $nomorPembuangan)->first();
        $data->tanggal_pembuangan = Carbon::parse($data->created_at)->locale('id')->translatedFormat('l, d F Y');
        $data->total_qty = $data->items->sum('qty');
        return view('pembuangan-barang.detail', compact('data'));
    }
}
