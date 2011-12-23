<?php

	$im = imagecreatetruecolor($w, $h);

	$cols = round($total/$rows, 0, PHP_ROUND_HALF_DOWN);

	//$total = count($hexes);
	$width = round($w/$cols, 0, PHP_ROUND_HALF_DOWN);
	$wunit = round($w/$cols, 0, PHP_ROUND_HALF_DOWN);
	
	$height = round($h/$rows, 0, PHP_ROUND_HALF_EVEN);
	$hunit = round($h/$rows, 0, PHP_ROUND_HALF_UP);

	$i = 1;
	foreach ($hexes as $color) {

// 		$color = str_split( $color, 2 ); //separate rr bb gg
// 		$color = array_map( 'ohex', $color ); //prefixes 0x
//  	    $color = imagecolorexact($im, $color[0], $color[1], $color[2]);
// 		if($color==-1) {
// 			//color does not exist; allocate a new one
// 			$color = imagecolorallocate($im, $color[0], $color[1], $color[2]); 
// 		}
		$color = '0x00'.$color;
		imagefilledrectangle($im, $width-$wunit, $height-$hunit, $w, $h, $color);
		if ($i == $cols) {
			$width = $wunit;
			$height = $height + $hunit;
			$i = 1;		
		}
		else {
			$width = $width + $wunit;
			++$i;
		}
	}

	//**
	header('Content-type: image/'.$ext);
	header('Content-Disposition: filename="swatch.'.$ext.'"');
	switch ($ext) {
		case 'png' :
			imagepng($im);
		break;
		case 'jpg' :
			imagejpeg($im);
		break;
		case 'gif' :
			imagegif($im);
		break;
	}
	/**/

	imagedestroy( $im );
