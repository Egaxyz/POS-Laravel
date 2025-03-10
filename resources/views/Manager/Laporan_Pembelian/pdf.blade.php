<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembelian</title>
    <style>
        /* Center the h2 element */
        h2.center {
            text-align: center;
            margin-bottom: 20px;
            /* Add space below the title */
        }

        /* Styling for the table */
        table {
            width: 100%;
            border-collapse: collapse;
            /* Ensures borders are combined */
            margin: 0 auto;
            /* Center the table */
        }

        th,
        td {
            border: 1px solid black;
            /* Add border to table cells */
            padding: 10px;
            /* Add padding inside cells */
            text-align: left;
            /* Align text to the left */
        }

        th {
            background-color: #f2f2f2;
            /* Light background for header */
        }
    </style>
</head>

<body>
    <h2 class="center">Laporan Pembelian</h2>
    <table>
        <thead>
            <tr>
                <th>Total Harga</th>
                <th>Detail Pembelian</th>
                <th>Tanggal Pembelian</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pembelian as $data)
                <tr>
                    <td>Rp. {{ number_format($data->total_harga, 0, ',', '.') }}</td>

                    <td>
                        <span
                            class="badge {{ $data->status_pembelian == 'Selesai' ? 'badge-success' : 'badge-warning' }}">
                            {{ $data->status_pembelian == 'Selesai' ? 'Selesai' : 'Proses' }}
                        </span>
                    </td>

                    <td>
                        {{ $data->tanggal_pembelian }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
