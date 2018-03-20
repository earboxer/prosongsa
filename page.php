<?php
/**
 * @author: Zach DeCook
 * Facilitates loading of tablature and transposition.
 * Copyright (C) 2017 Zach DeCook
 */

/**
 * @brief Go through the file inputfile.txt and return each song with a link to it.
 */
function toc( $sort ){
	$handle = fopen("inputfile.txt", "r");
	$entries = array();
	$number = 0;
	if ( $handle ){while (($line = fgets($handle)) !== false)
	{
		// Fix weird apostrophe errors
		$line = stripslashes(
			mb_convert_encoding( $line, "HTML-ENTITIES", "UTF-8" )
		);
		$matches = array();
		if( preg_match("(^(X?C?B?\d+)\. )", $line, $matches))
		{
			$number = $matches[1];
			$entries[$number] = array( 'title' => preg_replace("(^(X?C?B?\d+)\. )",'',$line) );
			$entries[$number]['number'] = $number;
		}
		if( preg_match("/^{.?Verse: ?(.*)}/i", $line, $matches)){
			if (isset($entries[$number]['verse'])){
				$entries[$number]['verse'] .= ", $matches[1]";}
			else { $entries[$number]['verse'] = $matches[1];}
		}
		if( preg_match("/^{Key: ?(.*)}/i", $line, $matches)){
			$entries[$number]['key'] = $matches[1];}
		if( preg_match( "/\{p?\d*\((.+m?)\)\}/", $line, $matches) ){
			$entries[$number]['key'] = $matches[1];}

	}fclose($handle);}
	else
	{
		//Error
	}
	$toc = '<form><input type="text" id="toc-filter" placeholder="Filter by song title"/></form>';
	$toc .= '<ul id="toc">';
	if ( $sort ){
		include 'sort.php';
		if ( $sort == 'key' ){
			usort( $entries, 'tocKeysort' );
		}
		else if ( $sort == 'verse' ){
			usort( $entries, 'tocBooksort' );
		}
	}
	foreach( $entries as $item ){
		$toc .= tocentry($item);
	}

	$toc .= '</ul>';
	return $toc;
}

function tocentry( $item ){
	$output = "<li><a href=?song=$item[number]>$item[number]. $item[title]</a>";
	if ( $item['verse'] ){ $output .= "($item[verse])"; }
	if ( $item['key'] ){ $output .= "($item[key])"; }

	return $output;
}

/**
 * @brief load a portion of the text file inputfile.txt starting with
 *  "$number. "
 * Where $number is an integer, or "X12", etc.
 */
function load_song( $number, $transp = 0 )
{
	// Song suggestions are based on the minute you loaded the page
	mt_srand( time()/60 );
	$handle = fopen("inputfile.txt", "r");
	if( ! $transp ) $transp = 0;

	$current_song = 0;
	$something = 0;
	$song = '';
	$textlines = '';///@< for suggesting songs
	$allsongs = array();
	$suggestedSong = array();
	$songKeys = array();

	if ($handle) {while (($line = fgets($handle)) !== false)
	{
		// Fix weird apostrophe errors
		$line = stripslashes(
			mb_convert_encoding( $line, "HTML-ENTITIES", "UTF-8" )
		);

		// If we see a number, then that is what song we are on.
		$matches = array();
		if ( preg_match("(^(X?C?B?\d+)\. )", $line, $matches) )
		{
			$allsongs[$matches[1]] = $line;
			$current_song = $matches[1];
			if( mt_rand(0,100) < 5 )
			{
				$suggestedSong[$current_song]['title'] = $line;
			}
		}

		if ( preg_match( "/\{p?\d*\((.+m?)\)\}/", $line, $matches)
			|| preg_match("/^{Key: ?(.*)}/i", $line, $matches)
		)
		{
			if ( isset( $suggestedSong[$current_song]))
			{
				$suggestedSong[$current_song]['key'] = $matches[1];
			}
			if ( $current_song === $number )
			{
				$songKeys[] = $matches[1];
				$songKey = $matches[1];
			}
		}

		if ( $current_song === $number || $number == "all" )
		{
			if( chordline($line) )
			{
				if ( $transp != 0)
				{
					$line = z_transpose2( $line, $transp );
				}
				$class = ! isset( $songKey ) ? "tabs chord$transp" : "tabs chord" . transpadd( $songKey, $transp );
				$line = str_replace(
					array('{','}'), 
					array('</b>{', "}<b class='$class'>" ),
					$line );

				$song .= "<b class='$class'>" . $line . "</b>";
			}
			else
			{
				$song .= $line;
				$textlines .= $line;
			}
		}
	}fclose($handle);}
	else {
		// error opening the file.
	}

	return
		renderNavButtons( $number )
		. renderEasyTransp( $transp, $number, $songKeys )
		. "<pre>" . $song . "</pre>\n"
		. renderNavButtons( $number )
		. renderSS($suggestedSong, $songKeys, $transp);
}

function renderEasyTransp( $transp, $num, $songKeys = array() )
{
	$s = '';
	//up two semitones
	$classT = 'btn col-xs-12';
	$nsongKey = 'Z';
	$words = "Transposed up 2 semitones";
	if (isset ( $songKeys[0] ) )
	{
		$classT = 'btn col-xs-4';
		$origKey = $songKeys[0];
		$pastKey = transpadd( $origKey, $transp - 2 );
		$presentKey = transpadd( $origKey, $transp );
		$s .= getKeyButton( "Transpose down to", $transp - 2, $pastKey, $num, $classT );
		$s .= getKeyButton( "Current Key: ", $transp, $presentKey, $num, $classT );
		$nsongKey = transpadd( $presentKey, 2) ?: 'Z';
		$words = "Transpose up to ";
	}
	$s .= getKeyButton( $words, $transp + 2, $nsongKey, $num, $classT );

	return $s;
}

function getKeyButton( $text, $value, $key, $num, $classT )
{
	$zkey = $key ?: 'Z';
	$key = ($key == 'Z') ? '' : $key;
	return "<a href='?song=$num&transp=$value' class='$classT btn-$zkey' data-key='$key' data-words='$text '>"
		. "$text $key</a>";
}

function renderNavButtons( $number )
{
	$navbuttons = '';
	if ( is_numeric($number) )
	{
		$pnumber = $number - 1;
		$nnumber = $number + 1;
		$navbuttons .= "<a href='?song=$pnumber' class='btn btn-Z col-xs-6'>previous</a>\n";
		$navbuttons .= "<a href='?song=$nnumber' class='btn btn-Z col-xs-6'>next</a>\n";
	}
	return $navbuttons;
}

/**
 * @brief Show the suggested songs as buttons with transpositions.
 * @param $suggestedSong array of songs '25' => ['title'=> 'Majesty', 'key' => 'Bb' ]
 * @param $songKeys Keys that you want to transpose these songs into.
 */
function renderSS( $suggestedSong = array(), $songKeys, $transp )
{
	$ss = array();
	$bs = array();// "bad" songs. Songs without transpositions.
	//shuffle( $suggestedSong );
	foreach ($suggestedSong as $songNum => $songarray) {
		if( isset($songarray['key']) )
		{
			$ok = trim($songarray['key'], 'm');
			$n = 2;
			$bn = 12-($n* count($songKeys));
			$smn = count($songKeys) < 3 ? 3 : 12/count($songKeys);
			$smbn = count($songKeys) < 3 ? 12 - count($songKeys) * $smn : 12;
			$xsn = count($songKeys) == 1 ? 4 : 12/(count($songKeys) ?: 1);
			$xsbn = count($songKeys) == 1? 8 : 12;
			//$xtn = 12/count($songKeys);
			//$xtbn = 12;
			$s = "<a href='?song=$songNum' class='btn btn-$ok col-md-$bn col-sm-$smbn col-xs-$xsbn'>"
				. "$songarray[title] ($songarray[key])</a>\n";
			$classT = "btn col-md-$n col-sm-$smn col-xs-$xsn";
			foreach( $songKeys as $sK )
			{
				$tt = difftransp( $sK, $songarray['key']);
				if ( is_integer($tt) );
				{
					$tt = ($tt + $transp + 12)%12;
					$nsongKey = transpadd( $sK, $transp );
					
					$s .= "\t<a href='?song=$songNum&transp=$tt' class='$classT btn-$nsongKey' data-key='$nsongKey' "
						. "data-words='Transposed to '>"
						. "Transposed to $nsongKey</a>\n";
				}
			}
			$ss[] = $s;
		}
		else
		{
			$bs[] = "<a href='?song=$songNum' class='btn btn-Z col-xs-12'>$songarray[title]</a>\n";
		}
	}
	shuffle( $ss ); shuffle( $bs );
	return implode($ss) . implode($bs);
}

/**
 * @brief Determine whether or not this line contains chords.
 */
function chordline($line)
{
	$chords = 
		array( "C","C#","D","D#","E","F","F#","G","G#","A#","B",//"A",
		"Db", "Eb", "Gb", "Bb",//"Ab",
		"Cm", "Dm", "Fm", "Gm", "Bm",  //"Em", "Am",
		);
	$ambiguous = array( "Ab", "Em", "Am", "A" );
	$line = str_replace(array('/','7', "\n", '2', '4'), ' ', $line);
	$tokens = array_filter(explode(' ', $line));

	$badtokens = 0;
	$ambtokens = 0;
	foreach ($tokens as $token) {
		if( in_array( substr($token, 0,2), $chords ) ) return TRUE;
		else if ( in_array( substr( $token, 0,2), $ambiguous) ) $ambtokens++;
		else if( $badtokens > 10 ) return FALSE;
		else $badtokens++;
	}
	return $ambtokens >= $badtokens;
}

function normalizechords($line, $space=TRUE)
{
	// Get rid of flats
	$old = array( "Db", "Eb", "Gb", "Ab", "Bb");
	$new = array( "C#", "D#", "F#", "G#", "A#");
	$line =	str_replace($old, $new, $line);

	// Uppercase letters A-G
	$lc = array( "a", "b", "c", "d", "e", "f", "g");
	$uc = array( "A", "B", "C", "D", "E", "F", "G");
	$line = str_replace($lc, $uc, $line);

	if ( $space == TRUE )
	{
		// Move space for nonsharp chords that didn't end in one
		$line = preg_replace("( ([A-G])([^# ]))", "$1 $2", $line);

		// Trailing space at the end of the line
		$line = str_replace("\n", " \n", $line );
	}

	return $line;
}

function z_transpose2( $line, $transp )
{
	$newchords = z_transparray( $transp );
	$newline = '';
	$space = 0; ///@< Spaces that need to be added or removed.
	for($i = 0; $i < strlen($line); $i++)
	{
		$char = $line[$i];
		$nchar = isset($line[$i+1]) ? $line[$i+1] : '';
		$upchar = strtoupper($line[$i]);
		$cval = ord($upchar);
		// A-G
		if( $cval <= 71 && $cval >=65 )
		{
			// Exception for Cmaj7
			if( $upchar == 'A' && $nchar == 'j' )
			{
				$newline .= $char;
			}
			else if( $nchar == 'b' || $nchar =='#')
			{
				$i++; //We have read an extra character now.
				$newchord = $newchords[$upchar . $nchar];
				if( strlen($newchord) == 1 )
				{
					// Need to insert an extra space.
					$space += 1;
				}
				$newline .= $newchord;
			}
			else
			{
				$newchord = $newchords[$upchar];
				if( strlen($newchord) == 2 )
				{
					// Need to remove an extra space.
					$space -= 1;
				}
				$newline .= $newchord;
			}
		}
		else if ( $char == ' ' )
		{
			if( $space >= 0)
			{
				for ($j = 0; $j <= $space; $j++)
				{
					$newline .= ' ';
				}
				$space = 0;
			}
			else
			{
				// Only balance negative spaces if one will be left remaining
				if( $nchar == ' ' )
				{
					$i++;
					$space += 1;
				}
				$newline .= ' ';
			}
		}
		else
		{
			$newline .= $char;
		}
	}
	return $newline;
}

function z_transparray( $transp )
{
	$chords = 
		array( "C","C#","D","D#","E","F","F#","G","G#","A","Bb","B" );
	$newchords = array();
	// Create array to map old chords to new ones
	for ($i=0; $i < 12; $i++)
	{ 
		$newchords[$chords[$i]] = $chords[($i+$transp+12)%12];
	}
	$newchords["Db"] = $newchords["C#"];
	$newchords["Eb"] = $newchords["D#"];
	$newchords["Gb"] = $newchords["F#"];
	$newchords["Ab"] = $newchords["G#"];
	$newchords["A#"] = $newchords["Bb"];
	return $newchords;
}

function difftransp( $fromkey, $tokey )
{
	$chords = array_flip(array( "C","C#","D","D#","E","F","F#","G","G#","A","A#","B" ));
	$chords["Db"] = $chords["C#"];
	$chords["Eb"] = $chords["D#"];
	$chords["Gb"] = $chords["F#"];
	$chords["Ab"] = $chords["G#"];
	$chords["Bb"] = $chords["A#"];
	$fromkey = trim($fromkey, 'm');
	$tokey = trim($tokey, 'm');
	return $chords[$fromkey] - $chords[$tokey];
}

function transpadd( $fromkey, $integer )
{
	$chords = array_flip(array( "C","C#","D","D#","E","F","F#","G","G#","A","A#","B" ));
	$chords["Db"] = $chords["C#"];
	$chords["Eb"] = $chords["D#"];
	$chords["Gb"] = $chords["F#"];
	$chords["Ab"] = $chords["G#"];
	$chords["Bb"] = $chords["A#"];
	$ochords = array( "C","Db","D","Eb","E","F","Gb","G","Ab","A","Bb","B" );
	$fromkey = trim($fromkey, 'm');
	return $ochords[($chords[$fromkey] + $integer + 24)%12];
}
