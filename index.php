<?php
require_once('functions.php');
error_reporting(0);

	//in case of url like: http://colorto.me/?colors=abc/def
	if (isset($_GET['colors'])) {
		$colors = trim( $_GET['colors'],'/' );
		header( 'Location:/' . $colors );
		die();
	}

	//let's figure out what we've been given
	$hexes = explode( '/', $_SERVER['REQUEST_URI'] ); //get segment
	$hexes = array_filter( $hexes ); //remove empties
	$hexes = array_merge( $hexes, array() ); //re-key

	$total = 0;
	$last = '';
	
	//assuming we have at least 1 segment...
	if ( count( $hexes) > 0 ) {

		//we can create images if given the right option
		$types = array( 'jpg', 'png', 'gif' );
		$ext = in_array($hexes[0], $types) ? $hexes[0] : false;

		//if we've been told to create an image
		if ( $ext ) {
			//extension is in $ext now, remove from $hexes
			unset( $hexes[0] );

			//assume at least 2 more segments, dimensions, and color value
			//if not, die
			if ( count( $hexes ) < 2 ) {
					//header("Status: 404 Not Found"); //fast-cgi
					header("HTTP/1.0 404 Not Found");
					die('No sir, I don\'t like it.');
			}
			
			//since we've unset the extension, the first should be our dimensions
			//if already numeric, make square with given number
			if ( is_numeric( $hexes[1] ) ) {
				//setup our width and height
				$w = $h = $hexes[1];
				//remove dimensions so that only hex colors are left in $hexes
				unset( $hexes[1] );
			}
			//if not numeric, assume WxH
			elseif ( strpos( $hexes[1], 'x' ) !== false ) {
				//explode by 'x'
				//extract with height
				list( $w, $h ) = explode( 'x', $hexes[1]);

				//remove dimensions so that only hex colors are left in $hexes
				unset( $hexes[1] );

				//we should have a postitive area (WxH)
				if ( ($w * $h) < 1) {
					header("Status: 404 Not Found"); //fast-cgi
					//header("HTTP/1.0 404 Not Found");
					die('No sir, I don\'t like it.');
				}

			}
			//if no dimensions discovered use these
			else {
				$w = 600;
				$h = 450;
			}
			
			//enforce maximum dimensions
			$w = $w > 1000 ? 1000 : $w;
			$h = $h > 1000 ? 1000 : $h;
			
			//sort of a secret feature
			//split columns into rows
			//ex: http://colorto.me/png/400x200/row2/abc/def/431/765

			
			$rows = 1; //default
			
			//basically, if 'row' is in the third segment
			//divide our columns into appropriate number of rows
			//
			if ( strpos( $hexes[2], 'row') !== false ) {
				$rows = intval( str_replace( 'row', '', $hexes[2] ) );
				if ($rows < 1) $rows = 1;
				//remove so that only hex colors are left in $hexes
				unset( $hexes[2] );
			}
			//or, if not 'row', but 'h' make columns horizontal
			//ex: http://colorto.me/png/400x200/h/abc/def/431/765
			elseif ( $hexes[2] == 'h') {
				$rows = 'h';
				//remove so that only hex colors are left in $hexes
				unset( $hexes[2] );
			}
		}//end if($ext)
		
		//if no extension, also no dimensions
		//but double-check and remove
		else if ( strpos( $hexes[0], 'x' ) !== false ) {
			unset($hexes[0]);
		}
		$hexes = array_merge( $hexes, array() ); //re-key since we've probably unset() something
		//make sure all colors are valid
		$hexes = array_map( 'color_clean', $hexes );
		//how many?
		$total = count( $hexes );
		//if opted for rows instead of columns, then the number of rows (previously 'h') should be the total
		$rows = $rows == 'h' ? $total : $rows;

		//use img.php if creating an image
		if ($ext) {
			$imgfile = realpath('img.php');
			require_once($imgfile);
			die();
		}
		//else keep going
		if ($total) {
			$last = $hexes[ ($total-1) ];
			//determine column width, simple stuff
			$width = floor(100/$total).'%';
		}
	}

?><!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>ColorTo.Me</title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="/favicon.gif">

	<link rel="stylesheet" href="/style.css">
	<?php
		//jquery 1.7.1 - breaks it
		//specifically, jquery.color.js
		//haven't investigated further
	?>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
	<script type="text/javascript" src="/js/jquery.color.js"></script>
	<script type="text/javascript" src="/js/jquery.ui.min.js"></script>
	<script type="text/javascript" src="/js/jquery.alphanumeric.pack.js"></script>
	<script type="text/javascript" src="/js/html5slider.js"></script>
	<script type="text/javascript" src="/js/init.dev.js"></script>
</head>
<?php 
//$total is the number of url segments, if less that one, show homepage
if ($total < 1) : ?>
<body>

	<div id="home">
		<h1>ColorTo.me <span>color to me? color tome? I don't know.</span></h1>
		<form method="get" id="colorform">
		<p><input type="text" name="colors" value="000/random/aaa/fff/" size="47" /> <a href="#" id="gobtn">Go</a><!-- <input type="submit" value=" Go " /> --></p>
		<p>Just pass 3 or 6 digit hexadecimal color codes</p>
		<ul>
		<li class="head">Create an image:</li>
		<li><a href="/png/400x300/random/d50/">http://colorto.me<strong>/png/400x300/</strong>random/d50/</a> - png, jpg, gif are accepted, must be first parameter. dimensions follow immediately</li>
		<li><a href="/jpg/300x700/rand/aliceblue/random/">http://colorto.me/jpg/300x700<strong>/rand/aliceblue/random/</strong></a> - 'rand' for a 3 digit hex color, 'random' for 6. color names also accepted</li>
		<li><a href="/gif/200/random/">http://colorto.me/gif<strong>/200/</strong>random/</a> - will be square</li>
		<li><a href="/gif/400/h/234/567/89a/bcd/">http://colorto.me/gif/400<strong>/h/</strong>234/567/89a/bcd</a> - arrange stripes horizontally. must follow dimensions.</li>
		<li><a href="/gif/400/row2/dcb/a98/765/432/">http://colorto.me/gif/400<strong>/row2/</strong>dcb/a98/765/432</a> - show in 2 rows (change number as needed).</li>
		<li>Max dimensions: 1000x1000</li>
		<li class="head">Share a palette:</li>
		<li><a href="/000/1f1/222/3f3/444/5f5/666/7f7/888/9f9/aaa/bfb/ccc/dfd/eee/fff/">http://colorto.me/000/1f1/222/3f3/444/5f5/666/7f7/888/9f9/aaa/bfb/ccc/dfd/eee/fff/</a></li>
		<li><a href="/036/069/09a/0ac/">http://colorto.me/036/069/09a/0ac/</a></li>
		<li><a href="/random/fff/random/000/random/">http://colorto.me<strong>/random/</strong>fff/random/000/random/</a></li>
		</ul>
		</form>
		<p id="credit">By <a href="http://twitter.com/trepmal">@trepmal</a>, and yes, inspired by <a href="http://dummyimage.com/">dummyimage.com</a>.</p>
	</div>

<?php
//otherwise, fanciness!
else : ?>
<body id="palette">

	<div id="colors" style="overflow:hidden;background:#<?php echo $last; ?>">
		<a href="#" id="save">Get share URL</a>
		<a href="#" id="add">Add Color</a>
		<a href="https://github.com/trepmal/colorto.me" id="sourcecode">Get source code</a>
		<?php
			foreach( $hexes as $k => $color ) {
				echo '<div class="col" style="width:'.$width.';background:#'.$color.';">';
			?>
<p class="slider slider_r slider1">
    <span class="range_wrap"><input class="r ch" type="range" min="0" max="255" /></span>
    <input class="ch_val" />
</p>
<p class="slider slider_g">
    <span class="range_wrap"><input class="g ch" type="range" min="0" max="255" /></span>
    <input class="ch_val" />
</p>
<p class="slider slider_b">
    <span class="range_wrap"><input class="b ch" type="range" min="0" max="255" /></span>
    <input class="ch_val" />
</p>
			<?php
				echo '<p class="hex">#<input type="text" value="'.$color.'" /> <a href="#" class="del">&otimes;</a></p>';
				echo '</div>';
			}
	
			echo '<a href="/png/600x450/'. implode('/', $hexes) .'" id="image">Get Image</a>';
		?>
	</div>
<?php endif; ?>

</body>
</html>