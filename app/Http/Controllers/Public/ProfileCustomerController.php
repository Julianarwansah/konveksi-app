<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileCustomerController extends Controller
{
    /**
     * Menampilkan profil customer
     */
    public function show()
    {
        // Ambil customer yang sedang login
        $customer = Auth::guard('customer')->user();
        
        return view('public.profile.showcustomer', compact('customer'));
    }

    /**
     * Menampilkan form edit profil
     */
    public function edit()
    {
        $customer = Auth::guard('customer')->user();
        return view('public.profile.editcustomer', compact('customer'));
    }

    /**
     * Mengupdate profil customer
     */
    public function update(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $validatedData = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:customer,email,'.$customer->id,
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle upload gambar
        if ($request->hasFile('img')) {
            // Hapus gambar lama jika ada
            if ($customer->img && file_exists(storage_path('app/public/' . $customer->img))) {
                unlink(storage_path('app/public/' . $customer->img));
            }
            
            $path = $request->file('img')->store('customer_images', 'public');
            $validatedData['img'] = $path;
        }

        $customer->update($validatedData);

        return redirect()->route('customer.profile')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Menampilkan form ganti password
     */
    public function editPassword()
    {
        return view('public.profile.edit-passwordcustomer');
    }

    /**
     * Mengupdate password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $customer = Auth::guard('customer')->user();

        // Verifikasi password saat ini
        if (!\Hash::check($request->current_password, $customer->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah']);
        }

        // Update password
        $customer->update([
            'password' => \Hash::make($request->new_password)
        ]);

        return redirect()->route('customer.profile')->with('success', 'Password berhasil diubah!');
    }
}