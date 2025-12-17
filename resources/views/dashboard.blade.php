<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Booking</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
        }

        .badge {
            font-size: 0.9rem;
            padding: 0.45em 0.75em;
        }
    </style>
</head>

<body>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">📋 Dashboard Booking Meeting Room</h4>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Pemesan</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->id }}</td>
                                            <td>{{ $booking->booked_by }}</td>
                                            <td>{{ $booking->booking_date }}</td>
                                            <td>{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                                            <td>
                                                <!-- SEMUA STATUS HIJAU -->
                                                <span class="badge bg-success">
                                                    {{ $booking->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                Belum ada data booking
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer text-center text-muted">
                        Meeting Room Booking System © {{ date('Y') }}
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

</html>
