<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penilaian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h1 class="text-xl font-bold mb-4">Sistem Penilaian</h1>
            <h2 class="text-lg font-semibold mb-6">Form Nilai Siswa</h2>
            <form action="output.php" method="POST">
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <label class="text-right font-semibold">Nama Lengkap</label>
                    <input type="text" name="nama" class="col-span-2 border rounded p-2" placeholder="Nama Lengkap" required>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <label class="text-right font-semibold">Mata Kuliah</label>
                    <select name="matkul" class="col-span-2 border rounded p-2" required>
                        <option value="Dasar Dasar Pemrograman">Dasar Dasar Pemrograman</option>
                        <option value="Pemrograman Web">Pemrograman Web</option>
                        <option value="UI/UX">UI/UX</option>
                        <option value="Basis Data">Basis Data</option>
                    </select>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <label class="text-right font-semibold">Nilai UTS</label>
                    <input type="text" name="uts" class="col-span-2 border rounded p-2" placeholder="Nilai UTS" required>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <label class="text-right font-semibold">Nilai UAS</label>
                    <input type="text" name="uas" class="col-span-2 border rounded p-2" placeholder="Nilai UAS" required>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <label class="text-right font-semibold">Nilai Tugas/Praktikum</label>
                    <input type="text" name="tugas" class="col-span-2 border rounded p-2" placeholder="Nilai Tugas" required>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div></div>
                    <button type="submit" class="col-span-2 bg-blue-500 text-white rounded p-2">Simpan</button>
                </div>
            </form>
        </div>
        <div class="text-center mt-4 text-gray-600">
            By @oryza
        </div>
    </div>
</body>
</html>