<!DOCTYPE html>
<html>
<head>
    <title>Laporan Material Usage Request - #{{ $materialUsageRequest->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Material Usage Request</h2>
        <h3>Request #{{ $materialUsageRequest->id }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>Dibuat Oleh</th>
                <th>Tanggal Permintaan</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $materialUsageRequest->createdBy->nama }}</td>
                <td>{{ \Carbon\Carbon::parse($materialUsageRequest->tanggal_permintaan)->format('d-m-Y') }}</td>
                <td>{{ $materialUsageRequest->status }}</td>
                <td>{{ $materialUsageRequest->catatan ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <h3>Detail Material yang Digunakan</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Material</th>
                <th>Jumlah Digunakan</th>
                <th>Satuan</th>
                <th>Stok Saat Ini</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materialUsageRequest->details as $detail)
                <tr>
                    <td>{{ $detail->bahanBaku->nama }}</td>
                    <td>{{ $detail->jumlah }}</td>
                    <td>{{ $detail->bahanBaku->satuan }}</td>
                    <td>{{ $detail->bahanBaku->stok }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>
</body>
</html>
