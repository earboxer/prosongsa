/**
 * @brief jsonly.js contains the functions that are only called in js,
 * not translated from server-side code.
 */

$('#transp').change( do_transpose );
var lastTransp = parseInt($('#transp').val());

var guitarObj = {
	name: 'guitar',
	dict: chordsDict,
	region: 'guitarregion',
	chord: '',
	chordd: '',
	number: 0,
}
var ukeObj = {
	name: 'ukulele',
	dict: ukulelechordsDict,
	region: 'ukuleleregion',
	chord: '',
	chordd: '',
	number: 0,
}

function do_transpose()
{
	var transp = parseInt($('#transp').val());
	var neww = transp;

	$(".tabs").each(function(){
		// Since we only store text that is displayed, transpose relative to 
		// previous transposition.
		// Requires the class to be "tabs chordXX";
		var old = parseInt($(this).attr('class').substring(10));
		if ( isNaN(old) )
		{
			old = $(this).attr('class').substring(10);
			neww = transpadd(old, transp - lastTransp);
		}
		$(this).text( zj_transpose2( $(this).text(), transp - lastTransp ) );
		$(this).removeClass("chord"+old);
		$(this).addClass("chord"+neww);
	});
	$(".btn[data-key]").each(function(){
		console.log($(this));
		var oldKey = $(this).attr('data-key');
		var newKey = transpadd(oldKey, transp - lastTransp);
		if ( typeof newKey !== 'undefined' )
		{
			$(this).removeClass('btn-'+oldKey);
			$(this).addClass('btn-'+newKey);
			$(this).attr('data-key', newKey);
			$(this).text( $(this).attr('data-words') + newKey );
		}
		var tt = parseInt($(this).attr('href').match(/transp=(.+)/)[1]);
		tt = ( transp - lastTransp + 24 + tt)%12;
		var newhref = $(this).attr('href').match(/(.*?&transp=).+/)[1] + tt;
		$(this).attr('href', newhref);
	})
	lastTransp = transp;
}

$(".tabs").click(function(e) {
    s = window.getSelection();
    var range = s.getRangeAt(0);
    var node = s.anchorNode;
    while (range.toString().indexOf(' ') != 0 ) {
		if (range.startOffset == 0)
			break;
        range.setStart(node, (range.startOffset - 1));
    }
    range.setStart(node, Math.max(0,range.startOffset));
	while (range.toString().lastIndexOf(' ') != range.toString().length - 1
		|| range.toString().length <= 1 )
	{
		range.setEnd(node, range.endOffset+1);
		if ( range.endOffset == node.length)
			break;
    }
	//range.setEnd(Math.min(range.endOffset+1,node.length);
    var str = range.toString().trim();
	if( str != "" )
		show_tab(str);
});

function show_tab( chord )
{
	var canvas = $("#guitarregion")[0];
	var context = canvas.getContext('2d');
	context.clearRect(0, 0, canvas.width, canvas.height);
	var canvas = $("#ukuleleregion")[0];
	var context = canvas.getContext('2d');
	context.clearRect(0, 0, canvas.width, canvas.height);

	getChordFrets(chord);
}

function getChordFrets(chord)
{
	$("#messages").html("");
	chord = chord.replace("(", "");
	chord = chord.replace(")", "");
	chord = chord.replace("sus", "s");
	chord = chord.replace("s4", "s");
	chord = chord.replace("s", "sus");
	chord = chord.replace("7sus", "sus7");
	chord = chord.replace("mj7", "maj7");
	var chordd = chord;
	chordd = chordd.replace("/A","/a");
	chordd = chordd.replace("/B","/b");
	chordd = chordd.replace("/C","/c");
	chordd = chordd.replace("/D","/d");
	chordd = chordd.replace("/E","/e");
	chordd = chordd.replace("/F","/f");
	chordd = chordd.replace("/G","/g");
	chordd = chordd.replace("Db", "C#");
	chordd = chordd.replace("Eb", "D#");
	chordd = chordd.replace("Gb", "F#");
	chordd = chordd.replace("Ab", "G#");
	chordd = chordd.replace("Bb", "A#");

	plotChord(chord,chordd,guitarObj)
	plotChord(chord,chordd,ukeObj)
}

function plotChord(chord, chordd, obj){
	try{
		tab = obj.dict[chordd][0];
		ChordCharter.drawChord( obj.region, 30, 25, chord, tab );
		obj.chord = chord;
		obj.chordd = chordd;
		obj.number = 0;
	}
	catch(target){
		$("#messages").prepend("No "+obj.name+" tab for '" + chord + "'. ");

		// Try it without the base note.
		if( chordd.replace(/\/.*/,"") != chordd)
		{
			plotChord( chord.replace(/\/.*/,""), chordd.replace(/\/.*/,""), obj);
		}
	}

}

function cycle(obj){
	var canvas = $("#"+obj.region)[0];
	var context = canvas.getContext('2d');
	context.clearRect(0, 0, canvas.width, canvas.height);

	obj.number = (obj.number + 1) % obj.dict[obj.chordd].length
	tab = obj.dict[obj.chordd][obj.number]
	ChordCharter.drawChord( obj.region, 30, 25, obj.chord +"("+obj.number + ")", tab );
}
