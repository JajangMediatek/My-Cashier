<?php

namespace App\Http\Controllers;

use App\Models\ItemPengeluaranBarang;
use App\Models\PengeluaranBarang;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        $totalOrder = PengeluaranBarang::whereMonth('created_at', $bulanIni)->whereYear('created_at', $tahunIni)->count();
        $totalPendapatan = PengeluaranBarang::whereMonth('created_at', $bulanIni)->whereYear('created_at', $tahunIni)->sum('total_harga');
        $totalPendapatan = "Rp. " . number_format($totalPendapatan);

        $latestOrder = PengeluaranBarang::latest()->take(5)->get()->map(function($item){
            $item->tanggal_transaksi = Carbon::parse($item->created_at)->locale('id')->translatedFormat('d-m-Y');
            return $item;
        });

        $produkTerlaris = ItemPengeluaranBarang::select('nama_produk')->selectRaw('SUM(qty) as total_terjual')->whereMonth('created_at', $bulanIni)->whereYear('created_at', $tahunIni)->groupBy('nama_produk')->orderByDesc('total_terjual')->limit(5)->get();

        $totalUsers = User::count();
        $totalProduk = Product::count();
        return view('dashboard.index', compact('totalUsers', 'totalProduk', 'totalOrder', 'totalPendapatan', 'latestOrder', 'produkTerlaris'));
    }
}
