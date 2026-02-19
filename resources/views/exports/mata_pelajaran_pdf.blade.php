<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #eee; }
    </style>
</head>
<body>

<h3 style="text-align:center">DATA MATA PELAJARAN</h3>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Nama</th>
            <th>Jurusan</th>
            <th>Bobot</th>
            <th>Jenis</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->kode_feeder }}</td>
                <td>{{ $row->nama }}</td>
                <td>{{ $row->jurusan->nama ?? '-' }}</td>
                <td style="text-align:center">{{ $row->bobot }}</td>
                <td>{{ $row->jenis }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
