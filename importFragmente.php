<?php

require_once('korrekturen.php');

# Cache laden
if(!file_exists('cache')) {
	print "Fehler: Cache existiert nicht! 'make cache' ausgefuehrt?\n";
	exit(1);
}
$cache = unserialize(file_get_contents('cache'));

# Liste ignorierter Fragmente/Plagiatskategorien/Quellen anzeigen
foreach($cache['ignored']['fragments'] as $title) {
	print "%XXX: Ignoriere Fragment: $title\n";
}
foreach($cache['ignored']['fragmenttypes'] as $title) {
	print "%XXX: Ignoriere Plagiatskategorie: $title\n";
}
foreach($cache['ignored']['sources'] as $title) {
	print "%XXX: Ignoriere Quelle: $title\n";
}

# Liste der Quellen erzeugen
$sources = array();
foreach($cache['sources'] as $source) {
	if(!isset($source['InLit']))
		$source['InLit'] = 'nein';
	if(!isset($source['InFN']))
		$source['InFN'] = 'nein';
	$sources[$source['title']] = $source;
}

# Liste der Plagiatskategorien holen
$fragtypes = array();
foreach($cache['fragmenttypes'] as $fragtype) {
	$fragtypes[$fragtype['title']] = $fragtype;
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
	if($f[8] != $sources[$currentSourceTitle]['InLit']) {
		print "%XXX: {$f['wikiTitle']}: Warnung, Diskrepanz zwischen InLit in Fragment und Quelle! (".$f[8]." != ".$sources[$currentSourceTitle]['InLit'].")\n";
	}
	$list[$i]['inFN'] = $sources[$currentSourceTitle]['InFN'];

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

	print '\section{'.$fragtypeTitle."}\n";
	foreach($list as $l) {
		if($l['kategorie'] !== $fragtypeTitle)
			continue;
		$l['seite'] = korrBereich($l['seite']);
		$l['seitefund'] = korrBereich($l['seitefund']);
		$l['zeilen'] = korrBereich($l['zeilen']);
		$l['zeilenfund'] = korrBereich($l['zeilenfund']);
		$l['plagiat'] = replaceIfEmpty(korrStringWithLinks($l['plagiat']), '---');
		$l['orig'] = replaceIfEmpty(korrStringWithLinks($l['orig']), '---');
		$l['anmerkung'] = replaceIfEmpty(korrStringWithLinks($l['anmerkung']), '');

		if($l['seitefund']) {
			if($l['zeilenfund'])
				$cite = '\cite[S.~'.$l['seitefund'].' Z.~'.$l['zeilenfund'].']';
			else
				$cite = '\cite[S.~'.$l['seitefund'].']';
		} else {
			$cite = '\cite';
		}

		if($l['inLit'] === 'ja') {
			$citedInDiss = '';
		} else if($l['inFN'] === 'ja') {
			$citedInDiss = ' (Nur in Fu\ss{}note, aber \emph{nicht} im Literaturverzeichnis angef\"uhrt!)';
		} else {
			$citedInDiss = ' (\emph{Weder} in Fu\ss{}note noch im Literaturverzeichnis angef\"uhrt!)';
		}

		print '\phantomsection{}'."\n";
		print '\belowpdfbookmark{Fragment '.$l['seite'].' '.$l['zeilen'].'}{'.$l['wikiTitle'].'}'."\n";
		print '\hypertarget{'.titleToKey($l['wikiTitle']).'}{}'."\n";

		print '\begin{fragment}'."\n";
		print '\begin{fragmentpart}{Dissertation S.~'.$l['seite'].' Z.~'.$l['zeilen'].'}'."\n";
		print '\enquote{'.$l['plagiat'].'}'."\n";
		print '\end{fragmentpart}'."\n";
		print '\begin{fragmentpart}{Original '.$cite.'{'.$l['quelle'].'}'.$citedInDiss.'}'."\n";
		print '\enquote{'.$l['orig'].'}'."\n";
		print '\end{fragmentpart}'."\n";
		if(!empty($l['anmerkung'])) {
			print '\begin{fragmentpart}{Anmerkung}'."\n";
			print $l['anmerkung']."\n";
			print '\end{fragmentpart}'."\n";
		}
		print '\end{fragment}'."\n";
	}
}

