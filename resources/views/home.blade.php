@extends('master')

@section('konten')
    <h4>Selamat Datang <b>{{ Auth::user()->name }}</b></h4>

    <div class="container mt-4">
        <!-- Scanner & Input Section -->
        <div class="row mb-4">
            <!-- Camera Scanner -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-camera me-2"></i> Scanner Barcode / QR
                    </div>
                    <div class="card-body">
                        <div id="qrRegion" class="bg-light rounded p-2">
                            <div id="qrReader" style="width:100%;"></div>
                            <small class="text-muted d-block mt-2">
                                Arahkan kamera ke barcode.
                            </small>
                        </div>

                        <div id="scanResult" class="alert alert-info mt-3"></div>
                    </div>
                </div>
            </div>


            <!-- Manual Input -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-edit me-2"></i> Input Manual NIM
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label for="nim" class="form-label">NIM Mahasiswa Wisudawan</label>
                                <input type="text" class="form-control form-control-lg" id="nim"
                                    placeholder="Masukkan NIM mahasiswa">
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-check me-2"></i> Konfirmasi Kehadiran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-0">Total Undangan</h6>
                            <h3 class="mb-0 text-primary">150</h3>
                        </div>
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stats-card" style="border-left-color: #10b981;">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-0">Telah Hadir</h6>
                            <h3 class="mb-0" style="color: #10b981;">75</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x" style="color: #10b981;"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stats-card" style="border-left-color: #ef4444;">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-0">Belum Hadir</h6>
                            <h3 class="mb-0" style="color: #ef4444;">75</h3>
                        </div>
                        <i class="fas fa-clock fa-2x" style="color: #ef4444;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div id="chartSection" class="card">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2"></i> Grafik Kehadiran
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Table Section (Hidden by default) -->
        <div id="tableSection" class="card table-container d-none">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span id="tableTitle">
                    <i class="fas fa-list me-2"></i> Daftar Kehadiran
                </span>
                <button class="btn btn-sm back-btn text-white" onclick="showChart()">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Grafik
                </button>
            </div>
            <div class="card-body">
                <!-- Search Bar -->
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Cari nama atau NIM...">
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Program Studi</th>
                                <th>Status</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function domReady(fn) {
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                setTimeout(fn, 1); // Use setTimeout to ensure the DOM is fully loaded
            } else {
                document.addEventListener('DOMContentLoaded', fn);
            }
        }

        domReady(function() {
            var nim;
            let result;
            var myqr = document.getElementById('scanResult');
            var lastResult, countResults = 0;

            function onScanSuccess(decodedText, decodedResult) {
                if (decodedText !== lastResult) {
                    ++countResults;
                    lastResult = decodedText;
                    myqr.innerHTML =
                        `<div class="alert alert-success">Hasil: ${decodedText} <br> Total hasil: ${countResults}</div>`;
                    let nim = decodedText.split(";")[0];
                    console.log(nim);

                    fetch(`/mahasiswa/${nim}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                console.log("Error:", data.message);
                            } else {
                                console.log("Data Mahasiswa:", data);
                            }
                        })
                        .catch(err => console.error("Fetch error:", err));

                }
            }

            var htmlscanner = new Html5QrcodeScanner(
                "qrReader", {
                    fps: 10,
                    qrbox: 250
                }
            )
            htmlscanner.render(onScanSuccess)
        })

        //accessData
        let attendedStudents = @json($mahasiswas);
        console.log(attendedStudents.nim);



        const notAttendedStudents = [{
            nim: "222112006",
            nama: "Eka Putri Maharani",
            kelas: "D-IV Statistika",
            status: "-"
        }];

        // Initialize Chart
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Telah Hadir', 'Belum Hadir'],
                datasets: [{
                    data: [75, 75],
                    backgroundColor: ['#10b981', '#ef4444'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 14
                            }
                        }
                    }
                },
                onHover: (event, activeElements) => {
                    event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
                },
                onClick: (event, activeElements) => {
                    if (activeElements.length > 0) {
                        const index = activeElements[0].index;
                        showTable(index === 0 ? 'attended' : 'notAttended');
                    }
                }
            }
        });

        function showTable(type) {

            document.getElementById('chartSection').classList.add('d-none');
            document.getElementById('tableSection').classList.remove('d-none');

            const tableTitle = document.getElementById('tableTitle');
            if (type === 'attended') {
                tableTitle.innerHTML =
                    '<i class="fas fa-check-circle me-2" style="color: #10b981;"></i> Daftar Yang Telah Hadir';
                populateTable(attendedStudents, 'Hadir', '#10b981');
            } else {
                tableTitle.innerHTML = '<i class="fas fa-clock me-2" style="color: #ef4444;"></i> Daftar Yang Belum Hadir';
                populateTable(notAttendedStudents, 'Belum Hadir', '#ef4444');
            }
        }

        function populateTable(data, status, color) {
            const tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = '';
            data.forEach((student, index) => {
                tableBody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${student.nim}</td>
                        <td>${student.nama}</td>
                        <td>${student.kelas}</td>
                        <td><span class="badge" style="background-color:${color};">${status}</span></td>
                        <td>${student.status}</td>
                    </tr>`;
            });
        }

        function showChart() {
            document.getElementById('chartSection').classList.remove('d-none');
            document.getElementById('tableSection').classList.add('d-none');
        }
    </script>
@endsection
