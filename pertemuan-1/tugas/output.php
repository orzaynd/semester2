<!DOCTYPE html>
<html lang="id">
<head>
    <title>Hasil Penilaian</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Hasil Penilaian Siswa</h2>
    <hr>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = $_POST['nama'];
        $matkul = $_POST['matkul'];
        $uts = $_POST['uts'];
        $uas = $_POST['uas'];
        $tugas = $_POST['tugas'];

        // Fungsi untuk menghitung nilai akhir
        function hitungNilaiAkhir($uts, $uas, $tugas) {
            return (0.3 * $uts) + (0.35 * $uas) + (0.35 * $tugas);
        }

        // Fungsi untuk menentukan grade
        function tentukanGrade($nilai) {
            if ($nilai < 0 || $nilai > 100) {
                return 'I';
            } elseif ($nilai <= 35) {
                return 'E';
            } elseif ($nilai <= 55) {
                return 'D';
            } elseif ($nilai <= 69) {
                return 'C';
            } elseif ($nilai <= 84) {
                return 'B';
            } else {
                return 'A';
            }
        }

        // Fungsi untuk menentukan predikat
        function tentukanPredikat($grade) {
            $predikat = [
                'E' => 'Sangat Kurang',
                'D' => 'Kurang',
                'C' => 'Cukup',
                'B' => 'Memuaskan',
                'A' => 'Sangat Memuaskan',
                'I' => 'Tidak Ada'
            ];
            return $predikat[$grade];
        }

        // Fungsi untuk mengecek kelulusan
        function cekKelulusan($nilaiAkhir) {
            return $nilaiAkhir > 55 ? 'Lulus' : 'Tidak Lulus';
        }

        // Proses perhitungan
        $nilaiAkhir = hitungNilaiAkhir($uts, $uas, $tugas);
        $grade = tentukanGrade($nilaiAkhir);
        $predikat = tentukanPredikat($grade);
        $kelulusan = cekKelulusan($nilaiAkhir);

        // Tampilkan hasil
        echo "
        <table class='table table-bordered'>
            <tr><td><b>Nama Lengkap</b></td><td>$nama</td></tr>
            <tr><td><b>Mata Kuliah</b></td><td>$matkul</td></tr>
            <tr><td><b>Nilai Akhir</b></td><td>$nilaiAkhir</td></tr>
            <tr><td><b>Grade</b></td><td>$grade</td></tr>
            <tr><td><b>Predikat</b></td><td>$predikat</td></tr>
            <tr><td><b>Status Kelulusan</b></td><td>$kelulusan</td></tr>
        </table>
        ";
    } else {
        echo "<p class='text-danger'>Akses tidak valid!</p>";
    }
    ?>

    <a href="index.php" class="btn btn-secondary">Kembali ke Form</a>
    <hr>
    <footer>By @oryza</footer>
</div>

</body>
</html>
