<?php

require_once('korrekturen.php');

$whitelist = array('KomplettPlagiat', 'Verschleierung', 'HalbsatzFlickerei', 'ShakeAndPaste', 'ÜbersetzungsPlagiat', 'StrukturPlagiat', 'BauernOpfer', 'VerschärftesBauernOpfer');

# Cache laden
if(!file_exists('cache')) {
	print "Fehler: Cache existiert nicht! 'make cache' ausgefuehrt?\n";
	exit(1);
}
$cache = unserialize(file_get_contents('cache'));

# Liste der Quellen erzeugen
$categories = array();
foreach($cache['sources'] as $source) {
	$categories[] = titleToKey($source['title']);
}

$list = array();
$i = 0;
foreach($cache['fragments'] as $f) {
	if(!in_array($f[7], $whitelist)) {
		print "%{$f['wikiTitle']}: Ignoriere, Plagiatstyp '$f[7]'\n";
		continue;
	}
	$found = false;
	foreach($f['categories'] as $c) {
		if(in_array(titleToKey($c), $categories)) { // Quelle gefunden
			$list[$i]['quelle'] = titleToKey($c);
			$list[$i]['seite'] = $f[1];
			$list[$i]['zeilen'] = $f[2];
			$list[$i]['plagiat'] = $f[3];
			$list[$i]['orig'] = $f[6];
			$list[$i]['kategorie'] = $f[7];
			$list[$i]['inLit'] = $f[8];
			$list[$i]['seitefund'] = $f[4];
			$list[$i]['zeilenfund'] = $f[5];
			$list[$i]['wikiTitle'] = titleToKey($f['wikiTitle']);
			preg_match('/\d+/', $list[$i]['seite'], $m1);
			preg_match('/\d+/', $list[$i]['zeilen'], $m2);
			$sort[$i] = (int)($m1[0]) *1000 + (int)$m2[0];
			$i++;
			$found = true;
			break;
		}
	}
	if(!$found) {
		print "%XXX: {$f['wikiTitle']}: Ignoriere, keine Quelle gefunden! (".implode(", ", $f['categories']).")\n";
		continue;
	}
}

array_multisort($sort, $list);

foreach($whitelist as $cat) {
	$done = false;
	foreach($list as $l) {
		if($l['kategorie'] !== $cat)
			continue;
		if(!$done) {
			echo '\subsection{'.$cat."}\n";
			$done = true;
		}
		$l['seite'] = korrBereich($l['seite']);
		$l['seitefund'] = korrBereich($l['seitefund']);
		$l['zeilen'] = korrBereich($l['zeilen']);
		$l['zeilenfund'] = korrBereich($l['zeilenfund']);
		$l['plagiat'] = korrString($l['plagiat']);
		$l['orig'] = korrString($l['orig']);
	
		if($l['seitefund']) {
			if($l['zeilenfund'])
				$cite = '\cite[S.~'.$l['seitefund'].' Z.~'.$l['zeilenfund'].']';
			else
				$cite = '\cite[S.~'.$l['seitefund'].']';
		} else {
			$cite = '\cite';
		}

		$start = '\belowpdfbookmark{Fragment '.$l['seite'].' '.$l['zeilen'].'}{'.$l['wikiTitle'].'}';

		if($l['inLit'] !== 'ja')
			$start .= '\fragmentNichtLit{';
		else
			$start .= '\fragment{';
	
		echo $start.$l['seite'].'}{'.$l['zeilen'].'}{'.$l['kategorie'].'}{'.$l['plagiat'].'}{'.$l['orig'].'}{'.$cite.'{'.$l['quelle'].'}}\hypertarget{'.$l['wikiTitle']."}{}\n";
		if($i++ == 20) break;
	}
}

