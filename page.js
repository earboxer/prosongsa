/**
 * @brief page.js tries to improve pageload speeds
 * by processing client-side.
 * @author Zach DeCook (zjd7calvin)
 * @date 12, February 2017
 * requires jQuery
 */

/**
 * @brief zj_transpose2 transposes a line
 */
function zj_transpose2( line, transp )
{
	var newchords = zj_transparray( transp );
	var newline = '';
	var space = 0; ///@< Spaces that need to be added or removed.
	for(var i = 0; i < line.length; i++)
	{
		var chari = line[i];
		var nchar = line[i+1];
		var upchar = line[i].toUpperCase();
		var cval = upchar.charCodeAt(0);
		// A-G
		if( cval <= 71 && cval >=65 )
		{
			// Exception for Cmaj7
			if( upchar == 'A' && nchar == 'j' )
			{
				newline += chari;
			}
			else if( nchar == 'b' || nchar =='#')
			{
				i++; //We have read an extra character now.
				var newchord = newchords[upchar + nchar];
				if( newchord.length == 1 )
				{
					// Need to insert an extra space.
					space += 1;
				}
				newline += newchord;
			}
			else
			{
				var newchord = newchords[upchar];
				if( newchord.length == 2 )
				{
					// Need to remove an extra space.
					space -= 1;
				}
				newline += newchord;
			}
		}
		else if ( chari == ' ' )
		{
			if( space >= 0)
			{
				for (var j = 0; j <= space; j++)
				{
					newline += ' ';
				}
				space = 0;
			}
			else
			{
				// Only balance negative spaces if one will be left remaining
				if( nchar == ' ' )
				{
					i++;
					space += 1;
				}
				newline += ' ';
			}
		}
		else
		{
			newline += chari;
		}
	}
	return newline;
}

function zj_transparray( transp )
{
	var chords = 
		["C","C#","D","D#","E","F","F#","G","G#","A","Bb","B" ];
	var newchords = [];
	// Create array to map old chords to new ones
	for (var i=0; i < 12; i++)
	{ 
		newchords[chords[i]] = chords[(i+transp+12)%12];
	}
	newchords["Db"] = newchords["C#"];
	newchords["Eb"] = newchords["D#"];
	newchords["Gb"] = newchords["F#"];
	newchords["Ab"] = newchords["G#"];
	newchords["A#"] = newchords["Bb"];
	return newchords;
}

function transpadd( fromkey, integer )
{
	var chords = array_flip( [ "C","C#","D","D#","E","F","F#","G","G#","A","A#","B" ]);
	chords["Db"] = chords["C#"];
	chords["Eb"] = chords["D#"];
	chords["Gb"] = chords["F#"];
	chords["Ab"] = chords["G#"];
	chords["Bb"] = chords["A#"];	
	var ochords = [ "C","Db","D","Eb","E","F","Gb","G","Ab","A","Bb","B" ];

	return ochords[(parseInt(chords[fromkey]) + integer + 24)%12];
}