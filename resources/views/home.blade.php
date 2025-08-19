@extends('master')

@section('konten')
    <h4>Selamat Datang <b>{{ Auth::user()->name }}</b></h4>

    <div class="container mt-4">
        <!-- Scanner & Input Section -->
        <div class="row mb-4">
            <!-- Camera Scanner -->
            <div class="col-md-6 mb-1">
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

                        <div id="scanResult" class="alert alert-info mt-3 d-none"></div>
                    </div>
                </div>
            </div>


            <!-- Manual Input -->
            <div class="col-md-6 mt-1">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-edit me-2"></i> Input Manual NIM
                    </div>
                    <div class="card-body">
                        <form id="NIMsearch">
                            <div class="mb-3">
                                <label for="nim" class="form-label">NIM Mahasiswa Wisudawan</label>
                                <input type="text" class="form-control form-control-lg" id="nim"
                                    placeholder="Masukkan NIM mahasiswa">
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-check me-2"></i> Konfirmasi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card stats-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-0">Total Undangan</h6>
                            <h3 class="mb-0 text-primary" id="allAttended"></h3>
                        </div>
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card stats-card" style="border-left-color: #10b981;">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-0">Telah Hadir</h6>
                            <h3 class="mb-0" style="color: #10b981;" id="attended"></h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x" style="color: #10b981;"></i>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card stats-card" style="border-left-color: #ef4444;">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-0">Belum Hadir</h6>
                            <h3 class="mb-0" style="color: #ef4444;" id="notAttended"></h3>
                        </div>
                        <i class="fas fa-clock fa-2x" style="color: #ef4444;"></i>
                    </div>
                </div>
            </div>
        </div>


        <!-- Chart Section -->
        <div id="chartSection" class="card mb-4">
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
                                <th>Kelas</th>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Ensure the DOM is fully loaded before running scripts
        function domReady(fn) {
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                setTimeout(fn, 1); // Use setTimeout to ensure the DOM is fully loaded
            } else {
                document.addEventListener('DOMContentLoaded', fn);
            }
        }

        //Update attandance function
        function updateAttendance(data, nim) {
            // Get values from input fields
            let token = $("meta[name='csrf-token']").attr("content");

            //ajax
            $.ajax({

                url: `/mahasiswa/${nim}`,
                type: "PUT",
                cache: false,
                data: {
                    "_token": token
                },
                success: function(response) {
                    //show success message
                    fetchAttendanceData();
                    Swal.fire({
                        icon: 'success',
                        title: "Terkonfirmasi!",
                        text: data.nama + " telah ditandai hadir.",
                        showConfirmButton: false,
                        timer: 1400
                    });

                },
                error: function(error) {
                    console.error("Error updating attendance:", error);
                    Swal.fire({
                        icon: "error",
                        title: "GAGAL!",
                        text: "Terjadi kesalahan saat memperbarui kehadiran.",
                        showConfirmButton: false,
                        timer: 2000
                    });
                }

            });
        }

        // Function to handle confirmation of mahasiswa presence
        function confirmationMahasiswa(nim) {
            fetch(`/mahasiswa/${nim}`)
                .then(res => res.json())
                .then(data => {
                    if (data.message) {
                        Swal.fire({
                            icon: "error",
                            title: "GAGAL!",
                            text: data.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        if (data.status === 1) {
                            Swal.fire({
                                icon: "info",
                                title: "Sudah Hadir!",
                                text: data.nama + " sudah ditandai hadir.",
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else {
                            const swalWithBootstrapButtons = Swal.mixin({
                                customClass: {
                                    confirmButton: "btn btn-success",
                                    cancelButton: "btn btn-danger me-2"
                                },
                                buttonsStyling: false
                            });

                            swalWithBootstrapButtons.fire({
                                title: "Konfirmasi Kehadiran?",
                                text: data.nama + " akan ditandai hadir",
                                icon: "question",
                                showCancelButton: true,
                                confirmButtonText: "Konfirmasi Hadir",
                                cancelButtonText: "Batalkan",
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    updateAttendance(data, nim);
                                } else if (result.dismiss === Swal.DismissReason.cancel) {
                                    Swal.fire({
                                        title: "Dibatalkan",
                                        text: "Tidak ada perubahan pada kehadiran.",
                                        icon: "error"
                                    });
                                }

                                processing = false;
                            });
                        }

                    }
                })
                .catch(err => {
                    console.error("Fetch error:", err);
                    processing = false;
                });
        }

        // Handle QR code scanning when the DOM is ready
        domReady(function() {
            var nim;
            let result;
            let processing = false;
            var myqr = document.getElementById('scanResult');
            var lastResult, countResults = 0;

            function onScanSuccess(decodedText, decodedResult) {
                if (decodedText !== lastResult) {
                    lastResult = decodedText;
                    processing = false;
                }

                if (!processing) {
                    processing = true;
                    let nim = decodedText.split(";")[0];
                    confirmationMahasiswa(nim);
                    myqr.classList.remove('d-none');
                    myqr.innerHTML = `<strong>Hasil Scan Terakhir:</strong> ${decodedText}`;
                }
            }

            function onScanError(errMessege) {
                processing = false;
            }

            var htmlscanner = new Html5QrcodeScanner(
                "qrReader", {
                    fps: 10,
                    qrbox: 250
                }
            )

            htmlscanner.render(onScanSuccess, onScanError);
        })

        //Handler for manual NIM input
        document.getElementById('NIMsearch').addEventListener('submit', function(event) {
            event.preventDefault();
            const nimInput = document.getElementById('nim').value.trim();
            if (nimInput) {
                confirmationMahasiswa(nimInput);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'NIM tidak boleh kosong.',
                    confirmButtonText: 'OK'
                });
            }
        });


        // Pie Chart Kehadiran Undangan
        let attendanceChart;
        domReady(function() {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            attendanceChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Telah Hadir', 'Belum Hadir'],
                    datasets: [{
                        data: [0, 0],
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
                        event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' :
                            'default';
                    },
                    onClick: (event, activeElements) => {
                        if (activeElements.length > 0) {
                            const index = activeElements[0].index;
                            showTable(index === 0 ? 'attended' : 'notAttended');
                        }
                    }
                }
            });
        });

        function fetchAttendanceData() {
            document.getElementById
            fetch('/mahasiswa/status')
                .then(res => res.json())
                .then(data => {
                    console.log(data.attended);
                    $('#allAttended').text(data.allAttended);
                    $('#attended').text(data.attended);
                    $('#notAttended').text(data.notAttended);
                    attendanceChart.data.datasets[0].data = [data.attended, data.notAttended];
                    attendanceChart.update();
                })
                .catch(err => console.error('Error fetching chart data:', err));
        }

        fetchAttendanceData();

        //accessData
        const notAttendedStudents = [{
            nim: "222112006",
            nama: "Eka Putri Maharani",
            kelas: "D-IV Statistika",
            status: "-"
        }];
        console.log(typeof(notAttendedStudents));

        // Initialize Chart

        function showTable(type) {

            document.getElementById('chartSection').classList.add('d-none');
            document.getElementById('tableSection').classList.remove('d-none');
            let AttendedStudents;
            let notAttendedStudents;
            const tableTitle = document.getElementById('tableTitle');
            fetch('/mahasiswa')
                .then(res => res.json())
                .then(data => {
                    AttendedStudents = data.allAttended.filter(m => m.status === 1);
                    notAttendedStudents = data.allAttended.filter(m => m.status === 0);
                    console.log(AttendedStudents);
                    if (type === 'attended') {
                        tableTitle.innerHTML =
                            '<i class="fas fa-check-circle me-2" style="color: #10b981;"></i> Daftar Yang Telah Hadir';
                        populateTable(AttendedStudents, 'Hadir', '#10b981');
                    } else {
                        tableTitle.innerHTML =
                            '<i class="fas fa-clock me-2" style="color: #ef4444;"></i> Daftar Yang Belum Hadir';
                        populateTable(notAttendedStudents, 'Belum Hadir', '#ef4444');
                    }
                })
                .catch(err => console.error('Error fetching chart data:', err));
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
                        <td>${student.updated_at_}</td>
                    </tr>`;
            });
        }

        function showChart() {
            document.getElementById('chartSection').classList.remove('d-none');
            document.getElementById('tableSection').classList.add('d-none');
        }
    </script>
@endsection
