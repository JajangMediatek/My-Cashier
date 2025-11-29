<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(){
        $users = User::all();
        confirmDelete('Hapus User', 'Apakah anda yakin ingin menghapus user ini?');
        return view('users.index', compact('users'));
    }

    public function store(Request $request){
        $id = $request->id;
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
        ],[
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
        ]);

        $newRequest = $request->all();

        if (!$id) {
            $newRequest['password'] = Hash::make('12345678');
        }

        User::updateOrCreate(['id' => $id], $newRequest);
        toast()->success('User berhasil disimpan');
        return redirect()->route('users.index');
    }

    public function gantiPassword(Request $request){
        try {
            $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:8|confirmed'
            // 'password'     => [Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
        ],[
            'old_password.required' => 'Password lama harus diisi',
            'password.required' => 'Password baru harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Password baru tidak sama dengan konfirmasi password',
        ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
        $error = $e->validator->errors()->first();

        toast()->error($error);
        return back(); // atau redirect()->route('dashboard');
        }
        
        $users = User::find(Auth::id());

        // cek old password
        if (!Hash::check($request->old_password , $users->password)){
            toast()->error('Password lama tidak sesuai');
            return redirect()->back();
        }

        // update password
        $users -> update([
            'password' => Hash::make($request->password)
        ]);

        toast()->success('Password berhasil diubah');
        return redirect()->back();
    }

    public function destroy(String $id){
        $user = User::find($id);

        if (Auth::id() == $id) {
            toast()->error('Tidak dapat menghapus akun yang sedang login');
            return redirect()->route('users.index');
        }

        $user->delete();
        toast()->success('User berhasil dihapus');
        return redirect()->route('users.index');
    }

    public function resetPassword(Request $request){
        $request->validate([
            'id' => 'required'
        ]);

        $user = User::find($request->id);
        $user->update([
            'password' => Hash::make('12345678')
        ]);
        toast()->success('Password berhasil direset');
        return redirect()->route('users.index');
    }
}
