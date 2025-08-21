<?php
include '../config/koneksi.php';

$bulan = $_POST['bulan'];
$tahun = $_POST['tahun'];
$kelompok = $_POST['kelompok'];

// cek apakah sudah ada data
$cek = mysqli_query($conn, "SELECT * FROM data_antisipasi WHERE bulan='$bulan' AND tahun='$tahun' AND kelompok='$kelompok'");
if (mysqli_num_rows($cek) > 0) {
    // update
    $sql = "UPDATE saldo SET 
            a=a+{$_POST['a']}, b=b+{$_POST['b']}, c=c+{$_POST['c']},
            d=d+{$_POST['d']}, e=e+{$_POST['e']}, f=f+{$_POST['f']},
            g=g+{$_POST['g']}, h=h+{$_POST['h']}, i=i+{$_POST['i']},
            j=j+{$_POST['j']}, k=k+{$_POST['k']}, l=l+{$_POST['l']}
            WHERE bulan='$bulan' AND tahun='$tahun' AND kelompok='$kelompok'";
} else {
    // insert baru
    $sql = "INSERT INTO data_antisipasi (bulan, tahun, kelompok, a,b,c,d,e,f,g,h,i,j,k,l)
            VALUES ('$bulan','$tahun','$kelompok',
            {$_POST['a']},{$_POST['b']},{$_POST['c']},
            {$_POST['d']},{$_POST['e']},{$_POST['f']},
            {$_POST['g']},{$_POST['h']},{$_POST['i']},
            {$_POST['j']},{$_POST['k']},{$_POST['l']})";
}
mysqli_query($conn, $sql);
header("Location: data_antisipasi.php");
?>
