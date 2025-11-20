<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(){
        $products = Product::all();
        $stokMenipis = Product::whereColumn('stok', '<=', 'stok_minimal')->get();
        confirmDelete('Hapus Data', 'Apakah anda yakin ingin menghapus data ini?');
        return view('product.index', compact('products', 'stokMenipis'));
    }


    public function store(Request $request){
        $id = $request->id;
        $request->validate([
            'nama_product'    => 'required|unique:products,nama_produk,'.$id,
            'harga_jual'      => 'required|numeric|min:0',
            'harga_beli_pokok' => 'required|numeric|min:0',
            'kategori_id'     => 'required|exists:kategoris,id',
            'stok'            => 'required|numeric|min:0',
            'stok_minimal'    => 'required|numeric|min:0',
        ],[
            'nama_product.required' => 'Nama produk harus diisi',
            'nama_product.unique' => 'Nama produk sudah ada',
            'harga_jual.required' => 'Harga jual harus diisi',
            'harga_jual.numeric' => 'Harga jual harus berupa angka',
            'harga_jual.min' => 'Harga jual minimal 0',
            'harga_beli_pokok.required' => 'Harga beli pokok harus diisi',
            'harga_beli_pokok.numeric' => 'Harga beli pokok harus berupa angka',
            'harga_beli_pokok.min' => 'Harga beli pokok minimal 0',
            'kategori_id.required'=> 'Kategori harus diisi',
            'kategori_id.exists' => 'Kategori tidak valid',
            'stok.required' => 'Stok harus diisi',
            'stok.numeric' => 'Stok harus berupa angka',
            'stok.min' => 'Stok Minimal 0',
            'stok_minimal.required' => 'Stok Minimal harus diisi',
            'stok_minimal.numeric' => 'Stok Minimal harus berupa angka',
            'stok_minimal.min' => 'Stok minimal terlalu kecil'
        ]);

        $newRequest = [
                'id' => $id,
                'nama_produk' => $request->nama_product,
                'harga_jual' => $request->harga_jual,
                'harga_beli_pokok' => $request->harga_beli_pokok,
                'kategori_id' => $request->kategori_id,
                'stok' => $request->stok,
                'stok_minimal' => $request->stok_minimal,
                'is_active' => $request->is_active ? true : false,
        ];
        if (!$id) {
            $newRequest['sku'] = Product::nomorSKU();
        }
        Product::updateOrCreate(
            ["id" => $id],
            $newRequest
            );
            toast()->success('Data berhasil disimpan');
            return redirect()->route('master-data.product.index');
    }

    public function destroy(String $id){
        $product = Product::find($id);
        $product->delete();
        toast()->success('Data berhasil dihapus');
        return redirect()->route('master-data.product.index');
    }

    public function getData(){
        $search = request()->query('search');

        $query = Product::query()->where('is_active', 1);
        $product = $query->where('nama_produk', 'like', '%'. $search . '%')->get([
            'id',
            'nama_produk',
            'stok',
            'harga_beli_pokok as harga_beli'
        ]);
        return response()->json($product);
    }

    public function cekStok(){
        $id = request()->query('id');
        $stok = Product::find($id)->stok;
        return response()->json($stok);
    }

    public function cekHarga(){
        $id = request()->query('id');
        $harga = Product::find($id)->harga_jual;
        return response()->json($harga);
    }
}
