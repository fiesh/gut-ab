<?php

$whitelist = array('KomplettPlagiat', 'Verschleierung', 'HalbsatzFlickerei', 'ShakeAndPaste', 'ÜbersetzungsPlagiat', 'StrukturPlagiat', 'BauernOpfer', 'VerschärftesBauernOpfer');

require_once('FragmentLoader.php');
require_once('categories.php');
require_once('korrekturen.php');

$fragments = FragmentLoader::getFragments();
$file = fopen('cache', 'w'); fwrite($file, serialize($fragments)); fclose($file);

$fragments = unserialize(file_get_contents('cache'));

$list = array();
$i = 0;
foreach($fragments as $f) {
	if(!in_array($f[7], $whitelist)) {
		print "%{$f['wikiTitle']}: Ignoriere, Plagiatstyp '$f[7]'\n";
		continue;
	}
	preg_match_all('/\[\[Kategorie:([^]]+)\]\]/', $f[0], $matches);
	$found = false;
	foreach($matches[1] as $c) {
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
		print "%XXX: {$f['wikiTitle']}: Ignoriere, keine Quelle gefunden! (Kategorien: ".implode(", ", $matches[1]).")\n";
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

