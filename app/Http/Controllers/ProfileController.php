<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Menampilkan form edit profile
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // Menyimpan perubahan profile
    public function update(Request $request)
    {
        //dd($request->all());
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'nullable|min:8|confirmed',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:800', // Max 800KB
        ]);

        // Ambil data user yang sedang login
        $user = Auth::user();

        // Update data user
        $user->nama = $request->nama;
        $user->email = $request->email;

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        // Handle upload gambar
        if ($request->hasFile('img')) {
            // Hapus gambar lama jika ada
            if ($user->img && Storage::exists('public/avatars/' . $user->img)) {
                Storage::delete('public/avatars/' . $user->img);
            }

            // Simpan gambar baru
            $imageName = time() . '.' . $request->img->extension();
            $request->img->storeAs('public/avatars', $imageName);
            $user->img = $imageName;
        }

        // Simpan perubahan
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }
}