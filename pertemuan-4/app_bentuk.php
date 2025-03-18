<?php
 require_once 'lingkaran.php'; /**ini untuk memanggil file */

 $lingkaran1 = new Lingkaran(8.4); /**ini objek $lingkaran1/lingkaran2 */
 $lingkaran2 = new Lingkaran(14);

 echo 'NILAI PHI adalah'. Lingkaran::PHI;/**phi disini konstanta */
 echo '<br>Jari-jari lingkaran 1'. $lingkaran1->jari;
 echo '<br>Luas lingkaran 1'. $lingkaran1->getLuas();/**ini method */
 echo '<br>Luas lingkaran 1'. $lingkaran1->getKeliling();/**ini juga method */
 echo '<br>'/**ini untuk spasi */

?>