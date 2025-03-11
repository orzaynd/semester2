<?php
$susu = ["indomilk", "ultramilk", "soya", "cimory"];

//menghapus elemen pertama 
$awal = array_shift($susu);

//Hasil
echo "Susu yang di hapus : $awal <br>";
echo "Array setelah shift <br>";
foreach ($susu as $r) {
    echo "$r <br>";
}
?>