@extends('layoutspublic.app')

@section('content')

<style>
    /* Styling untuk label warna */
    .color-label {
        display: inline-block;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 2px solid transparent;
        margin-right: 10px;
        cursor: pointer;
        transition: transform 0.3s ease, border 0.3s ease;
        position: relative;
    }

    /* Menyembunyikan input radio asli */
    .color-label input[type="radio"] {
        display: none;
    }

    /* Gaya saat label warna dipilih */
    .color-label.selected {
        border: 3px solid #000; /* Border hitam saat dipilih */
        transform: scale(1.2); /* Efek perbesaran saat dipilih */
    }

    /* Styling untuk label ukuran */
    .size-label {
        display: inline-block;
        padding: 8px 15px;
        border: 1px solid #ddd;
        border-radius: 20px;
        margin-right: 10px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    /* Menyembunyikan input radio asli untuk ukuran */
    .size-label input[type="radio"] {
        display: none;
    }

    /* Gaya saat label ukuran dipilih */
    .size-label.selected {
        background-color: #000;
        color: #fff;
        border-color: #000;
    }
</style>

<section class="shop-details">
    <div class="product__details__pic">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="product__details__breadcrumb">
                        <a href="./index.html">Home</a>
                        <a href="./shop.html">Shop</a>
                        <span>Product Details</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Bagian thumbnail gambar produk -->
                <div class="col-lg-3 col-md-3">
                    <ul class="nav nav-tabs" role="tablist">
                        @foreach($products->gambarDetails as $key => $gambar)
                        <li class="nav-item">
                            <a class="nav-link {{ $key == 0 ? 'active' : '' }}" data-toggle="tab" href="#tabs-{{ $key+1 }}" role="tab">
                                <div class="product__thumb__pic set-bg" data-setbg="{{ asset('storage/'.$gambar->gambar) }}">
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <!-- Bagian gambar produk utama -->
                <div class="col-lg-6 col-md-9">
                    <div class="tab-content">
                        @foreach($products->gambarDetails as $key => $gambar)
                        <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="tabs-{{ $key+1 }}" role="tabpanel">
                            <div class="product__details__pic__item">
                                <img src="{{ asset('storage/'.$gambar->gambar) }}" alt="{{ $products->nama }}">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="product__details__content">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-8">
                    <div class="product__details__text">
                        <h4>{{ $products->nama }}</h4>
                        <div class="rating">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star-o"></i>
                            <span> - 5 Reviews</span>
                        </div>
                        <h3>Rp {{ number_format($products->harga, 0, ',', '.') }}</h3>

                        {{-- PHP untuk mengelompokkan varian berdasarkan ukuran --}}
                        @php
                            // Kelompokkan variantMap berdasarkan ukuran, simpan semua id kombinasi dalam array
                            $groupedSizes = collect($variantMap)->groupBy('ukuran')->map(function ($items) {
                                return [
                                    'ukuran' => $items[0]['ukuran'],
                                    'ukuran_ids' => $items->pluck('ukuran_id')->toArray(),
                                ];
                            });
                        @endphp

                        <div class="product__details__option">
                            {{-- Pilihan ukuran produk --}}
                            @if(count($groupedSizes) > 0)
                            <div class="product__details__option__size">
                                <span>Ukuran:</span>
                                @foreach($groupedSizes as $group)
                                    <label class="size-label" data-ukuran-ids="{{ implode(',', $group['ukuran_ids']) }}">
                                        {{ $group['ukuran'] }}
                                        <input type="radio" name="size" value="">
                                    </label>
                                @endforeach
                            </div>
                            @endif

                            {{-- Pilihan warna produk --}}
                            @if($products->produkWarna->count() > 0)
                            <div class="product__details__option__color">
                                <span>Warna:</span>
                                @foreach($products->produkWarna as $warna)
                                    <label class="color-label" data-warna-id="{{ $warna->id }}" style="background-color: {{ $warna->warna }};" title="{{ $warna->warna }}">
                                        <input type="radio" name="color" value="{{ $warna->id }}">
                                    </label>
                                @endforeach
                            </div>
                            @endif

                            {{-- Informasi stok produk --}}
                            <div id="stok-tersedia" style="margin-top: 10px; font-weight: bold;">
                                Stok tersedia: <span id="stok-value">{{ $products->total_stok }}</span> pcs
                            </div>
                            <div id="detail-stok" style="margin-top: 5px; font-size: 0.9em; color: #666; display: none;">
                                Stok untuk <span id="selected-size"></span> dan <span id="selected-color"></span>:
                                <span id="exact-stock"></span>
                            </div>
                        </div>

                        {{-- Bagian kuantitas dan tombol 'Add to cart' --}}
                        <div class="product__details__cart__option">
                            <div class="quantity">
                                <div class="pro-qty">
                                    <input type="text" id="quantity-input" value="1" min="1" max="{{ $products->total_stok }}">
                                </div>
                            </div>
                            <button id="add-to-cart-btn" class="primary-btn">Tambah ke Keranjang</button>
                        </div>

                        {{-- Form tersembunyi untuk pengiriman data ke keranjang --}}
                        <form id="add-to-cart-form" method="POST" action="{{ route('cart.add') }}" style="display: none;">
                            @csrf
                            <input type="hidden" name="produk_id" value="{{ $products->id }}">
                            <input type="hidden" name="warna_id" id="form-warna-id">
                            <input type="hidden" name="ukuran_id" id="form-ukuran-id">
                            <input type="hidden" name="quantity" id="form-quantity">
                            <input type="hidden" name="harga_satuan" value="{{ $products->harga }}">
                        </form>

                        {{-- Informasi tambahan produk --}}
                        <div class="product__details__last__option">
                            <ul>
                                <li><span>SKU:</span> {{ $products->id }}</li>
                                <li><span>Kategori:</span> {{ $products->kategori }}</li>
                                <li><span>Stok Tersedia:</span> {{ $products->total_stok }} pcs</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian tab deskripsi dan bahan produk --}}
            <div class="row">
                <div class="col-lg-12">
                    <div class="product__details__tab">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tabs-5" role="tab">Deskripsi</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabs-7" role="tab">Bahan Produk</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="tabs-5" role="tabpanel">
                                <div class="product__details__tab__content">
                                    <div class="product__details__tab__content__item">
                                        <div class="note">{!! $products->deskripsi !!}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="tabs-7" role="tabpanel">
                                <div class="product__details__tab__content">
                                    <div class="product__details__tab__content__item">
                                        <h5>Bahan Pembuatan</h5>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nama Bahan</th>
                                                    <th>Jumlah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($products->produkBahan as $bahan)
                                                <tr>
                                                    <td>{{ $bahan->bahan->nama }}</td>
                                                    <td>{{ $bahan->jumlah }} {{ $bahan->bahan->satuan }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Data varian produk yang tersedia dari backend
    const variantMap = @json($variantMap);
    console.log('Daftar Variant Map:', variantMap);

    document.addEventListener('DOMContentLoaded', function() {
        // Mendapatkan semua elemen DOM yang dibutuhkan
        const sizeLabels = document.querySelectorAll('.size-label');
        const colorLabels = document.querySelectorAll('.color-label');
        const stokValueSpan = document.getElementById('stok-value');
        const detailStokDiv = document.getElementById('detail-stok');
        const selectedSizeSpan = document.getElementById('selected-size');
        const selectedColorSpan = document.getElementById('selected-color');
        const exactStockSpan = document.getElementById('exact-stock');
        const quantityInput = document.getElementById('quantity-input');
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        const addToCartForm = document.getElementById('add-to-cart-form');
        const formUkuranIdInput = document.getElementById('form-ukuran-id');
        const formWarnaIdInput = document.getElementById('form-warna-id');

        quantityInput.addEventListener('change', function() {
            const selectedQuantity = parseInt(this.value);
            const availableStock = parseInt(stokValueSpan.textContent);
            
            // Validasi jika quantity melebihi stok
            if (selectedQuantity > availableStock) {
                alert(`Jumlah yang diminta melebihi stok tersedia. Stok tersisa: ${availableStock} pcs`);
                this.value = 1; // Reset ke 1
            }
            
            // Pastikan quantity minimal 1
            if (selectedQuantity < 1) {
                this.value = 1;
            }
        });
        

        /**
         * Memperbarui tampilan stok dan nilai maksimum input kuantitas
         * berdasarkan ukuran dan warna yang dipilih.
         */
        function updateStockDisplay() {
            const selectedSizeInput = document.querySelector('input[name="size"]:checked');
            const selectedColorInput = document.querySelector('input[name="color"]:checked');

            // Jika ukuran atau warna belum dipilih
            if (!selectedSizeInput || !selectedColorInput) {
                stokValueSpan.textContent = {{ $products->total_stok }}; // Kembali ke total stok produk
                quantityInput.max = {{ $products->total_stok }};
                quantityInput.value = 1; // Reset kuantitas ke 1
                detailStokDiv.style.display = 'none'; // Sembunyikan detail stok
                formUkuranIdInput.value = '';
                formWarnaIdInput.value = '';
                addToCartBtn.disabled = true; // Nonaktifkan tombol
                return;
            }

            // Dapatkan nama ukuran dan warna yang dipilih
            const sizeLabel = selectedSizeInput.closest('.size-label');
            const sizeName = sizeLabel.textContent.trim();
            const colorLabel = selectedColorInput.closest('.color-label');
            const colorName = colorLabel.getAttribute('title');
            const selectedWarnaId = parseInt(selectedColorInput.value);

            // Dapatkan SEMUA ukuran_id yang terkait dengan label ukuran yang dipilih
            const ukuranIdsAssociatedWithSizeLabel = sizeLabel.dataset.ukuranIds.split(',').map(id => parseInt(id));

            // Cari varian yang cocok dengan warna_id yang dipilih DAN ukuran_id yang termasuk dalam grup ukuran yang dipilih
            const variant = variantMap.find(v =>
                ukuranIdsAssociatedWithSizeLabel.includes(v.ukuran_id) &&
                v.warna_id === selectedWarnaId
            );

            if (variant) {
                // Tampilkan detail stok untuk varian yang ditemukan
                selectedSizeSpan.textContent = sizeName;
                selectedColorSpan.textContent = colorName;
                exactStockSpan.textContent = variant.stok + ' pcs';
                stokValueSpan.textContent = variant.stok;
                quantityInput.max = variant.stok; // Set batas maksimal kuantitas sesuai stok varian
                quantityInput.value = Math.min(quantityInput.value, variant.stok); // Sesuaikan kuantitas agar tidak melebihi stok
                detailStokDiv.style.display = 'block';

                // Isi nilai hidden input untuk form keranjang
                formUkuranIdInput.value = variant.ukuran_id;
                formWarnaIdInput.value = variant.warna_id;
                addToCartBtn.disabled = variant.stok <= 0; // Aktifkan/nonaktifkan tombol berdasarkan stok
            } else {
                // Jika varian tidak ditemukan (kombinasi tidak valid)
                selectedSizeSpan.textContent = sizeName;
                selectedColorSpan.textContent = colorName;
                exactStockSpan.textContent = '0 pcs';
                stokValueSpan.textContent = '0';
                quantityInput.max = 0;
                quantityInput.value = 1; // Reset kuantitas
                detailStokDiv.style.display = 'block';
                formUkuranIdInput.value = '';
                formWarnaIdInput.value = '';
                addToCartBtn.disabled = true; // Nonaktifkan tombol
            }
        }

        /**
         * Memperbarui opsi ukuran dan warna yang tersedia
         * berdasarkan pilihan saat ini untuk mencegah kombinasi yang tidak valid.
         * Fungsi ini hanya menyembunyikan label, tidak menonaktifkan input.
         */
        function updateAvailableOptions() {
            const selectedSizeInput = document.querySelector('input[name="size"]:checked');
            const selectedColorInput = document.querySelector('input[name="color"]:checked');

            // Reset semua opsi agar terlihat sebelum melakukan filter
            sizeLabels.forEach(label => label.style.display = 'inline-block');
            colorLabels.forEach(label => label.style.display = 'inline-block');

            // Jika ukuran dipilih, filter warna yang tersedia
            if (selectedSizeInput) {
                const ukuranIds = selectedSizeInput.closest('.size-label').dataset.ukuranIds.split(',').map(Number);
                const availableColorIds = variantMap
                    .filter(v => ukuranIds.includes(v.ukuran_id))
                    .map(v => v.warna_id);

                colorLabels.forEach(label => {
                    const colorId = parseInt(label.dataset.warnaId);
                    label.style.display = availableColorIds.includes(colorId) ? 'inline-block' : 'none';
                    // Jika warna yang sedang dipilih menjadi tidak tersedia, hapus centangnya
                    if (label.querySelector('input[name="color"]').checked && !availableColorIds.includes(colorId)) {
                        label.querySelector('input[name="color"]').checked = false;
                        label.classList.remove('selected');
                    }
                });
            }

            // Jika warna dipilih, filter ukuran yang tersedia
            if (selectedColorInput) {
                const colorId = parseInt(selectedColorInput.value);
                const availableSizeUkuranIds = variantMap
                    .filter(v => v.warna_id === colorId)
                    .map(v => v.ukuran_id);

                sizeLabels.forEach(label => {
                    const labelSizeIds = label.dataset.ukuranIds.split(',').map(Number);
                    // Cek apakah ada setidaknya satu ukuran_id dari label yang cocok dengan ukuran yang tersedia
                    const hasMatch = labelSizeIds.some(id => availableSizeUkuranIds.includes(id));
                    label.style.display = hasMatch ? 'inline-block' : 'none';
                    // Jika ukuran yang sedang dipilih menjadi tidak tersedia, hapus centangnya
                    if (label.querySelector('input[name="size"]').checked && !hasMatch) {
                        label.querySelector('input[name="size"]').checked = false;
                        label.classList.remove('selected');
                    }
                });
            }
        }

        /**
         * Menangani klik tombol 'Add to cart'.
         * Melakukan validasi pilihan dan mengirimkan form.
         * @param {Event} e - Objek event
         */
        function handleAddToCart(e) {
            e.preventDefault();

            const selectedColorInput = document.querySelector('input[name="color"]:checked');
            const selectedSizeInput = document.querySelector('input[name="size"]:checked');
            const quantity = parseInt(quantityInput.value);
            const availableStock = parseInt(stokValueSpan.textContent);

            // Validasi pilihan ukuran dan warna
            if (!selectedSizeInput) {
                alert('Silakan pilih ukuran terlebih dahulu.');
                return;
            }
            if (!selectedColorInput) {
                alert('Silakan pilih warna terlebih dahulu.');
                return;
            }

            // Validasi stok
            if (isNaN(availableStock) || availableStock <= 0) {
                alert('Stok tidak tersedia untuk kombinasi ini.');
                return;
            }

            // Validasi kuantitas
            if (quantity < 1 || quantity > availableStock) {
                alert(`Jumlah yang diminta tidak valid atau stok tidak mencukupi. Maksimal ${availableStock} pcs.`);
                return;
            }

            // Dapatkan ukuran_id dan warna_id yang benar berdasarkan kombinasi yang dipilih
            const sizeLabel = selectedSizeInput.closest('.size-label');
            const ukuranIds = sizeLabel.dataset.ukuranIds.split(',').map(Number);
            const selectedWarnaId = parseInt(selectedColorInput.value);

            // Cari varian yang sesuai di variantMap
            const variant = variantMap.find(v =>
                ukuranIds.includes(v.ukuran_id) &&
                v.warna_id === selectedWarnaId
            );

            if (variant) {
                // Isi hidden input form dan submit
                formWarnaIdInput.value = variant.warna_id;
                formUkuranIdInput.value = variant.ukuran_id;
                document.getElementById('form-quantity').value = quantity; // Pastikan ini juga diisi

                addToCartForm.submit();
            } else {
                alert('Kombinasi yang dipilih tidak valid.');
            }
        }

        // Event listener untuk label ukuran (saat salah satu radio button ukuran diubah)
        sizeLabels.forEach(label => {
            const input = label.querySelector('input[name="size"]');
            input.addEventListener('change', function() {
                // Hapus class 'selected' dari semua label ukuran
                sizeLabels.forEach(lbl => lbl.classList.remove('selected'));
                // Tambahkan class 'selected' ke label yang sedang dipilih
                label.classList.add('selected');
                updateAvailableOptions(); // Perbarui opsi yang tersedia
                updateStockDisplay();      // Perbarui tampilan stok
            });
        });

        // Event listener untuk label warna (saat salah satu radio button warna diubah)
        colorLabels.forEach(label => {
            const input = label.querySelector('input[name="color"]');
            input.addEventListener('change', function() {
                // Hapus class 'selected' dari semua label warna
                colorLabels.forEach(lbl => lbl.classList.remove('selected'));
                // Tambahkan class 'selected' ke label yang sedang dipilih
                label.classList.add('selected');
                updateAvailableOptions(); // Perbarui opsi yang tersedia
                updateStockDisplay();      // Perbarui tampilan stok
            });
        });

        // Event listener untuk tombol 'Add to cart'
        addToCartBtn.addEventListener('click', handleAddToCart);

        // Inisialisasi tampilan saat halaman dimuat
        // Menandai label ukuran dan warna yang mungkin sudah terpilih saat load
        const initialSelectedSizeInput = document.querySelector('input[name="size"]:checked');
        if (initialSelectedSizeInput) {
            initialSelectedSizeInput.closest('.size-label').classList.add('selected');
        }
        const initialSelectedColorInput = document.querySelector('input[name="color"]:checked');
        if (initialSelectedColorInput) {
            initialSelectedColorInput.closest('.color-label').classList.add('selected');
        }

        // Jalankan fungsi update saat halaman dimuat untuk mengatur status awal
        updateAvailableOptions(); // Pastikan opsi yang valid terlihat
        updateStockDisplay();     // Tampilkan stok awal yang benar
    });
</script>
@endsection