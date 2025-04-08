<?php
require_once 'dbkoneksi.php';

//tangkap data dari form
$_kode = $_POST['kode'];
$_nama = $_POST['nama'];
$_kaprodi = $_POST['kaprodi'];
$_proses = $_POST['proses'];

if($_proses == 'simpan'){
    $sql = "INSERT INTO prodi(kode,nama,kaprodi)VALUES(?,?,?)";
}elseif($_proses == "update"){
    $id_edit = $_POST['id_edit'];
    $ar_data[] = $id_edit;
    $sql = "UPDATE prodi SET nama=?,kaprodi=?,kode=? WHERE id=?";
}elseif($_proses == "hapus"){
    $id_hapus = $_POST['id_hapus'];
    $id_data = [$id_hapus];
    $sql = "DELETE FROM prodi WHERE id=?";
}
?>