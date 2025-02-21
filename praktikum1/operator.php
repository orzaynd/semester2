<!DOCTYPE html> 
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator</title>
</head>
<body>
    <h1>Kalkulator</h1>
    <form action="" method="GET">
        <label for="angka1">Angka 1:</label>
        <input type="number" name="angka1" required>
        <br>

        <label for="angka2">Angka 2:</label>
        <input type="number" name="angka2" required>
        <br>

        <label for="operasi">Operasi:</label>
        <select name="operasi" required>
            <option value="tambah">+</option>
            <option value="kurang">-</option>
            <option value="kali">*</option>
            <option value="bagi">/</option>
        </select>
        <br>

        <button type="submit" name="hitung">Hitung</button>
    </form>

    <?php
    if (isset($_GET['hitung'])) {
        $angka1 = isset($_GET['angka1']) ? $_GET['angka1'] : 0;
        $angka2 = isset($_GET['angka2']) ? $_GET['angka2'] : 0;
        $operasi = $_GET['operasi'];
        $hasil = 0;

        switch ($operasi) {
            case 'tambah':
                $hasil = $angka1 + $angka2;
                break;
            case 'kurang':
                $hasil = $angka1 - $angka2;
                break;
            case 'kali':
                $hasil = $angka1 * $angka2;
                break;
            case 'bagi':
                if ($angka2 != 0) {
                    $hasil = $angka1 / $angka2;
                } else {
                    $hasil = "Error: Division by zero";
                }
                break;
            default:
                $hasil = "Error: Invalid operation";
                break;
        }
        echo "<h3>Hasil: $hasil</h3>";
    }
    ?>
</body>
</html>