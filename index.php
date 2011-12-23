<?php
require_once('functions.php');
error_reporting(0);

	if (isset($_GET['colors'])) {
		$colors = trim( $_GET['colors'],'/' );
		header( 'Location:/' . $colors );
		die();
	}

	$hexes = explode( '/', $_SERVER['REQUEST_URI'] );
	$hexes = array_filter( $hexes );
	$hexes = array_merge( $hexes, array() );

	$total = 0;
	$last = '';
	if ( count( $hexes) > 0 ) {

		$types = array( 'jpg', 'png', 'gif' );
		$ext = in_array($hexes[0], $types) ? $hexes[0] : false;

		if ( $ext ) {
			unset( $hexes[0] );
			
			if ( count( $hexes ) < 2 ) {
					//header("Status: 404 Not Found"); //fast-cgi
					header("HTTP/1.0 404 Not Found");
					die();
			}
			if ( is_numeric( $hexes[1] ) ) {
				$w = $hexes[1];
				$h = $hexes[1];
				unset( $hexes[1] );
			}
			elseif ( strpos( $hexes[1], 'x' ) !== false ) {
				$dims = explode( 'x', $hexes[1]);
				$w = intval( $dims[0] );
				$h = intval( $dims[1] );
				unset( $hexes[1] );

				if ( ($w * $h) < 1) {
					//header("Status: 404 Not Found"); //fast-cgi
					header("HTTP/1.0 404 Not Found");
					die();
				}

			}
			else {
				$w = 600;
				$h = 450;
			}
			
			$w = $w > 1000 ? 1000 : $w;
			$h = $h > 1000 ? 1000 : $h;
			
			$rows = 1;
			if ( strpos( $hexes[2], 'row') !== false ) {
				$rows = intval( str_replace( 'row', '', $hexes[2] ) );
				if ($rows < 1) $rows = 1;
				unset( $hexes[2] );
			}
			elseif ( $hexes[2] == 'h') {
				$rows = 'h';
				unset( $hexes[2] );
			}
		}
		else if ( strpos( $hexes[0], 'x' ) !== false ) {
			unset($hexes[0]);
		}
		$hexes = array_merge( $hexes, array() );
		$hexes = array_map( 'color_clean', $hexes );
		$total = count( $hexes );
		$rows = $rows == 'h' ? $total : $rows;

		if ($ext) {
			$imgfile = realpath('img.php');
			require_once($imgfile);
			die();
		}
		if ($total) {
			$last = $hexes[ ($total-1) ];
			$width = round(100/$total, PHP_ROUND_HALF_DOWN).'%';
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

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
	<script type="text/javascript" src="/js/jquery.color.js"></script>
	<script type="text/javascript" src="/js/jquery.ui.min.js"></script>
	<script type="text/javascript" src="/js/jquery.alphanumeric.pack.js"></script>
	<script type="text/javascript" src="/js/html5slider.js"></script>
	<script type="text/javascript" src="/js/init.dev.js"></script>

</head>
<?php if ($total < 1) : ?>
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

<?php else : ?>
<body id="palette">

	<div id="colors" style="overflow:hidden;background:#<?php echo $last; ?>">
		<a href="#" id="save">Get share URL</a>
		<a href="#" id="add">Add Color</a>
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