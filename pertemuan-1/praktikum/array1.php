<?php
// Array buah
$ar_buah = ["Semangka", "Mangga", "Pisang", "Sirsak"];

// Menampilkan buah ke-2 (Indeks 1)
echo "Buah ke-2 adalah: " . $ar_buah[1];

echo "<br>Jumlah array: " . count($ar_buah);

// Menampilkan seluruh buah
echo '<br>Seluruh Buah: <ol>';
foreach ($ar_buah as $buah) {
    echo '<li>' . $buah . '</li>';
}
echo '</ol>';

// Menambahkan buah baru
$ar_buah[] = "Nanas";

// Menghapus buah dengan indeks 1 (Mangga)
unset($ar_buah[1]);

// Menambahkan buah baru di indeks 4
$ar_buah[4] = "Melon";

// Menampilkan array dengan indeksnya
echo '<ul>';
foreach ($ar_buah as $ak => $av) {
    echo '<li>Index: ' . $ak . ' - Nama Buah: ' . $av . '</li>';
}
echo '</ul>';
?>
