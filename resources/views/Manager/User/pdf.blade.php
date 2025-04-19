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
    <h2 class="center">Daftar Pegawai</h2>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Role</th>
                <th>Status</th>
                <th>No Hp</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($user as $data)
                <tr>
                    <td>{{ $data->nama }}</td>
                    <td>{{ $data->role }}</td>
                    <td>{{ $data->status }}</td>
                    <td>{{ $data->no_hp }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
