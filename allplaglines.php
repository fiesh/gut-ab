<?php

require_once('WikiLoader.php');

function getSourceTypes($sourceTitles) {
	$sourceTypesTitles = WikiLoader::getCategoryMembersTitles('Kategorie:Art der Quelle');
	$sourceTypes = array();
	foreach(WikiLoader::getEntriesByTitles($sourceTitles) as $e) {
		if(!isset($e['categories'])) continue;
		foreach($e['categories'] as $c) {
			$title = $c['title'];
			if(in_array($title, $sourceTypesTitles)) {
				if(!isset($sourceTypes[$title]))
					$sourceTypes[$title] = array();
				$sourceTypes[$title][] = $e['title'];
			}
		}
	}
	ksort($sourceTypes);
	return $sourceTypes;
}

function invertSourceTypes($sourceTypes) {
	$inv = array();
	foreach($sourceTypes as $t => $arr) {
		foreach($arr as $s) {
			$inv[$s][] = $t;
		}
	}
	ksort($inv);
	return $inv;
}

function getTotalLines($fragments) {
	uksort($fragments, 'strnatcmp');

	$lastpage = 0;
	$lastline = 0;
	$total = 0;
	foreach($fragments as $title => $f) {
		if(preg_match('/^Fragment (\d{3}) (\d{2,3})-(\d{2,3})$/', $title, $match)) {
			$wp = (int) $match[1];
			$ws = (int) $match[2];
			$we = (int) $match[3];
		} else if(preg_match('/^Fragment (\d{3}) (\d{2,3})$/', $title, $match)) {
			$wp = (int) $match[1];
			$ws = (int) $match[2];
			$we = (int) $match[2];
		} else {
			continue;
		}
		if ($we < $ws) continue;

		//print "processing: $title\n";
		if($wp == $lastpage && $ws <= $lastline) {
			$ws = $lastline+1;
			//print "overlapping, new ws=$ws\n";
		}
		if($we < $ws) $we = $ws;
		$total += ($we-$ws+1);
		$lastpage = $wp;
		$lastline = $we;
	}
	return $total;
}


$cache = unserialize(file_get_contents('cache'));

$sources = array();
foreach($cache['sources'] as $source) {
	$sources[$source['title']] = $source;
}

$sourceTypes = getSourceTypes(array_keys($sources));
$invSourceTypes = invertSourceTypes($sourceTypes);

$frags = array();
$fragsInCats = array();
foreach($cache['fragments'] as $f) {
	$title = $f['wikiTitle'];

	$found = false;
	if(isset($f['categories']))
		foreach($f['categories'] as $c)
			if(isset($sources[$c])) {
				$found = $c;
				break;
			}

	$frags[$title] = $f;

	if($found === false) {
		print "Keine Quelle gefunden: $title\n";
	} else {
		$fragsInCats[$found][$title] = $f;
		if(isset($invSourceTypes[$found]))
			foreach($invSourceTypes[$found] as $t)
				$fragsInCats[$t][$title] = $f;
	}
}

ksort($fragsInCats);

foreach($frags as $title => $f) {
	if(preg_match('/^Fragment (\d{3}) (\d{2,3})-(\d{2,3})$/', $title, $match)) {
		$wp = (int) $match[1];
		$ws = (int) $match[2];
		$we = (int) $match[3];
	} else if(preg_match('/^Fragment (\d{3}) (\d{2,3})$/', $title, $match)) {
		print "warning: semi-bad fragment title: $title\n";
		$wp = (int) $match[1];
		$ws = (int) $match[2];
		$we = (int) $match[2];
	} else {
		print "bad fragment title: $title\n";
	}
	if(preg_match('/^\d{1,3}$/', $f[1], $match)) {
		$fp = (int) $match[0];
	} else {
		print "bad page number '$f[1]': $title\n";
	}
	if(preg_match('/^\d{1,3}$/', $f[2], $match)) {
		$fs = $fe = (int) $match[0];
	} else if(preg_match('/^(\d{1,3})-(\d{1,3})$/', $f[2], $match)) {
		$fs = (int) $match[1];
		$fe = (int) $match[2];
	} else {
		print "bad line number '$f[2]': $title\n";
	}
	if ($wp != $fp || $ws != $fs || $we != $fe) {
		print "discrepancy: $title  $fp $fs $fe\n";
	}
	if ($we < $ws) {
		print "end less than start: $title\n";
	}
}


print "\n";
print "all\t".getTotalLines($frags)."\n";
print "\n";
foreach(array_keys($sources) as $s) {
	if(!isset($fragsInCats[$s])) continue;
	print "$s\t".getTotalLines($fragsInCats[$s])."\n";
}
print "\n";
foreach(array_keys($sourceTypes) as $t) {
	if(!isset($fragsInCats[$t])) continue;
	print "$t\t".getTotalLines($fragsInCats[$t])."\n";
}
