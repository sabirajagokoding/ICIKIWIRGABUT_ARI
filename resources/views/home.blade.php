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
                {{-- IMPORT DATA MAHASISWA --}}
                @if (session('role') === 1)
                    <div class="card mb-5 mt-3">
                        <div class="card-header">
                            <i class="fas fa-file-csv me-2"></i> Import Data CSV
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            <form action="{{ route('csv.upload') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="file">Pilih File CSV</label>
                                    <p>Gunakan format CSV : <strong>nim;nama;kelas</strong></p>
                                    <input type="file" name="file" class="form-control" accept=".csv" required>
                                </div>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-upload me-2"></i> Upload & Import
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
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

            <div class="col-12 col-md-4 pe-auto" style="cursor: pointer; " onclick="showTable('attended')">
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

            <div class="col-12 col-md-4" style="cursor: pointer;" onclick="showTable('notAttended')">
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-chart-pie me-2"></i> Grafik Kehadiran
                </div>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle bg-transparent text-white fw-bold" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false" id="cs-prodi">
                        Kelas
                    </button>
                    <ul class="dropdown-menu">
                        <li><button class="dropdown-item" onclick="prodiFilter('all')">Semua</button></li>
                        <li><button class="dropdown-item" onclick="prodiFilter('SI')">SI</button></li>
                        <li><button class="dropdown-item" onclick="prodiFilter('SD')">SD</button></li>
                        <li><button class="dropdown-item" onclick="prodiFilter('SK')">SK</button></li>
                        <li><button class="dropdown-item" onclick="prodiFilter('SE')">SE</button></li>
                        <li><button class="dropdown-item" onclick="prodiFilter('D3')">D3</button></li>
                    </ul>
                </div>
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
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari nama atau NIM...">
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
                                <th class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle bg-transparent text-black fw-bold"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ts-prodi">
                                        Kelas
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><button class="dropdown-item" onclick="prodiFilter('all')">Semua</button>
                                        </li>
                                        <li><button class="dropdown-item" onclick="prodiFilter('SI')">SI</button></li>
                                        <li><button class="dropdown-item" onclick="prodiFilter('SD')">SD</button></li>
                                        <li><button class="dropdown-item" onclick="prodiFilter('SK')">SK</button></li>
                                        <li><button class="dropdown-item" onclick="prodiFilter('SE')">SE</button></li>
                                        <li><button class="dropdown-item" onclick="prodiFilter('D3')">D3</button></li>
                                    </ul>
                                </th>
                                <th>Status</th>
                                <th>Waktu</th>
                                <th>Diupdate oleh</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        /*
         ** --------------------------
         **     GLOBAL VARIABLES 
         ** --------------------------
         */
        let peminatanFiltered = 'all'; // filter kelas/peminatan, default all
        let dataMahasiswa = []; // global variable to store fetched data mahasiswa
        let processing = true; //scanner processing state
        var htmlscanner; // html5 qrcode scanner instance

        /*
        ** Show & Remove Loader spinner Overlay
        */
        function showLoader() {
            const loaderOverlay = `
            <div id="loader-overlay" class="d-flex justify-content-center align-items-center"
                style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.7);z-index:9999;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            `;
            $("body").append(loaderOverlay);
        }

        function removeLoader() {
            $("#loader-overlay").remove();
        }


        // Function to fetch updated data from the server
        // @return : updates 'dataMahasiswa' global variable array by new data from server
        async function getUpdatedData() {
            try {
                const res = await fetch('/mahasiswa');
                const data = await res.json();
                dataMahasiswa = data.allAttended;
            } catch (err) {
                console.error("Error fetching chart data:", err);
            }
        }


        // Ensure the DOM is fully loaded before running scripts
        function domReady(fn) {
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                setTimeout(fn, 1); // Use setTimeout to ensure the DOM is fully loaded
            } else {
                document.addEventListener('DOMContentLoaded', fn);
            }
        }





        /*
         ** ------------------------
         ** SCAN & UPDATE ATTANDANCE
         ** ------------------------
         */

        // Handle QR code scanning when the DOM is ready
        domReady(function() {
            var nim;
            let result;
            var myqr = document.getElementById('scanResult');
            var lastResult;

            function onScanSuccess(decodedText, decodedResult) {
                if (decodedText !== lastResult) {
                    lastResult = decodedText;
                    processing = true;
                }

                if (processing) {
                    processing = false;
                    htmlscanner.pause();
                    let scanResult = decryptAES(decodedText);
                    let nim = scanResult.split(";")[0];
                    confirmationMahasiswa(nim); // call confirmation modal
                    myqr.classList.remove('d-none');
                    myqr.innerHTML = `<strong>Hasil Scan Terakhir:</strong> ${scanResult}`;
                }
            }

            function onScanError(errMessege) {
                processing = true;
            }

            htmlscanner = new Html5QrcodeScanner(
                "qrReader", {
                    fps: 10,
                    qrbox: 250,
                    videoConstraints: {
                        facingMode: "environment"
                    }
                }
            )
            htmlscanner.render(onScanSuccess, onScanError);    
        })


        // Function to handle confirmation of mahasiswa presence
        function confirmationMahasiswa(nim) {
            showLoader();
            fetch(`/mahasiswa/${nim}`) // check existance of NIM
                .then(res => res.json())
                .then(data => {
                    removeLoader();
                    if (data.message) { // NIM not found
                        Swal.fire({
                            icon: "error",
                            title: "GAGAL!",
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        htmlscanner.resume(); // resume scanner
                    } else { // nim found
                        if (data.status === 1) { // mahasiswa already attended
                            Swal.fire({
                                icon: "info",
                                title: "Sudah Hadir!",
                                text: data.nama + " sudah ditandai hadir.",
                                showConfirmButton: false,
                                timer: 1500
                            });
                            htmlscanner.resume(); // resume scanner
                        } else { // mahasiswa not attended
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
                                    updateAttendance(data, nim); // update mahasiswa attendance status with nim = nim
                                } else if (result.dismiss === Swal.DismissReason.cancel) {
                                    Swal.fire({
                                        title: "Dibatalkan",
                                        text: "Tidak ada perubahan pada kehadiran.",
                                        icon: "error"
                                    });
                                    htmlscanner.resume(); // resume scanner
                                }
                            });
                        }

                    }
                })
                .catch(err => {
                    console.error("Fetch error:", err);
                })
                .finally(() => {
                    removeLoader(); // ensure loader removed
                    htmlscanner.resume(); // resume scanner
                });
        }


        /* Update attandance status from 0 to 1 in database
        ** @param data : object mahasiswa for showing mahasiswa name in alert
        ** @param nimm : nim of mahasiswa to be updated
        */ 
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
                beforeSend: function() {
                    // show spinner
                    showLoader();
                },
                success: function(response) {
                    domReady(async () => {
                        await getUpdatedData(); // wait till load data from server done
                        fetchAttendanceData('all'); //
                    });
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
                        timer: 1500
                    });
                },
                complete: function() {
                    // delete spinner
                    removeLoader();
                    htmlscanner.resume(); // resume scanner
                }
            });
        }


        /* Handler for manual NIM input
        ** @trigger : submit form
        */
        document.getElementById('NIMsearch').addEventListener('submit', function(event) {
            event.preventDefault();
            const nimInput = document.getElementById('nim').value.trim();
            if (nimInput) {
                confirmationMahasiswa(nimInput);
                document.getElementById('nim').value = ''; // clear input field
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'NIM tidak boleh kosong.',
                    confirmButtonText: 'OK'
                });
            }
        });


        /* AES decryption function
        ** @param encryptedData : hex string
        ** @return original text
        ** used by : handle qr code scanning
        */
        function decryptAES(encryptedData) {
            // Kunci AES harus sama dengan di R
            const secretKey = "wisudastisss6364";
            const iv = CryptoJS.enc.Utf8.parse("1234567890123456"); // harus sama dengan IV

            // Convert hex ke WordArray CryptoJS
            const cipherWords = CryptoJS.enc.Hex.parse(encryptedData);

            // Dekripsi
            const bytes = CryptoJS.AES.decrypt({
                    ciphertext: cipherWords
                },
                CryptoJS.enc.Utf8.parse(secretKey), {
                    iv: iv
                }
            );

            const originalText = bytes.toString(CryptoJS.enc.Utf8) ? bytes.toString(CryptoJS.enc.Utf8) : 'unknown';

            return originalText;
        }





        /*
         ** ---------------------
         **       DASHBOARD
         ** ---------------------
         */

        /*
        ** Load data and show to chart and statistics cards when DOM ready
        */
        domReady(async () => {
            showLoader();
            await getUpdatedData(); // wait till load data from server done
            fetchAttendanceData(peminatanFiltered); 
            removeLoader();
        });

        // Card statistik dan Pie Chart update base on data
        function fetchAttendanceData(prodi) {
            let semua = dataMahasiswa.filter(item => prodi === "" || prodi === "all" || item.kelas.includes(prodi));
            let hadir = dataMahasiswa.filter(item => item.status === 1 && (prodi === "" || prodi === "all" || item.kelas
                .includes(prodi)));
            let tidakHadir = dataMahasiswa.filter(item => item.status === 0 && (prodi === "" || prodi === "all" || item
                .kelas.includes(prodi)));
            // Update card statistics
            $('#allAttended').text(semua.length);
            $('#attended').text(hadir.length);
            $('#notAttended').text(tidakHadir.length);
            // Update pie chart
            attendanceChart.data.datasets[0].data = [hadir.length, tidakHadir.length];
            attendanceChart.update();
        }

        // Filter table based on selected prodi
        function prodiFilter(peminatans) {
            peminatanFiltered = peminatans;
            let peminatan = peminatans;
            if (peminatans === 'all') {
                peminatan = 'Kelas';
            }
            document.getElementById('cs-prodi').innerHTML = peminatan;
            document.getElementById('ts-prodi').innerHTML = peminatan;
            fetchAttendanceData(peminatans);

            let rows = document.querySelectorAll("#tableBody tr");
            rows.forEach(row => {
                let peminatanCells = row.cells[3].textContent.toLowerCase(); // kolom Peminatan
                if (peminatanCells.includes(peminatans.toLowerCase()) || peminatans === "all") {
                    row.style.display = ""; // tampilkan
                } else {
                    row.style.display = "none"; // sembunyikan
                }
            });
        }


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


        // Show table based on type (attended or not attended)
        function showTable(type) {
            tableType = type;
            document.getElementById('chartSection').classList.add('d-none');
            document.getElementById('tableSection').classList.remove('d-none');
            let AttendedStudents;
            let notAttendedStudents;
            const tableTitle = document.getElementById('tableTitle');
            AttendedStudents = dataMahasiswa.filter(m => m.status === 1);
            notAttendedStudents = dataMahasiswa.filter(m => m.status === 0);
            if (type === 'attended') {
                tableTitle.innerHTML =
                    '<i class="fas fa-check-circle me-2" style="color: #10b981;"></i> Daftar Yang Telah Hadir';
                populateTable(AttendedStudents, 'Hadir', '#10b981');
            } else {
                tableTitle.innerHTML =
                    '<i class="fas fa-clock me-2" style="color: #ef4444;"></i> Daftar Yang Belum Hadir';
                populateTable(notAttendedStudents, 'Belum Hadir', '#ef4444');
            }
        }

        // search filter
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase(); // ambil input & lowercase
            let rows = document.querySelectorAll("#tableBody tr");
            rows.forEach(row => {
                let nim = row.cells[1].textContent.toLowerCase(); // kolom NIM
                let nama = row.cells[2].textContent.toLowerCase(); // kolom Nama
                let kelas = row.cells[3].textContent.toLowerCase(); // kolom Kelas
                if ((nim.includes(filter) || nama.includes(filter)) && (kelas.includes(peminatanFiltered
                        .toLowerCase()) || peminatanFiltered === 'all')) {
                    row.style.display = ""; // tampilkan
                } else {
                    row.style.display = "none"; // sembunyikan
                }
            });
        });


        function populateTable(data, status, color) {
            const tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = '';
            data.forEach((student, index) => {
                tableBody.innerHTML +=
                    `<tr> 
                    <td> ${index + 1} </td> 
                    <td> ${student.nim} </td> 
                    <td> ${student.nama} </td> 
                    <td> ${student.kelas} </td> 
                    <td> 
                        <span class="btn badge" onclick="updateStatus('${student.nim}','${status}', 0)" style="background-color:${color};" > ${status} </span>
                    </td> 
                    <td> ${student.updated_at_} </td> 
                    <td> ${student.updated_by} </td> 
                </tr>`;
            });

            prodiFilter(peminatanFiltered);
        }

        // Function to update status back to 'Belum Hadir'
        function updateStatus(nim, status, newStatus) {
            if (status === 'Belum Hadir' || @json(session('role'))==0) {
                return; // set to not attended
            }
            showLoader();
            fetch('/update-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        nim: nim,
                        status: newStatus
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        domReady(async () => {
                            await getUpdatedData(); // wait till load data from server done
                            fetchAttendanceData('all');
                            removeLoader();
                            Swal.fire({
                                icon: 'success',
                                title: 'Status Diperbarui',
                                text: `Status mahasiswa dengan NIM ${nim} telah dihapus.`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        });
                    } else {
                        alert('Gagal: ' + data.message);
                    }
                })
                .catch(err => console.error(err));
        }


        function showChart() {
            document.getElementById('chartSection').classList.remove('d-none');
            document.getElementById('tableSection').classList.add('d-none');
        }
    </script>
@endsection
