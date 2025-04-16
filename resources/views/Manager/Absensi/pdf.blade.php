<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi</title>
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
    <h2 class="center">Absensi</h2>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Waktu Masuk</th>
                <th>Status</th>
                <th>Waktu Pulang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($user as $data)
                <tr>
                    <td>{{ $data->user->nama }}</td>
                    <td>{{ $data->tanggal }}</td>
                    <tdp>{{ $data->waktu_masuk }}</tdp>
                    <td>{{ $data->status }}</td>
                    <td>{{ $data->waktu_pulang }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
