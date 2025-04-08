<?php
require_once 'dbkoneksi.php';

// Definisikan query dengan benar
$sql = "SELECT * FROM mahasiswa ORDER BY thn_masuk DESC";

// Jalankan query
$rs = $dbh->query($sql);

// Tampilkan hasil query dengan fetchAll
foreach($rs->fetchAll(PDO::FETCH_OBJ) as $row){
    echo '<br/>'.$row->nim.' - ' .$row->nama;
}
?>
