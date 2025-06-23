<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Menampilkan daftar roles
     */
    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    /**
     * Menampilkan form untuk membuat role baru.
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Menyimpan role baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:50|unique:roles,nama',
        ]);

        // Simpan role baru
        Role::create($request->only('nama'));

        // Kembalikan ke view roles.index dengan pesan sukses
        $roles = Role::all();
        return view('roles.index', compact('roles'))->with('success', 'Role berhasil dibuat.');
    }

    /**
     * Menampilkan detail role.
     */
    public function show(Role $role)
    {
        return view('roles.show', compact('role'));
    }

    /**
     * Menampilkan form untuk mengedit role.
     */
    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    /**
     * Mengupdate role di database.
     */
    public function update(Request $request, Role $role)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:50|unique:roles,nama,' . $role->id,
        ]);

        // Update role
        $role->update($request->only('nama'));

        // Kembalikan ke view roles.index dengan pesan sukses
        $roles = Role::all();
        return view('roles.index', compact('roles'))->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Menghapus role dari database.
     */
    public function destroy(Role $role)
    {
        // Hapus role
        $role->delete();

        // Kembalikan ke view roles.index dengan pesan sukses
        $roles = Role::all();
        return view('roles.index', compact('roles'))->with('success', 'Role berhasil dihapus.');
    }
}