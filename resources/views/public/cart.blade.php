@extends('layoutspublic.app')

@section('content')

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__text">
                    <h4>Shopping Cart</h4>
                    <div class="breadcrumb__links">
                        <a href="{{ url('/') }}">Home</a>
                        <a href="{{ url('/shop') }}">Shop</a>
                        <span>Shopping Cart</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Shopping Cart Section Begin -->
<section class="shopping-cart spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="shopping__cart__table table-responsive">
                    @if($cartItems->isEmpty())
                        <p>Keranjang Anda masih kosong.</p>
                    @else
                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 35%">Product</th>
                                    <th style="width: 20%">Quantity</th>
                                    <th style="width: 20%">Total</th>
                                    <th style="width: 10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalHarga = 0; @endphp
                                @foreach($cartItems as $item)
                                    @php $totalHarga += $item->subtotal; @endphp
                                    <tr>
                                        <td class="d-flex align-items-center gap-3 text-start">
                                            <img src="{{ $item->produk->gambarDetails->first() ? asset('storage/'.$item->produk->gambarDetails->first()->gambar) : asset('assetspublic/img/product/default.jpg') }}"
                                                alt="" style="width: 80px; height: auto; border-radius: 5px;">
                                            <div>
                                                <strong>{{ $item->produk->nama }}</strong><br>
                                                <small>Harga: Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</small><br>
                                                <small>Ukuran: {{ $item->ukuran->ukuran ?? '-' }}</small><br>
                                                <small>
                                                    Warna: 
                                                    @if(isset($item->warna->warna))
                                                        <span style="display:inline-block; width:12px; height:12px; background-color:{{ $item->warna->warna }}; border:1px solid #ccc; vertical-align:middle; margin-right:4px;"></span>
                                                        {{ $item->warna->warna }}
                                                    @else
                                                        -
                                                    @endif
                                                </small>
                                                @php
                                                    $maxStok = $item->ukuran ? $item->ukuran->stok : $item->produk->total_stok;
                                                @endphp
                                                @if($maxStok < 5)
                                                    <div class="text-danger small">Stok hampir habis!</div>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-flex flex-column align-items-center">
                                                @csrf
                                                @method('PUT')
                                                @php
                                                    $maxStok = $item->ukuran ? $item->ukuran->stok : $item->produk->total_stok;
                                                @endphp
                                                <input type="number" name="jumlah" value="{{ min($item->jumlah, $maxStok) }}" min="1"
                                                    max="{{ $maxStok }}" class="form-control form-control-sm text-center"
                                                    style="width: 60px;">
                                                <small class="text-muted">Stok: {{ $maxStok }}</small>
                                                <button type="submit" class="btn btn-sm btn-outline-primary mt-2">Update</button>
                                            </form>
                                        </td>

                                        <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>

                                        <td>
                                            <form action="{{ route('cart.destroy', $item->id) }}" method="POST"
                                                  onsubmit="return confirm('Yakin ingin menghapus item ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <div class="mt-3">
                    <a href="{{ route('produkshop.index') }}" class="btn btn-outline-secondary">
                        &larr; Continue Shopping
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Cart Summary</h5>
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Subtotal</span>
                                <strong>Rp {{ number_format($totalHarga, 0, ',', '.') }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total</span>
                                <strong>Rp {{ number_format($totalHarga, 0, ',', '.') }}</strong>
                            </li>
                        </ul>
                        <a href="{{ route('pesanancustomerproduk.create') }}" class="btn btn-success w-100">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Shopping Cart Section End -->

@endsection
