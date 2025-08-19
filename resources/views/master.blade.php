<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Hadirin Wisuda - Politeknik Statistika</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- JQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <style>
        :root {
            --primary-blue: #1e3a8a;
            --secondary-blue: #3b82f6;
            --light-blue: #dbeafe;
            --dark-blue: #1e40af;
        }

        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            padding: 1.5rem 0;
        }

        .header h2 {
            margin: 0;
            font-weight: 600;
        }

        .header p {
            margin: 0;
        }

        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        .card-header {
            background-color: var(--primary-blue);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--secondary-blue);
            border-color: var(--secondary-blue);
        }

        .btn-primary:hover {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .stats-card {
            border-left: 4px solid var(--secondary-blue);
        }

        .chart-container {
            position: relative;
            height: 400px;
        }

        #attendanceChart {
            cursor: pointer;
        }

        .logo {
            max-height: 60px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-md-2 text-center">
                    <i class="fas fa-graduation-cap fa-3x mb-2"></i>
                </div>

                <!-- Title -->
                <div class="col-md-6">
                    <h2>Sistem Presensi Terpadu</h2>
                    <p>Politeknik Statistika STIS</p>
                </div>

                <!-- User Menu - FIXED BOOTSTRAP 5 VERSION -->
                <div class="col-md-4 d-flex justify-content-end">
                    <div class="dropdown">
                        <button class="btn dropdown-toggle d-flex align-items-center" type="button" id="userDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-user me-2"></i>
                            <span>{{ Auth::user()->email }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ route('actionlogout') }}">
                                    <i class="fa fa-power-off me-2"></i> Log Out
                                </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Content -->
    <div class="container mt-4">
        @yield('konten')
    </div>

    <!-- html 5 QR code Library JS -->
    <script src="https://unpkg.com/html5-qrcode"></script>
</body>

</html>
