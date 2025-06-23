<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Menampilkan daftar users.
     */
    public function index()
    {
        $users = User::with('role')->get();
        return view('users.index', compact('users')); // Mengembalikan view dengan data users
    }

    /**
     * Menampilkan form untuk membuat user baru.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles')); // Mengembalikan view dengan data roles
    }

    /**
     * Menyimpan user baru ke database
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'img' => 'nullable|string',
        ]);

        // Simpan user baru
        $user = User::create([
            'role_id' => $request->role_id,
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash password
            'img' => $request->img,
        ]);

        return view('users.index', ['users' => User::with('role')->get()])
            ->with('success', 'User berhasil dibuat.'); // Mengembalikan view dengan pesan sukses
    }

    /**
     * Menampilkan detail user.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user')); // Mengembalikan view dengan data user
    }

    /**
     * Menampilkan form untuk mengedit user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles')); // Mengembalikan view dengan data user dan roles
    }

    /**
     * Mengupdate user di database.
     */
    public function update(Request $request, User $user)
    {
        // Validasi input
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'img' => 'nullable|string',
        ]);

        // Update user
        $user->update([
            'role_id' => $request->role_id,
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password, // Hash password jika diisi
            'img' => $request->img,
        ]);

        return view('users.index', ['users' => User::with('role')->get()])
            ->with('success', 'User berhasil diperbarui.'); // Mengembalikan view dengan pesan sukses
    }

    /**
     * Menghapus user dari database.
     */
    public function destroy(User $user)
    {
        // Hapus user
        $user->delete();

        return view('users.index', ['users' => User::with('role')->get()])
            ->with('success', 'User berhasil dihapus.'); // Mengembalikan view dengan pesan sukses
    }
}