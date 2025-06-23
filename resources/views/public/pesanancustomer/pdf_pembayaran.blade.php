<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Pembayaran</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 80mm;  /* Lebar kertas struk thermal */
            margin: 0 auto;
            padding: 5px;
            color: #000;
            background: white;
            line-height: 1.3;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .alamat {
            font-size: 10px;
            margin-bottom: 8px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .info p {
            margin: 5px 0;
            display: flex;
            justify-content: space-between;
        }

        .info p strong {
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
        }

        .terimakasih {
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }

        .barcode {
            text-align: center;
            margin: 10px 0;
            font-family: 'Libre Barcode 39', cursive;
            font-size: 24px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">JUL KONVEK</div>
        <div class="alamat">
            Perum griya pasir jaya tahap 3 blok E 14<br>
            Telp: (+62) 89661770123
        </div>
        <div class="divider"></div>
    </div>

    <h3 style="text-align: center; margin: 5px 0; font-size: 14px;">BUKTI PEMBAYARAN</h3>
    <div class="divider"></div>

    <div class="info">
        <p><strong>ID Pesanan:</strong> <span>{{ $bayar->pesanan->id }}</span></p>
        <p><strong>Tanggal:</strong> <span>{{ $bayar->tanggal_bayar->format('d-m-Y H:i') }}</span></p>
        <p><strong>Metode:</strong> <span>{{ $bayar->metode }}</span></p>
        <p><strong>Jumlah:</strong> <span>Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}</span></p>
        <p><strong>Status:</strong> <span>{{ $bayar->status }}</span></p>
        <p><strong>Jenis Bayar:</strong> <span>{{ $bayar->is_dp ? 'Uang Muka' : 'Pelunasan' }}</span></p>
        <p><strong>Catatan:</strong> <span>{{ $bayar->catatan ?? '-' }}</span></p>
    </div>

    <div class="divider"></div>

    <div class="barcode">
        *{{ $bayar->pesanan->id }}*
    </div>

    <div class="terimakasih">
        TERIMA KASIH
    </div>

    <div class="footer">
        Dicetak: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}<br>
        www.julkonvek.com
    </div>

</body>
</html>