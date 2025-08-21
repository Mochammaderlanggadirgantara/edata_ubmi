<!DOCTYPE html>
<html>

<head>
    <base target="_top">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-image: url('https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgzD9TUmBlfVpR9cXtAWCfX5JMcXZqmuvVL1C4cYHdnYO95_K2Y3vu3oWEDPtXuePg1LoRdMy2RSerbN5sPvZ2nMDfJ936cg5UHww2EasLS-Bal_YEW76uBa0ILh4FR9h6ryWIDWk4AG7ldTiR-AvDiGaUGCsJcUsrdITlgTAbc091LfErRNWs7EJZwF3o/w945-h600-p-k-no-nu/walpaper%20BABAT1.png');
            background-size: cover;
            background-position: right top;
        }

        .search-box {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100px;
        }

        .search-box form {
            display: flex;
            align-items: center;
            width: 10%;
        }

        .search-box input[type="text"] {
            border-radius: 25px;
            padding: 10px;
            border: none;
            outline: none;
            font-size: 16px;
            width: 3000px;
            text-align: center;
            /* tambahkan kode ini */
            margin: auto;
            background-color: #E41B17;
            color: black;
            /* tambahkan kode ini */
        }

        .search-box input[type="button"] {
            border-radius: 25px;
            padding: 10px 20px;
            background-color: #E41B17;
            color: black;
            border: none;
            outline: none;
            font-size: 16px;
            cursor: pointer;
        }

        .search-box input[type="button"]:hover {
            background-color: #E41B17;
        }

        .search-box input[type="button"]:focus {
            outline: none;
        }

        .search-box #searchBtn {
            margin-left: 10px;
            margin-right: 0;
        }

        table {
            border-collapse: inherit;
            margin: auto;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }

        th {
            background-color: #E41B17;
        }

        .error {
            color: black;
            text-align: center;
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <div class="search-box">
        <form>
            <label class="sr-only"> </label>
            <input type="text" id="nik" name="nik" placeholder="Masukkan NIK ktp">
            <input type="button" value="Search" onclick="search()" id="searchBtn">
        </form>
    </div>
    <div id="result"></div>
    <script>
        function search() {
            var nik = document.getElementById("nik").value;
            google.script.run.withSuccessHandler(displayResult).searchNIK(nik);
        }

        function displayResult(result) {
            var resultDiv = document.getElementById("result");
            if (result == "NIK tidak ditemukan") {
                resultDiv.innerHTML = "<div class='error'>NIK tidak ditemukan</div>";
            } else {
                var table = "<table>";
                table += "<tr><th>NO</<th><th>NO ANGGOTA</th><th>NIK</th><th>NAMA</th><th>DOMISILI</th><th>TGL DROP</th><th>PINJAMAN</th><th>HARI</th><th>KLP</th><th>KL</th></tr>";
                for (var i = 0; i < result.length; i++) {
                    table += "<tr><td>" + (i + 1) + "</td><td>" + result[i][1] + "</td><td>" + result[i][2] + "</td><td>" + result[i][3] + "</td><td>" + result[i][4] + "</td><td>" + result[i][5] + "</td><td>" + result[i][6] + "</td><td>" + result[i][7] + "</td><td>" + result[i][8] + "</td><td>" + result[i][9] + ""
                }
                table += "</table>";
                resultDiv.innerHTML = table;
            }
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