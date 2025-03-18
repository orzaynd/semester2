<?php 
session_start();

// Jika session 'data_mhs' belum ada, buat array kosong
if (!isset($_SESSION['data_mhs'])) {
    $_SESSION['data_mhs'] = [];
}

// Fungsi untuk menyimpan data ke file JSON
function simpan_data_ke_file($data, $nama_file) {
    file_put_contents($nama_file, json_encode($data, JSON_PRETTY_PRINT));
}

// Fungsi untuk membaca data dari file JSON
function baca_data_dari_file($nama_file) {
    if (file_exists($nama_file)) {
        $data = json_decode(file_get_contents($nama_file), true);
        return $data ? $data : [];
    }
    return [];
}

// Ambil data dari file JSON jika ada
$nama_file = 'data_mahasiswa.json';
$data_mhs = baca_data_dari_file($nama_file);

// Jika form disubmit, tambahkan data baru ke session
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $matkul = $_POST['matkul'];
    $nilai_uts = $_POST['nilai_uts'];
    $nilai_uas = $_POST['nilai_uas'];
    $nilai_tugas = $_POST['nilai_tugas'];

    $new_mhs = new NilaiMahasiswa($nama, $matkul, $nilai_uts, $nilai_uas, $nilai_tugas);
    $_SESSION['data_mhs'][] = $new_mhs;

    // Simpan data ke file JSON
    simpan_data_ke_file($_SESSION['data_mhs'], $nama_file);
}

// Ambil data dari session
$data_mhs = $_SESSION['data_mhs'];
?>

<h3>Input Data Mahasiswa</h3>

<form action="" method="POST">
    <label for="">Nama</label>
    <input type="text" name="nama" required><br><br>

    <label for="">Matkul</label>
    <input type="text" name="matkul" required><br><br>

    <label for="">UTS</label>
    <input type="number" name="nilai_uts" required><br><br>

    <label for="">UAS</label>
    <input type="number" name="nilai_uas" required><br><br>

    <label for="">Tugas</label>
    <input type="number" name="nilai_tugas" required><br><br>

    <input type="submit" value="Simpan">
</form>

<h3>Daftar Nilai Mahasiswa</h3>
<table border="1" cellpadding="2" cellspacing="2" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Lengkap</th>
            <th>Mata Kuliah</th>
            <th>Nilai UTS</th>
            <th>Nilai UAS</th>
            <th>Nilai Tugas</th>
            <th>Nilai Akhir</th>
            <th>Kelulusan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $nomor = 1;
        foreach ($data_mhs as $mhs) {
            echo "<tr>";
            echo "<td>".$nomor."</td>";
            echo "<td>".$mhs->nama."</td>";
            echo "<td>".$mhs->matakuliah."</td>";
            echo "<td>".$mhs->nilai_uts."</td>";
            echo "<td>".$mhs->nilai_uas."</td>";
            echo "<td>".$mhs->nilai_tugas."</td>";
            echo "<td>".number_format($mhs->getNA(), 2)."</td>";
            echo "<td>".$mhs->getKelulusan()."</td>";
            echo "</tr>";
            $nomor++;
        }
        ?>
    </tbody>
</table>

<?php 
class NilaiMahasiswa {
    public $nama;
    public $matakuliah;
    public $nilai_uts;
    public $nilai_uas;
    public $nilai_tugas;
    
    public const PERSENTASE_UTS = 0.25;
    public const PERSENTASE_UAS = 0.30;
    public const PERSENTASE_TUGAS = 0.45;

    public function __construct($nama, $matakuliah, $nilai_uts, $nilai_uas, $nilai_tugas) {
        $this->nama = $nama;
        $this->matakuliah = $matakuliah;
        $this->nilai_uts = $nilai_uts;
        $this->nilai_uas = $nilai_uas;
        $this->nilai_tugas = $nilai_tugas;
    }

    public function getNA() {
        return ($this->nilai_uts * self::PERSENTASE_UTS) +
               ($this->nilai_uas * self::PERSENTASE_UAS) +
               ($this->nilai_tugas * self::PERSENTASE_TUGAS);
    }

    public function getKelulusan() {
        return $this->getNA() >= 60 ? "LULUS" : "DROP OUT";
    }
}
?>