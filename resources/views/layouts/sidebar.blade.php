<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ url('dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        <!-- SVG logo -->
      </span>
      <span class="app-brand-text demo menu-text fw-bolder ms-2">Konveksi</span>
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- DASHBOARD -->
    <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
      <a href="{{ url('/dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home"></i>
        <div>Dashboard</div>
      </a>
    </li>

    @if(auth()->user()->role->nama == 'Manager')
    <!-- MANAJEMEN PENGGUNA -->
    <li class="menu-header small text-uppercase"><span class="menu-header-text">Manajemen Pengguna</span></li>
    <li class="menu-item {{ request()->is('roles*') ? 'active' : '' }}">
      <a href="{{ route('roles.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-shield-quarter"></i>
        <div>Role</div>
      </a>
    </li>
    <li class="menu-item {{ request()->is('users*') ? 'active' : '' }}">
      <a href="{{ route('users.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user"></i>
        <div>Pengguna</div>
      </a>
    </li>
    @endif

    <!-- CUSTOMER -->
    @if(auth()->user()->role->nama != 'Produksi')
    <li class="menu-header small text-uppercase"><span class="menu-header-text">Customer</span></li>
    <li class="menu-item {{ request()->is('customers*') ? 'active' : '' }}">
      <a href="{{ route('customers.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user-check"></i>
        <div>Data Customer</div>
      </a>
    </li>
    @endif

    <!-- PRODUK & PESANAN -->
    @if(auth()->user()->role->nama != 'Produksi')
    <li class="menu-header small text-uppercase"><span class="menu-header-text">Produk</span></li>
    
    <!-- Bahan Baku - Hanya untuk Manager -->
    @if(auth()->user()->role->nama == 'Manager')
    <li class="menu-item {{ request()->routeIs('bahan.*') ? 'active' : '' }}">
      <a href="{{ route('bahan.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-basket"></i>
        <div>Bahan Baku</div>
      </a>
    </li>
    @endif

    <!-- Produk Jadi - Untuk Manager dan Admin Ecommerce -->
    @if(auth()->user()->role->nama == 'Manager' || auth()->user()->role->nama == 'Admin Ecommerce')
    <li class="menu-item {{ request()->routeIs('produk.*') ? 'active' : '' }}">
      <a href="{{ route('produk.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-cube"></i>
        <div>Produk Jadi</div>
      </a>
    </li>
    @endif
    
    <!-- Template Custom - Untuk Manager dan Admin Ecommerce -->
    @if(auth()->user()->role->nama == 'Manager' || auth()->user()->role->nama == 'Admin Ecommerce')
    <li class="menu-item {{ request()->routeIs('template.*') ? 'active' : '' }}">
      <a href="{{ route('template.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-palette"></i>
        <div>Template Custom</div>
      </a>
    </li>
    @endif
    
    @endif

    <!-- MENU PRODUKSI -->
    @if(auth()->user()->role->nama == 'Produksi' || auth()->user()->role->nama == 'Manager' || auth()->user()->role->nama == 'Admin Ecommerce')
    <li class="menu-header small text-uppercase"><span class="menu-header-text">Produksi</span></li>

    <!-- Pesanan Masuk - Untuk Manager dan Admin Ecommerce -->
    @if(auth()->user()->role->nama == 'Manager' || auth()->user()->role->nama == 'Admin Ecommerce')
    <li class="menu-item {{ request()->is('pesanan*') ? 'active' : '' }}">
      <a href="{{ route('pesanan.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-cart"></i>
        <div>Pesanan Masuk</div>
      </a>
    </li>
    @endif
    
    <!-- Pesanan Custom - Untuk Produksi, Manager, dan Admin Ecommerce -->
    <li class="menu-item {{ request()->is('custom*') ? 'active' : '' }}">
      <a href="{{ route('custom.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-cut"></i>
        <div>Pesanan Custom Detail</div>
      </a>
    </li>
    
    <!-- Antrian Produksi - Untuk Produksi, Manager, dan Admin Ecommerce -->
    <li class="menu-item {{ request()->is('antrian*') ? 'active' : '' }}">
      <a href="{{ route('antrian.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-time-five"></i>
        <div>Antrian Produksi</div>
      </a>
    </li>
    @endif

    <!-- PENGIRIMAN - Untuk Manager dan Admin Ecommerce -->
    @if(auth()->user()->role->nama != 'Produksi' && (auth()->user()->role->nama == 'Manager' || auth()->user()->role->nama == 'Admin Ecommerce' || auth()->user()->role->nama == 'Kasir'))
  <li class="menu-item {{ request()->is('pengiriman*') ? 'active' : '' }}">
    <a href="{{ route('pengiriman.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bxs-truck"></i>
      <div>Pengiriman</div>
    </a>
  </li>
  @endif

    {{-- KEUNGAN - Hanya untuk Kasir dan Manager --}}
    @if(auth()->check() && (auth()->user()->role->nama == 'Kasir' || auth()->user()->role->nama == 'Manager'))
    <li class="menu-header small text-uppercase"><span class="menu-header-text">Keuangan</span></li>
    <li class="menu-item {{ request()->is('pembayaran*') ? 'active' : '' }}">
          <a href="{{ route('pembayaran.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-wallet"></i>
              <div>Pembayaran Customer</div>
          </a>
    </li>
    <li class="menu-item {{ request()->is('pemasukan*') ? 'active' : '' }}">
          <a href="{{ route('pemasukan.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-down-arrow"></i>
              <div>Pemasukan</div>
          </a>
    </li>
    <li class="menu-item {{ request()->is('pengeluaran*') ? 'active' : '' }}">
          <a href="{{ route('pengeluaran.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-up-arrow"></i>
              <div>Pengeluaran</div>
          </a>
    </li>
    <li class="menu-item {{ request()->is('keuangan*') ? 'active' : '' }}">
          <a href="{{ route('keuangan.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-chart"></i>
              <div>Laporan Keuangan</div>
          </a>
    </li>
    @endif
  </ul>
</aside>