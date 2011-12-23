<?php

	//setup the image with given dimentions
	$im = imagecreatetruecolor($w, $h);
	
	//try and determine number of columns we'll need if dividing into rows
	$cols = round($total/$rows, 0, PHP_ROUND_HALF_DOWN);

	//when working with rows, we need to adjust the width of each column
	$width = $wunit = floor( $w/$cols );
	
	//when working with rows, we need to adjust the height of each row
	$height = floor( $h/$rows );
	//since it's not always evenly divisible, we need to allow a little overlap
	//ex: http://colorto.me/png/400/row3/fff/ccc/666/444/red/blue/green
	$hunit = ceil( $h/$rows );

	$i = 1;
	foreach ($hexes as $color) {

		//I don't remember what I was doing here
		//but it's commented, so.....
// 		$color = str_split( $color, 2 ); //separate rr bb gg
// 		$color = array_map( 'ohex', $color ); //prefixes 0x
//  	    $color = imagecolorexact($im, $color[0], $color[1], $color[2]);
// 		if($color==-1) {
// 			//color does not exist; allocate a new one
// 			$color = imagecolorallocate($im, $color[0], $color[1], $color[2]); 
// 		}
		//image friendly color
		$color = '0x00'.$color;

		//add a colored rectangle of the right size
		//offset by w/h on each iteration
		imagefilledrectangle($im, $width-$wunit, $height-$hunit, $w, $h, $color);
		if ($i == $cols) {
			//if new row, adjust reset x- and y-axis starting point (move back to far left, bump down)
			$width = $wunit;
			$height = $height + $hunit;
			$i = 1;		
		}
		else {
			//if new column, only adjust x-axis starting point (move to the right)
			$width = $width + $wunit;
			++$i;
		}
	}

	//spit it out
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
	//destroy it
	imagedestroy( $im );
