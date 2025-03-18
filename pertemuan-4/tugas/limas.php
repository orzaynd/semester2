<?php

class LimasSegiEmpat {
    public $panjang_alas;
    public $lebar_alas;
    public $tinggi;
    
    public function __construct($panjang_alas, $lebar_alas, $tinggi) {
        $this->panjang_alas = $panjang_alas;
        $this->lebar_alas = $lebar_alas;
        $this->tinggi = $tinggi;
    }
    
    public function getVolume() {
        return (1/3) * $this->panjang_alas * $this->lebar_alas * $this->tinggi;
    }
    
    public function getLuasPermukaan() {
        $luas_alas = $this->panjang_alas * $this->lebar_alas;
        $sisi_miring = sqrt(pow(($this->panjang_alas / 2), 2) + pow($this->tinggi, 2));
        $luas_sisi = ($this->panjang_alas * $sisi_miring) / 2;
        return $luas_alas + (4 * $luas_sisi);
    }
    
    public function cetak() {
        echo "Limas Segi Empat dengan panjang alas ".$this->panjang_alas.", lebar alas ".$this->lebar_alas." dan tinggi ".$this->tinggi;
        echo "<br>Volume = " . $this->getVolume();
        echo "<br>Luas Permukaan = " . $this->getLuasPermukaan();
    }
}
?>
