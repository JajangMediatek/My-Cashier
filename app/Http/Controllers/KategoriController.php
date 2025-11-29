<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpParser\Node\Expr\Cast\String_;

class KategoriController extends Controller
{
    public function index(){
        confirmDelete('Hapus Data', 'Apakah anda yakin ingin menghapus data ini?');
        $kategori = Kategori::all();
        return view('kategori.index', compact('kategori'));
    }

    public function store(Request $request){

        $id = $request->id;
        $request->validate([
            'nama_kategori' => [
            'required',
        Rule::unique('kategoris', 'nama_kategori')
            ->where(fn ($q) => $q->whereRaw('LOWER(nama_kategori) = LOWER(?)', [$request->nama_kategori]))
            ->ignore($id),
        ],
            'deskripsi' => 'required|max:100|min:10'
        ],[
            'nama_kategori.required' => 'Nama kategori harus diisi',
            'nama_kategori.unique' => 'Nama kategori sudah ada',
            'nama_kategori.max' => 'Nama Kategori Maksimal 30 Karakter',
            'deskripsi.required' => 'Deskripsi harus diisi',
            'deskripsi.max' => 'Deskripsi maksimal 100 karakter',
            'deskripsi.min' => 'Deskripsi minimal 10 karakter'
        ]);
        
        Kategori::updateOrCreate(
            ['id' => $id],
            [
                'nama_kategori' => $request->nama_kategori,
                'slug' => Str::slug($request->nama_kategori),
                'deskripsi' => $request->deskripsi,
            ]
        );

        toast()->success('Data berhsail disimpan');
        return redirect()->route('master-data.kategori.index');
    }

    public function destroy(String $id){
        $kategori = Kategori::findOrFail($id);

        if ($kategori->products()->exists()) {
        toast()->error('Kategori tidak dapat dihapus karena masih digunakan produk!');
        return redirect()->route('master-data.kategori.index');
        }

        $kategori->delete();
        toast()->success('Data berhasil dihapus');
        return redirect()->route('master-data.kategori.index');
    }
}
