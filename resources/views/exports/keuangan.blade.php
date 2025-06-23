<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Total Pemasukan</th>
            <th>Total Pengeluaran</th>
            <th>Saldo</th>
        </tr>
    </thead>
    <tbody>
        @foreach($keuangan as $item)
        <tr>
            <td>{{ $item->tanggal->format('d/m/Y') }}</td>
            <td>{{ $item->total_pemasukan }}</td>
            <td>{{ $item->total_pengeluaran }}</td>
            <td>{{ $item->saldo }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
