<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Menampilkan daftar customers.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $customers = Customer::when($search, function ($query, $search) {
            return $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
        })->paginate(10);

        return view('customers.index', compact('customers'));
    }

    /**
     * Menampilkan form untuk membuat customer baru.
     */
    public function create()
    {
        return view('customers.create'); // Mengembalikan view form create
    }

    /**
     * Menyimpan customer baru ke database
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:customer,email',
            'password' => 'required|string|min:8',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:20',
            'img' => 'nullable|string',
        ]);

        // Simpan customer baru
        Customer::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash password
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
            'img' => $request->img,
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil dibuat.'); // Redirect ke halaman index dengan pesan sukses
    }

    /**
     * Menampilkan detail customer.
     */
    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer')); // Mengembalikan view detail customer
    }

    /**
     * Menampilkan form untuk mengedit customer.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer')); // Mengembalikan view form edit
    }

    /**
     * Mengupdate customer di database.
     */
    public function update(Request $request, Customer $customer)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:customer,email,' . $customer->id,
            'password' => 'nullable|string|min:8',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:20',
            'img' => 'nullable|string',
        ]);

        // Update customer
        $customer->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $customer->password, // Hash password jika diisi
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
            'img' => $request->img,
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil diperbarui.'); // Redirect ke halaman index dengan pesan sukses
    }

    /**
     * Menghapus customer dari database.
     */
    public function destroy(Customer $customer)
    {
        // Hapus customer
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil dihapus.'); // Redirect ke halaman index dengan pesan sukses
    }
}