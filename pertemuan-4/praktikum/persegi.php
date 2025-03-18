<?php
// class persegi panjang
class PersegiPanjang {
    public $panjang;
    public $lebar;

    // konstruktor persegi panjang
    function __construct($panjang, $lebar) {
        $this->panjang = $panjang;
        $this->lebar = $lebar;
    }

    // method untuk menghitung luas
    function getLuas() {
        return $this->panjang * $this->lebar;
    }

    // method hitung keliling
    function getKeliling() {
        return 2 * ($this->panjang + $this->lebar);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="section">
        <h2>Contoh penggunaan persegi panjang</h2>
        <?php
        $pp = new PersegiPanjang(10, 8); // Tambahkan titik koma

        echo "Panjang : " . $pp->panjang . "<br>";
        echo "Lebar : " . $pp->lebar . "<br>";
        echo "<hr>";
        echo "Luas : " . $pp->getLuas() . "<br>"; // Pastikan return ada di getLuas()
        echo "Keliling : " . $pp->getKeliling() . "<br>"; // Perbaiki pemanggilan method
        ?>
    </div>
</body>
</html>