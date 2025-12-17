<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
</head>

<body>

    <h1>DASHBOARD BOOKING</h1>

    @if ($bookings->count() == 0)
        <p>Belum ada data.</p>
    @else
        <table border="1" cellpadding="8">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Status</th>
            </tr>

            @foreach ($bookings as $booking)
                <tr>
                    <td>{{ $booking->id }}</td>
                    <td>{{ $booking->name ?? '-' }}</td>
                    <td>{{ $booking->date ?? '-' }}</td>
                    <td>{{ $booking->status ?? '-' }}</td>
                </tr>
            @endforeach
        </table>
    @endif

</body>

</html>
