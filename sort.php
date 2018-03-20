<?php

function tocKeysort( $a, $b ){
	$aKey = $a['key'] ?: 'H';
	$bKey = $b['key'] ?: 'H';
	return ord($aKey[0]) - ord($bKey[0]);
}


function tocBooksort( $a, $b ){
$bookOrder = array(
'Genesis',
'Exodus',
'Numbers',
'Deuteronomy',
'Joshua',
'I Samuel',
'I Chronicles',
'II Chronicles',
'Psalm',
'Proverbs',
'Isaiah',
'Jeremiah',
'Lamentations',
'Micah',
'Habakkuk',
'Zephaniah',
'Zepheniah',
'Matthew',
'Matt.',
'John',
'Romans',
'I Cor.',
'II Corinthians',
'Galatians',
'Ephesians',
'Philippians',
'Phil.',
'I Thessalonians',
'II Timothy',
'Hebrews',
'James',
'Psalm',
'I Peter',
'I John',
'Jude',
'Revelation',
'Revelations',
'Rev.',
);
	$aVerse = $a['verse'] ?: '';
	$bVerse = $b['verse'] ?: '';
	$matches = array();
	$aBookKey = 3000;
	$bBookKey = 3000;
	if ( preg_match( '/^[0-9i]*\ ?[A-Z.]+/i', $aVerse, $matches ) ){
		$aBook = $matches[0];
		$aBookKey = array_search( $aBook, $bookOrder );
	}
	if ( preg_match( '/^[0-9i]*\ ?[A-Z.]+/i', $bVerse, $matches ) ){
		$bBook = $matches[0];
		$bBookKey = array_search( $bBook, $bookOrder );
	}

	return $aBookKey - $bBookKey;
}