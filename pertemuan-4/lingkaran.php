<?php

class Lingkaran{ /** ini adalah class */
    public $jari; /** ini adalah variabel */
    public const PHI = 3.14; /** ini adalah constanta */

    public function __construct($jari) {
        $this->jari = $jari; /** ini bagian dari variabel $this */
    }

    public function getLuas(){ /**ini method ktandanya ada ()  */
        $luas = self::PHI * $this->jari * $this->jari; /** self::PHI ini untuk konstanta yang artinya PHI*r*r */
        return $luas;
    }
    public function getKeliling(){ /**ini method */
        $keliling = 2.0 * self::PHI * $this->jari; /** ini artinya 2 *phi*r */
        return $keliling;
    }
    public function cetak(){
        echo "Lingkaran dengan jari-jari".$this->jari;
        echo "<br>Luas=". $this->getLuas();
        echo "<br>Keliling=". $this->getKeliling();
    }
}
?>