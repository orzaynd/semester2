<?php
$laptop = ["Asus", "Lenovo", "MSI", "HP"];

// menambahkan elemen di awal 
array_unshift($laptop, "HP", "MSI");

//Hasil
echo "Hasil"; 
foreach ($laptop as $p){
    echo "<br>". $p;
}
?>