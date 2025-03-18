<?php
require_once 'limas.php'; /** Memanggil file kelas LimasSegiEmpat */

$limas1 = new LimasSegiEmpat(10, 8, 12); /** Objek limas pertama */
$limas2 = new LimasSegiEmpat(15, 12, 18); /** Objek limas kedua */

echo 'Limas 1:';
echo '<br>Panjang alas: ' . $limas1->panjang_alas;
echo '<br>Lebar alas: ' . $limas1->lebar_alas;
echo '<br>Tinggi: ' . $limas1->tinggi;
echo '<br>Volume: ' . $limas1->getVolume();
echo '<br>Luas Permukaan: ' . $limas1->getLuasPermukaan();

echo '<br><br>'; /** Spasi */

echo 'Limas 2:';
echo '<br>Panjang alas: ' . $limas2->panjang_alas;
echo '<br>Lebar alas: ' . $limas2->lebar_alas;
echo '<br>Tinggi: ' . $limas2->tinggi;
echo '<br>Volume: ' . $limas2->getVolume();
echo '<br>Luas Permukaan: ' . $limas2->getLuasPermukaan();
?>
