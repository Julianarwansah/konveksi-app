<?php

use App\Http\Controllers\AntrianController;
use App\Http\Controllers\KapasitasController;
use App\Http\Controllers\PembayaranController;
use App\Models\TemplateWarna;


use App\Http\Controllers\BahanController;
use App\Http\Controllers\BiayaController;
use App\Http\Controllers\CustomController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\PengirimanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PesananProdukController;
use App\Http\Controllers\PesananCustomController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

// Public Controllers
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\CustomProdukController;
use App\Http\Controllers\Public\ProfileCustomerController;
use App\Http\Controllers\Public\CartController;
use App\Http\Controllers\Public\PesananCustomerController;
use App\Http\Controllers\Public\CustomerRegisterController;
use App\Http\Controllers\Public\PesananCustomerProdukController;
use App\Http\Controllers\Public\PesananCustomerCustomController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route umum tanpa middleware
Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/custom-produk', [CustomProdukController::class, 'index'])->name('custom.produk');
Route::get('/produkshop', [HomeController::class, 'produkshop'])->name('produkshop.index');
Route::view('/contact', 'public.contact');
Route::get('/faq-produk-jadi', function () {
    return view('public.faq_produk_jadi');
})->name('faq.produkjadi'); // <- ini penting

Route::get('/faq-produk-custom', function () {
    return view('public.faq_produk_custom');
})->name('faq.produkcustom');

// Route untuk customer profile dengan middleware auth:customer
Route::prefix('customer')->middleware(['auth:customer'])->group(function () {
    Route::get('/produkshopdetail/{id}', [HomeController::class, 'produkshopshow'])->name('produkshopdetail.detail');
    Route::get('/custom-produk/{id}', [CustomProdukController::class, 'show'])->name('custom.detail');

    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/pesanancustomer', [PesananCustomerController::class, 'index'])->name('pesanan.customer');
    Route::get('/pesanancustomerdetail/{id}', [PesananCustomerController::class, 'showcustomerpesanan'])->name('pesanan.pesanancustomerdetail');
    Route::post('/pesanan/store', [PesananCustomerController::class, 'store'])->name('pesanan.store');
    Route::post('/pesanan/{id}/selesaikan', [PesananCustomerController::class, 'selesaikanPesanan'])
     ->name('pesanan.selesaikan');
    Route::get('/pembayaran/{id}/pdf', [PesananCustomerController::class, 'cetakPembayaranPdf'])->name('pembayaran.pdf');


    Route::get('/pesanan/selesai', function () {
        return view('public.pesanancustomer.pesanancus');
    })->name('pesanan.selesai');

    Route::get('/profile', [ProfileCustomerController::class, 'show'])->name('customer.profile');
    Route::get('/profile/edit', [ProfileCustomerController::class, 'edit'])->name('customer.profile.edit');
    Route::put('/profile/update', [ProfileCustomerController::class, 'update'])->name('customer.profile.update');

    Route::get('/password/edit', [ProfileCustomerController::class, 'editPassword'])->name('customer.password.edit');
    Route::put('/password/update', [ProfileCustomerController::class, 'updatePassword'])->name('customer.password.update');
    Route::get('pesanancustomerproduk/create', [PesananCustomerProdukController::class, 'create'])->name('pesanancustomerproduk.create');

    // Form pelunasan
    Route::get('pesanan/{id}/pelunasan', [PesananCustomerController::class, 'pelunasanForm'])
        ->name('public.pesanan.pelunasanForm');
    // Proses simpan pelunasan
    Route::post('pesanan/{id}/pelunasan', [PesananCustomerController::class, 'storePelunasan'])
        ->name('public.pesanan.storePelunasan');

    Route::post('/pesanan/{id}/bayar-ulang', [PesananCustomerController::class, 'bayarUlang'])
        ->name('public.pesanan.bayarUlang');

    Route::prefix('pesanan')->group(function () {
        Route::get('/create', [PesananCustomerProdukController::class, 'create'])
             ->name('public.pesanan.create');
             
        Route::post('/store', [PesananCustomerProdukController::class, 'store'])
             ->name('public.pesanan.store');
             
        Route::get('/thankyou', function () {
             return view('public.pesanancustomer.thankyou');
        })->name('public.pesanan.thankyou');

        Route::get('/bayar-ulang/{id}', [PesananCustomerController::class, 'bayarUlangForm'])->name('pesanan.bayar-ulang');
        Route::get('/proses-bayar-ulang/{id}/{persentase}', [PesananCustomerController::class, 'prosesBayarUlang'])->name('pesanan.proses-bayar-ulang');
        Route::post('/simpan-bayar-ulang/{id}', [PesananCustomerController::class, 'simpanBayarUlang'])->name('pesanan.simpan-bayar-ulang');
    });

    Route::prefix('pesanan')->group(function () {
        // 1) Form awal pilih template
        Route::get('/custom/create', [PesananCustomerCustomController::class, 'create'])
            ->name('public.pesanan.custom.create');

        // 2) Tampilkan form checkout (via query string)
        Route::get('/custom/checkout', [PesananCustomerCustomController::class, 'showCheckoutForm'])
            ->name('public.pesanan.custom.checkout');

        // 3) Proses validasi & tampil ulang checkout (jika butuh)
        Route::post('/custom/checkout', [PesananCustomerCustomController::class, 'checkout'])
            ->name('public.pesanan.custom.checkout.process');

        // 4) Simpan pesanan & redirect ke thank you
        Route::post('/custom/store', [PesananCustomerCustomController::class, 'store'])
            ->name('public.pesanan.custom.store');

        // 5) Halaman “Thank You”
        Route::get('/custom/thankyou', function () {
            return view('public.pesanancustomer.thankyou');
        })->name('public.pesanan.custom.thankyou');

        // 6) Ambil daftar warna via AJAX
        Route::get('/custom/get-warna/{template}', function ($templateId) {
            return TemplateWarna::where('template_id', $templateId)
                ->pluck('nama', 'id');
        })->name('public.pesanan.custom.get-warna');
    });


});


// Route untuk menampilkan form login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Route untuk proses login
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [CustomerRegisterController::class, 'showRegistrationForm'])->name('customer.register.form');
Route::post('/register', [CustomerRegisterController::class, 'register'])->name('customer.register');

// Route untuk proses logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Pindahkan ini ke atas
Route::get('/keuangan/export', [KeuanganController::class, 'export'])->name('keuangan.export');

// Baru resource-nya
Route::resource('keuangan', KeuanganController::class);

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Resource Routes
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('produk', ProdukController::class);
    Route::resource('custom',CustomController::class);
    Route::resource('bahan', BahanController::class);
    Route::resource('pesanan', PesananController::class);
    Route::resource('pemasukan', PemasukanController::class);
    Route::resource('pengeluaran', PengeluaranController::class);
    Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
    Route::resource('pesanan-produk', PesananProdukController::class)->only(['create', 'store', 'edit', 'update']);
    Route::resource('pesanan-pakaian-custom', PesananCustomController::class)->only(['create', 'store', 'edit', 'update']);
    Route::resource('keuangan', KeuanganController::class);
    Route::resource('pembayaran', PembayaranController::class);
    Route::resource('template', TemplateController::class);
    Route::resource('antrian', AntrianController::class);
    Route::resource('pengiriman', PengirimanController::class);
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    
    // Route untuk mendapatkan bahan (hanya yang menggunakan method getBahan)
    Route::get('/pesanan-custom/get-bahan/{modelId}', [PesananCustomController::class, 'getBahan'])
        ->name('pesanan-custom.get-bahan');

    // Route untuk mendapatkan warna
    Route::get('/pesanan-custom/get-warna/{modelId}', [PesananCustomController::class, 'getWarnaByModel'])
        ->name('pesanan-custom.get-warna');

});