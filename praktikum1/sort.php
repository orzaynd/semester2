<?php
$ar_buah = ["a"=>"Apel", "m"=>"Mangga", "s"=>"Semangka", "n"=>"Nanas"];
       echo '<ol>';
       sort($ar_buah);
       echo '<hr/>';
       echo '</ol>';
       foreach ($ar_buah as $key => $value) {
        echo '<li>'.$buah.'</li>';
       }
       echo'<ol>';
?>