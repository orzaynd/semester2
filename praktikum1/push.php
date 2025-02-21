<?php
//array_push
$komponen = ["Mobo", "processor", "RAM", "SSD", "GPU"];

//menambahkan elemen di akhir array
array_push($komponen, "PSU", "Cassing");

echo "setelah push<br>";
foreach ($komponen as $k){
    echo $k. "<br>";
}
?>