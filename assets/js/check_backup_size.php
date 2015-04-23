<?php

function pr2file($a) {
    $file = 'log.txt';
    $current = file_get_contents($file);
    file_put_contents($file, $current.date('m.d.y H:i:s')."\n".print_r($a, true)."\n\n\n");
}

pr2file(array($_GET['path'], $_GET['size']));

if ( file_exists($_GET['path']) ) {

    $current = bcdiv( filesize($_GET['path']), 1048576, 2);
	$width = round( ( $current /  $_GET['size'] ) * 100 );

} else {

    $width = 0;

}


?>

<div class="progress"><?php echo $width.'%'; ?></div>
<div class="bar" style="width: <?php echo $width; ?>%;"></div>