<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="container mt-5">
        <h1>Data yang Dikirim</h1>
        <?php
        // Periksa metode pengiriman data (POST/GET)
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Ambil data dari POST
            $nama = isset($_POST['nama']) ? $_POST['nama'] : '';
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $pesan = isset($_POST['pesan']) ? $_POST['pesan'] : '';

            // Simpan data ke dalam array
            $datauser = array(
                "Nama" => $nama,
                "Email" => $email,
                "Pesan" => $pesan
            );

            // Tampilkan data yang dikirim
            echo "<h2>Tampilkan Data yang Dikirim Melalui POST :</h2>";
            echo '<ul class="list-group">';

            foreach ($datauser as $key => $value) {
                if (!empty($value)) {
                    echo '<li class="list-group-item"><strong>' . ucfirst($key) . ':</strong> ' . htmlspecialchars($value) . '</li>';
                } else {
                    echo '<li class="list-group-item"><strong>' . ucfirst($key) . ':</strong> Data Kosong</li>';
                }
            }

            echo '</ul>';
        }
        ?>
    </div>
</body>
</html>