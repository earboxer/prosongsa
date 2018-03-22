<?php
$theme = $_COOKIE['theme'] ?? 'light';
if ( isset( $_GET['theme'] ) ){
	$theme = $_GET['theme'];
	setcookie( 'theme', $theme, time()+60*60*24*30 );
}
?>
<!--
index.php contains the main html used for creating the page.
Author: Zach DeCook
-->
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet"
			href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
			integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"
			crossorigin="anonymous">

		<link rel="stylesheet" href="index.css" >
		<link rel="stylesheet"
			href=
			<?php
			if ( $theme == 'dark' ){echo "'theme-dark.css'";}
			else {echo "'theme.css'";}
			?> >
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=.65">
		<title>Choruses and Hymns</title>
	</head>

<body class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">

<header>

	<div class = "col-xs-6 col-xs-offset-0 ">
		<h3>Prosongsa</h3>
		Theme:
		<?php
		$query = preg_replace( '/&?theme=\w+/', '', $_SERVER['QUERY_STRING'] );
		echo "<a class='lightbubble' href='?$query&theme=light'>&#x25cf;</a>
			<a class='darkbubble' href='?$query&theme=dark'>&#x25cf;</a>";
		?>
		(Uses cookies :)
		<br>
		<a href="?song=0">Table of contents</a>
		<form>
			<input name='song' type='number' value='<?php echo isset($_GET['song']) ? $_GET['song'] : '' ?>'
			min='0' max='169'
			/>
			<input class="btn btn-Z" type="submit" value="Jump to song" />
		</form>
	</div>

	<div class = "col-xs-6 col-xs-offset-0 ">
		<div id="chordarea">
			<canvas id='chordy' width="100" height="100"/>
		</div>
		<i>Experimental: Click on a chord to view guitar tablature</i>
		<div id="messages"></div>
	</div>

</header>

<div class = "text-center">
	<form>
	<?php $transp = isset( $_GET['transp']) ? (int)$_GET['transp'] : 0 ?>
	<select name="transp" id="transp"
		value = "<?php echo $transp;?>"
	>
		<?php
			for ($i=-6; $i < 12; $i++) {
				if (($transp + 24)%12 == $i) $selected = 'selected';
				else $selected = '';
				//$dir = ($i >= 0 ? "up" : "down" );
				$dir = "transpose";
				echo "<option value='$i' $selected>$dir $i semitones</option>";
			}
		?>
	</select>
	<noscript>
		<button>Transpose</button>
	</noscript>
	</form>
</div>


<div>

<?php
include 'page.php';

	$song_number = isset( $_GET['song'] ) ? $_GET['song'] : '';
	if( ! $song_number )
	{
		$sort = '';
		if ( isset( $_GET['sort'] ) ) $sort = $_GET['sort'];
		echo toc($sort);
	}
	else
	{
		echo load_song( $song_number, ($transp + 24)%12 );
	}

?>
</div>

<footer>
All songs are owned by their respective copyright holders. No infringement intended.
<br>
Powered by <a href='https://github.com/earboxer/prosongsa'>Prosongsa</a>.
Suggest features <a href='https://github.com/earboxer/prosongsa/issues'>here</a>.
<br>
Prosongsa software licensed under the <a href='LICENSE'>GNU AGPLv3</a>.
View source <a href='source.php'>here</a>.
<br>
Using <a href='https://github.com/earboxer/chordsdata'>chordsdata</a>,
licensed under the <a href='chordsdata/LICENSE'>GNU LGPLv3</a>.
</footer>

<script   src="https://code.jquery.com/jquery-1.12.4.min.js"
integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
crossorigin="anonymous"></script>
<script src="toc-filter.js"></script>
<script type="text/javascript" src="page.js"></script>
<script type="text/javascript" src="jsonly.js"></script>
<script type="text/javascript" src="ccharter/scripts/ccharter.js"></script>
<script type="text/javascript" src="chordsdata/chords.js"></script>
  </body>
</html>
