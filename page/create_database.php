<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cari Data NIK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgzD9TUmBlfVpR9cXtAWCfX5JMcXZqmuvVL1C4cYHdnYO95_K2Y3vu3oWEDPtXuePg1LoRdMy2RSerbN5sPvZ2nMDfJ936cg5UHww2EasLS-Bal_YEW76uBa0ILh4FR9h6ryWIDWk4AG7ldTiR-AvDiGaUGCsJcUsrdITlgTAbc091LfErRNWs7EJZwF3o/w945-h600-p-k-no-nu/walpaper%20BABAT1.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            font-size: 1.1rem;
        }

        .overlay {
            background-color: rgba(255, 255, 255, 0.85);
            min-height: 100vh;
            padding: 2rem 1.25rem;
        }

        h2.mb-4 {
            font-size: 1.75rem;
            font-weight: bold;
        }

        input.form-control,
        button.btn {
            font-size: 1.1rem;
            padding: 1rem;
            border-radius: 0.75rem;
        }

        .btn-danger:hover {
            background-color: #c51210;
            transform: scale(1.02);
            transition: all 0.2s ease-in-out;
        }

        .error {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }

        .form-control::placeholder {
            text-align: center;
        }

        table th {
            background-color: rgb(28, 38, 167) !important;
            color: white !important;
        }

        .table-responsive {
            overflow-x: auto;
        }

        @media (max-width: 576px) {
            h2.mb-4 {
                font-size: 1.5rem;
            }

            .form-control,
            .btn {
                font-size: 1.25rem;
                padding: 1rem;
            }

            .table th,
            .table td {
                font-size: 14px;
                padding: 0.5rem;
            }

            .table-responsive-stacked table thead {
                display: none;
            }

            .table-responsive-stacked table,
            .table-responsive-stacked tbody,
            .table-responsive-stacked tr,
            .table-responsive-stacked td {
                display: block;
                width: 100%;
            }

            .table-responsive-stacked td {
                text-align: left;
                padding-left: 50%;
                position: relative;
                border: none;
                border-bottom: 1px solid #dee2e6;
                word-break: break-word;
            }

            .table-responsive-stacked td::before {
                content: attr(data-label);
                position: absolute;
                left: 1rem;
                top: 0;
                width: 45%;
                white-space: nowrap;
                font-weight: bold;
                font-size: 0.9rem;
            }

            .table-responsive-stacked tr {
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 0.25rem;
                background: #fff;
                padding: 0.5rem 0;
            }
        }
    </style>
</head>

<body>
    <div class="overlay">
        <div class="container-fluid">
            <div class="row justify-content-center mb-4">
                <div class="col-lg-6 col-md-8 col-sm-10 col-12 text-center">
                    <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgzD9TUmBlfVpR9cXtAWCfX5JMcXZqmuvVL1C4cYHdnYO95_K2Y3vu3oWEDPtXuePg1LoRdMy2RSerbN5sPvZ2nMDfJ936cg5UHww2EasLS-Bal_YEW76uBa0ILh4FR9h6ryWIDWk4AG7ldTiR-AvDiGaUGCsJcUsrdITlgTAbc091LfErRNWs7EJZwF3o/w945-h600-p-k-no-nu/walpaper%20BABAT1.png"
                        class="img-fluid mx-auto d-block mb-3" style="max-width: 150px;" alt="Logo">
                    <h2 class="mb-4">Cari Data Berdasarkan NIK</h2>
                    <div class="mb-3">
                        <input type="tel" class="form-control" id="nik" name="nik" placeholder="Masukkan NIK KTP" maxlength="16"
                            pattern="[0-9]*" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div>
                        <button class="btn btn-danger w-100" onclick="search()" id="searchBtn">Cari</button>
                    </div>
                </div>
            </div>
            <div id="result" class="mt-4"></div>

            <!-- Modal Alert -->
            <div class="modal fade" id="modalAlert" tabindex="-1" aria-labelledby="modalAlertLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="modalAlertLabel">Peringatan</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            Masukkan NIK terlebih dahulu!
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function search() {
            const nik = document.getElementById("nik").value.trim();

            if (nik === "") {
                const modal = new bootstrap.Modal(document.getElementById('modalAlert'));
                modal.show();
                return;
            }

            google.script.run.withSuccessHandler(displayResult).searchNIK(nik);
        }

        function formatRupiah(angka) {
            const number = parseInt(angka, 10);
            if (isNaN(number)) return angka;
            return 'Rp ' + number.toLocaleString('id-ID');
        }

        function displayResult(result) {
            const resultDiv = document.getElementById("result");

            if (!result || result.length === 0 || result === "NIK tidak ditemukan" || result[0] === "NIK Tidak Ditemukan") {
                resultDiv.innerHTML = "<div class='error'>NIK tidak ditemukan</div>";
                return;
            }

            let table = `
      <div class="table-responsive table-responsive-stacked">
        <table class="table table-bordered table-striped table-hover text-center align-middle">
          <thead class="bg-primary">
            <tr>
              <th>NO ANGGOTA</th>
              <th>NIK</th>
              <th>NAMA</th>
              <th>DOMISILI</th>
              <th>TGL DROP</th>
              <th>PINJAMAN</th>
              <th>HARI</th>
              <th>KLP</th>
              <th>KL</th>
            </tr>
          </thead>
          <tbody>`;

            for (let i = 0; i < result.length; i++) {
                const pinjamanFormatted = formatRupiah(result[i][6]);

                table += `
            <tr>
              <td data-label="NO ANGGOTA">${result[i][1]}</td>
              <td data-label="NIK">${result[i][2]}</td>
              <td data-label="NAMA">${result[i][3]}</td>
              <td data-label="DOMISILI">${result[i][4]}</td>
              <td data-label="TGL DROP">${result[i][5]}</td>
              <td data-label="PINJAMAN">${pinjamanFormatted}</td>
              <td data-label="HARI">${result[i][7]}</td>
              <td data-label="KLP">${result[i][8]}</td>
              <td data-label="KL">${result[i][9]}</td>
            </tr>`;
            }

            table += `</tbody></table></div>`;
            resultDiv.innerHTML = table;
        }

        document.getElementById("nik").addEventListener("keydown", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                document.getElementById("searchBtn").click();
            }
        });
    </script>

</body>

</html>