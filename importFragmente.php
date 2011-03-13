<?php

require_once('korrekturen.php');

# Cache laden
if(!file_exists('cache')) {
	print "Fehler: Cache existiert nicht! 'make cache' ausgefuehrt?\n";
	exit(1);
}
$cache = unserialize(file_get_contents('cache'));

# Liste der Quellen erzeugen
$sources = array();
foreach($cache['sources'] as $source) {
	if(count($source) >= 2) {
		if(!isset($source['InLit']))
			$source['InLit'] = 'nein';
		if(!isset($source['InFN']))
			$source['InFN'] = 'nein';
		$sources[$source['title']] = $source;
	} else {
		print "%XXX: Ignoriere Quelle: {$source['title']}\n";
	}
}

# Liste der Plagiatskategorien holen
$fragtypes = array();
foreach($cache['fragmenttypes'] as $fragtype) {
	if(isset($fragtype['priority']) && $fragtype['priority'] >= 0) {
		$fragtypes[$fragtype['title']] = $fragtype;
	} else {
		print "%XXX: Ignoriere Plagiatskategorie: {$fragtype['title']}\n";
	}
}


$list = array();
$fragmentTypeUsed = array();
$i = 0;
foreach($cache['fragments'] as $f) {
	$currentSources = array_values(array_intersect($f['categories'],
	                                               array_keys($sources)));
	$currentTypes = array_values(array_intersect($f['categories'],
	                                             array_keys($fragtypes)));

	if(empty($currentSources)) {
		print "%XXX: {$f['wikiTitle']}: Ignoriere, keine Quelle gefunden! (".implode(", ", $f['categories']).")\n";
	} else if(count($currentSources) >= 2) {
		print "%XXX: {$f['wikiTitle']}: Warnung, mehrere Quellen gefunden! (".implode(", ", $f['categories']).")\n";
	}

	if(empty($currentTypes) && empty($f[7])) {
		print "%XXX: {$f['wikiTitle']}: Ignoriere, keinen Plagiatstyp gefunden! (".implode(", ", $f['categories']).")\n";
	} else if(empty($currentTypes)) {
		print "%XXX: {$f['wikiTitle']}: Warnung, keinen Plagiatstyp gefunden! (".implode(", ", $f['categories']).")\n";
		$currentTypes[] = 'Kategorie:'.$f[7];
	} else if(count($currentTypes) >= 2) {
		print "%XXX: {$f['wikiTitle']}: Ignoriere, mehrere Plagiatstypen gefunden! (".implode(", ", $f['categories']).")\n";
	}

	if(empty($currentSources) || count($currentTypes) != 1)
		continue;

	$currentSourceTitle = $currentSources[0];
	$currentTypeTitle = $currentTypes[0];
	$currentTypeCleaned = preg_replace('/^Kategorie:/', '', $currentTypeTitle);

	if($f[7] != $currentTypeCleaned) {
		print "%XXX: {$f['wikiTitle']}: Warnung, Diskrepanz zwischen Fragment und Kategorisierung! (".$f[7]." != ".$currentTypeCleaned.")\n";
	}

	$list[$i]['quelle'] = titleToKey($currentSourceTitle);
	$list[$i]['seite'] = $f[1];
	$list[$i]['zeilen'] = $f[2];
	$list[$i]['plagiat'] = $f[3];
	$list[$i]['seitefund'] = $f[4];
	$list[$i]['zeilenfund'] = $f[5];
	$list[$i]['orig'] = $f[6];
	$list[$i]['anmerkung'] = $f[11];
	$list[$i]['kategorie'] = $currentTypeTitle;
	$list[$i]['inLit'] = $sources[$currentSourceTitle]['InLit'];
	$list[$i]['inFN'] = $sources[$currentSourceTitle]['InFN'];
	$list[$i]['wikiTitle'] = titleToKey($f['wikiTitle']);
	preg_match('/\d+/', $list[$i]['seite'], $m1);
	preg_match('/\d+/', $list[$i]['zeilen'], $m2);
	$sort[$i] = (int)($m1[0]) *1000 + (int)$m2[0];
	$fragmentTypeUsed[$currentTypeTitle] = true;
	$i++;
}

array_multisort($sort, $list);

foreach($fragtypes as $fragtypeTitle => $fragtype) {
	$found = false;
	foreach($list as $l) {
		if($l['kategorie'] === $fragtypeTitle) {
			$found = true;
			break;
		}
	}
	if(!$found)
		continue;

	echo '\subsection{'.$fragtypeTitle."}\n";
	foreach($list as $l) {
		if($l['kategorie'] !== $fragtypeTitle)
			continue;
		$l['seite'] = korrBereich($l['seite']);
		$l['seitefund'] = korrBereich($l['seitefund']);
		$l['zeilen'] = korrBereich($l['zeilen']);
		$l['zeilenfund'] = korrBereich($l['zeilenfund']);
		$l['plagiat'] = korrString($l['plagiat']);
		$l['orig'] = korrString($l['orig']);
		$l['anmerkung'] = korrStringWithLinks($l['anmerkung']);

		if($l['seitefund']) {
			if($l['zeilenfund'])
				$cite = '\cite[S.~'.$l['seitefund'].' Z.~'.$l['zeilenfund'].']';
			else
				$cite = '\cite[S.~'.$l['seitefund'].']';
		} else {
			$cite = '\cite';
		}

		$start = '\belowpdfbookmark{Fragment '.$l['seite'].' '.$l['zeilen'].'}{'.$l['wikiTitle'].'}';

		
		if($l['inLit'] === 'ja')
			$start .= '\fragment{';
		else if($l['inFN'] === 'ja')
			$start .= '\fragmentInFN{';
		else
			$start .= '\fragmentNichtLit{';

		echo $start.$l['seite'].'}{'.$l['zeilen'].'}{'.$l['kategorie'].'}{'.$l['plagiat'].'}{'.$l['orig'].'}{'.$cite.'{'.$l['quelle'].'}}\hypertarget{'.$l['wikiTitle']."}{}\n";
		if($i++ == 20) break;
	}
}

