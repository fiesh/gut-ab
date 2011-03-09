<?php

require_once('korrekturen.php');

function renameAndFix($source)
{
	$renames = array(
		'Autor' => 'author',
		'Hrsg' => 'editor',
		'Titel' => 'title',
		'Verlag' => 'publisher',
		'Zeitschrift' => 'journal',
		'Sammlung' => 'booktitle',
		'Reihe' => 'series',
		'Ort' => 'address',
		'Jahr' => 'year',
		'Monat' => 'month',
		'Tag' => FALSE, // Wird mit in Monat eingebaut
		'Ausgabe' => 'edition',
		'Jahrgang' => 'volume',
		'Nummer' => 'number',
		'Seiten' => 'pages',
		'Schluessel' => 'key',
		'URL' => 'url',
		'ISBN' => 'ISBN',
		'ISSN' => 'ISSN',
		'Anmerkung' => 'note',
		'InLit' => false, // nicht uebernehmen
		'InFN' => false, // nicht uebernehmen
		'title' => false, // Wikititel nicht uebernehmen
	);

	foreach($source as $key => $val) {
		if(in_array($key, array_keys($renames))) {
			if($renames[$key] && $val)
				$ret[$renames[$key]] = $val;
		} else {
			print "Fehler, kann $key nicht uebersetzen! Quelle: {$source['title']}\n";
		}
	}

	// Temporaerer Fix fuer "article"-Eintraege mit "Ausgabe"
	if(isset($ret['journal']) && isset($ret['edition'])) {
		print "WARNUNG: Quelle mit \"Zeitschrift\" und \"Ausgabe\": $categoryname\n";
		print "WARNUNG: Temporaere Ersetzung von \"Ausgabe\" durch \"Nummer\" erfolgt.\n";
		if(isset($ret['number'])) {
			$ret['number'] = $ret['edition'].','.$ret['number'];
		} else {
			$ret['number'] = $ret['edition'];
		}
		unset($ret['edition']);
	}

	// Tag einbauen
	if(isset($source['Tag']) && $source['Tag'])
		$ret['month'] = $source['Tag'].'. '.$ret['month'];

	// Weitere Korrekturen
	$korrs = array(
		'title' => array('korrString', 'korrVersalien', 'korrDash'),
		'booktitle' => array('korrString', 'korrVersalien'),
		'author' => array('korrBracket', 'korrAnd'),
		'editor' => array('korrBracket', 'korrAnd'),
		'publisher' => array('korrAmpersand', 'korrDash'),
		'pages' => array('korrBereich'),
		'note' => array('korrStringWithLinks'),
		'year' => array('korrBracket'),
	);
	foreach($korrs as $key => $korrFunctions) {
		if(isset($ret[$key]))
			foreach($korrFunctions as $korrFunction)
				$ret[$key] = $korrFunction($ret[$key]);
	}

	return $ret;
}


function decideType($f)
{
	if(isset($f['journal']))
		return 'article';

	if(isset($f['booktitle']))
		return 'incollection';

	if(isset($f['publisher']))
		return 'book';

	return 'misc';
}


if(!file_exists('cache')) {
	print "Fehler: Cache existiert nicht! 'make cache' ausgefuehrt?\n";
	exit(1);
}
$cache = unserialize(file_get_contents('cache'));

foreach($cache['sources'] as $source) {
	if(count($source) >= 2) {
		$fields = renameAndFix($source);

		if(!isset($fields['title'])) {
			print 'Fehlender Titel: '.$source['title']."\n";
			continue;
		}

		$type = decideType($fields);

		echo '@'.$type.'{'.titleToKey($source['title']).",\n";
		foreach($fields as $key => $val) {
			echo "	$key = {".$val."},\n";
		}
		echo "}\n";

	} else {
		print 'XXX: Ignoriere Quelle: '.$source['title']."\n";
	}
}
