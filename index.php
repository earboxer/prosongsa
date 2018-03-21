<!--
index.php contains the main html used for creating the page.
-->
<!DOCTYPE html>
<html>
	<head>
		<!-- Latest compiled and minified CSS -->
		<!--<link rel="stylesheet" href="../shared/bootstrap-3.3.6/css/bootstrap.min.css"
			integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"
			crossorigin="anonymous"> -->
		<link rel="stylesheet"
			href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

		<link rel="stylesheet" href="index.css" >
		<meta  http-equiv="Content-Type" content="text/html;  charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=.65">
		<title>Choruses and Hymns</title>
	</head>

  <p><body>

<!-- Light yellow box around page contents -->
<div class="col-lg-offset-2 col-lg-8 col-md-offset-1 col-md-10 col-sm-12 col-xs-12 bg-success">

	<div class = "col-xs-6 col-xs-offset-0 ">
		<h3>Browse Songs</h3>
		<a href="?song=0">Table of contents</a>
		<form>
			<input name='song' type='number' value='<?php echo isset($_GET['song']) ? $_GET['song'] : '' ?>'
			min='0' max='169'
			/>
 			<input type="submit" value="Jump to song" />
		</form>
	</div>

	<div class = "col-xs-6 col-xs-offset-0 ">
		<div id="chordarea">
			<canvas id='chordy' width="100" height="100"/>
		</div>
		<i>Experimental: Click on a chord to view guitar tablature</i>
		<div id="messages"></div>
	</div>

	<div class = "col-sm-0 col-md-2 col-lg-2"><p></p></div>
	<div class = "col-lg-12 col-md-12 col-sm-12 col-xs-12"><p></p></div>

	<br></br>
</div>
<div class = "text-center col-lg-offset-2 col-lg-8 col-md-offset-1 col-md-10 col-sm-12 col-xs-12 bg-info ">
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
	<?php
		//$val1 = ($_GET['transp'] - 1 + 12)%12;
		$val2 = ($transp + 2)%12;
		//$val3 = ($_GET['transp'] + 3)%12;
		//echo "<input type='submit' value='$val1' name='transp'>";
		echo "<input type='submit' value='$val2' name='transp'>";
		//echo "<input type='submit' value='$val3' name='transp'>";
	?>
	</noscript>
	</form>
</div>


<div class="col-lg-offset-2 col-lg-8 col-md-offset-1 col-md-10 col-sm-12 col-xs-12 bg-info">

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
<br/><br><br>
All songs are owned by their respective copyright holders. No infringement intended.
<br>
Powered by <a href='https://github.com/earboxer/prosongsa'>Prosongsa</a>.
Suggest features <a href='https://github.com/earboxer/prosongsa/issues'>here</a>.
<br>
Licensed under the <a href='LICENSE'>GNU AGPLv3</a>. View source <a href='source.php'>here</a>.
</div>

<!-- <script type="text/javascript" src="../scripts/jqm.js"></script> -->
<script   src="https://code.jquery.com/jquery-1.12.4.min.js"
integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
crossorigin="anonymous"></script>
<script src="toc-filter.js"></script>
<script type="text/javascript" src="page.js"></script>
<script type="text/javascript" src="jsonly.js"></script>
<script type="text/javascript" src="ccharter/scripts/ccharter.js"></script>
<script type="text/javascript" src="data/chords.js"></script>
  </body>
</html>
