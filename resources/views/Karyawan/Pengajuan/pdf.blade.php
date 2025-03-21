<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengajuan</title>
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
    <h2 class="center">Laporan Pengajuan</h2>
    <table>
        <thead>
            <tr>
                <th>Member</th>
                <th>Nama Menu</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pengajuan as $data)
                <tr>
                    <td>{{ $data->user->nama }}</td>
                    <td>{{ $data->nama_makanan }}</td>
                    <td>{{ $data->kategori }}</td>
                    <td>
                        <span
                            class="badge {{ $data->status_ == 'Sedisetujuilesai' ? 'badge-success' : 'badge-warning' }}">
                            {{ $data->status_ == 'disetujui' ? 'disetujui' : 'pending' }}
                        </span>
                    </td>

                    <td>{{ $data->tanggal }}</td>
                    <td>{{ $data->deskripsi }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
