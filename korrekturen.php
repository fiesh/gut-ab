<?php

// Seiten korrigieren
function korrBereich($s)
{
	$i = 0;
	$ret = '';
	if(preg_match_all('/(\d+)[-,](\d+)/', $s, $matches)) {
		while(isset($matches[1][$i])) {
			if($matches[1][$i] == $matches[2][$i])
				$ret .= ((int)$matches[1][$i]).',~';
			else
				$ret .= ((int)$matches[1][$i]).'--'.((int)$matches[2][$i]).',~';
			$i++;
		}
	} else if(preg_match_all('/(\d+)/', $s, $matches)) {
		while(isset($matches[1][$i])) {
			$ret .= ((int)$matches[1][$i]).',~';
			$i++;
		}
	} else {
		return false;
	}

	return trim(substr($ret, 0, strlen($ret)-2));
}

function korrStringWiki($s, $doTrim=true)
{
	preg_match_all('/&[#\d\w]+;/s', $s, $matches);
	$i = 0;
	$from = array();
	$to = array();
	while(isset($matches[0][$i])) {
		$from[$i] = $matches[0][$i];
		$to[$i] = mb_convert_encoding($from[$i], 'UTF-8', 'HTML-ENTITIES');
		$i++;
	}
	$s = str_replace($from, $to, $s);
	//$s = preg_replace('/"([^"]+)"/', '"`$1"\'', $s); // Anfuehrungszeichen lassen sich nicht korrekt reparieren.
	$s = str_replace(array(
			'"',
			'&',
			'#',
			'%',
			'',
			'_',
			'^',
			'´',
			'ﬁ',
			'¬',
			'ﬂ',
			'→',
			'°',
			'‑',
			'­',
			'$',
			'[',
			']',
			'~',
			'−',
		), array(
			'\textquotedbl{}',
			'\&',
			'\#',
			'\%',
			'',
			'\_',
			'\^',
			'\'',
			'i',
			' ',
			'fl',
			'\textrightarrow{}',
			'o',
			'---',
			'-',
			'\$',
			'$[$',
			'$]$',
			'\~{}',
			'---',
		), $s);

	$s = korrDash($s);
	$s = strip_tags($s);
	if ($doTrim)
		$s = trim($s);

	return $s;
}

function korrString($s, $doTrim=true)
{
	$s = str_replace(array(
			'\\',
			'{',
			'}',
		), array(
			'\backslash ',
			'\{',
			'\}',
		), $s);
	return korrStringWiki($s, $doTrim);
}

// wie korrString, aber externe Links in Anmerkung mit @url umfassen
function korrStringWithLinks($s, $doTrim=true)
{
	$result = '';
	$prots = 'http|https|ftp';
	$schemeRegex = '(?:(?:'.$prots.'):\/\/)';
	foreach(preg_split('/(\['.$schemeRegex.'[^][{}<>"\\x00-\\x08\\x0a-\\x1F]+\]|'.$schemeRegex.'[^][{}<>"\\x00-\\x20\\x7F]+)/s', $s, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE) as $part) {
		if(preg_match('/^'.$schemeRegex.'/s', $part, $match)) {
			$result .= '\url{'.$part.'}';
		} else if(preg_match('/^\[('.$schemeRegex.'[^][{}<>"\\x00-\x20\\x7F]+) *([^\]\\x00-\\x08\\x0A-\\x1F]*)?\]$/s', $part, $match)) {
			//FIXME: wie Links mit angegebenem Linktext behandeln?
			$result .= '\url{'.$match[1].'}';
		} else {
			$result .= korrString($part, false);
		}
	}

	if ($doTrim)
		$result = trim($result);

	return $result;
}

// Grossbuchstaben in Titel und Sammlung vor bibtex schuetzen
function korrVersalien($s)
{
	return preg_replace('/([A-Z])/', '{$1}', $s);
}

// - durch -- ersetzen, wenn es passt
function korrDash($s)
{
	return str_replace(' - ', ' --- ', $s);
}

// & durch \& ersetzen
function korrAmpersand($s)
{
	return str_replace('&', '\&', $s);
}

// , durch and ersetzen in den Autoren (aber nicht in geschweiften Klammern)
function korrAnd($s)
{
	$depth = 0;
	for($i = 0; $i < strlen($s); ++$i) {
		if($s[$i] == ',' && $depth <= 0)
			$s = substr($s, 0, $i) . ' and ' . substr($s, $i+1);
		else if($s[$i] == '{')
			$depth++;
		else if($s[$i] == '}')
			$depth--;
	}
	return $s;
}

// u.a. durch and others ersetzen in den Autoren
function korrEtAl($s)
{
	return preg_replace('/\[\s*u\.\s*a\.\s*\]\s*$|u\.\s*a\.\s*$/', ' and others', $s);
}

// aeussere eckige Klammern entfernen
// FIXME: Jahr und [Jahr] unterscheiden sich.
//   Jahr ist das tatsaechliche Erscheinungsjahr.
//   [Jahr] ist das Erscheinungsjahr der Ausgabe. Wie in bibtex darstellen?
function korrBracket($s)
{
	return preg_replace('/^\s*\[(.*)\]\s*$/', '$1', $s);
}

function replaceIfEmpty($s, $replacement)
{
	return (trim($s) == '') ? $replacement : $s;
}

function titleToKey($title)
{
	$title = str_replace('Kategorie:', '', $title);
	$title = str_replace(' ', '-', $title);
	$title = preg_replace('/[^a-zA-Z0-9]/', '-', $title);

	return $title;
}

