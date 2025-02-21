<?php
$siswa = ["tian", "Asep", "Ahong", "cipe"];

//menampilkan array awal
echo "Array awal :\n";
print_r($siswa);

//menghapus elemen terakhitr di array
$orang_terakhir = array_pop($siswa);

//menampilkan array setelah elemen terakhir
echo "Elemen yang akan di hapus" .$orang_terakhir. "\n";

//menampilkan array setelah penghapusan
echo "<br> setelah penghapusan : <br>";
print_r($siswa);

?>