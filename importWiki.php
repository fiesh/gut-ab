<?php

require_once('korrekturen.php');

function getCommonPrefix($s1, $s2) {
	$max = min(strlen($s1), strlen($s2));
	for ($i = 0; $i < $max && $s1[$i] == $s2[$i]; ++$i)
		;
	return substr($s1, 0, $i);
}

setlocale(LC_ALL, 'de_DE');

if(!file_exists('cache')) {
	print "Fehler: Cache existiert nicht! 'make cache' ausgefuehrt?\n";
	exit(1);
}
$cache = unserialize(file_get_contents('cache'));

$content = $cache['static'];

$content = preg_replace('/.*BEGIN_ABSCHLUSSBERICHT/s', '', $content);

$content = preg_replace('/===\s*([^=]+?)\s*===/s', '\section{$1}', $content);

$content = preg_replace('/==\s*([^=]+?)\s*==/s', '\chapter{$1}', $content);

// references
$content = preg_replace('/\[\[([^]|]*)[^]]*\]\]/se', '\'\hyperlink{\'.titleToKey(\'$1\').\'}{$1}\'', $content);

// external links
// FIXME: doesn't work with [http://... Link text] style links yet
$content = preg_replace('!\[((http|https|ftp)://[^\]]*)\]!s', '\footnote{\url{$1}}', $content);

$content = preg_replace('/\'\'\'([^\']*)\'\'\'/s', '\textbf{$1}', $content);
$content = preg_replace('/\'\'([^\']*)\'\'/s', '\textsl{$1}', $content);
$content = preg_replace(';<u>([^<]*)</u>;s', '\underline{$1}', $content);

$arr = explode("\n", $content);
$arr[] = ''; // for ensuring itemize/enumerate are closed properly

$i = 0;
$inEnum = '';
foreach($arr as $a) {
	$a = korrStringWiki($a);
	$new[$i] = '';
	preg_match('/^([\*#]*)(.*)$/', $a, $match);
	$enumPrefix = $match[1];
	$enumSuffix = $match[2];

	$commonEnumPrefix = getCommonPrefix($enumPrefix, $inEnum);
	while(strlen($inEnum) > strlen($commonEnumPrefix)) {
		if($inEnum[strlen($inEnum)-1] == '#')
			$new[$i] .= '\end{enumerate}'."\n";
		else
			$new[$i] .= '\end{itemize}'."\n";
		$inEnum = substr($inEnum, 0, strlen($inEnum)-1);
	}
	while(strlen($inEnum) < strlen($enumPrefix)) {
		if($enumPrefix[strlen($inEnum)] == '#')
			$new[$i] .= '\begin{enumerate}'."\n";
		else
			$new[$i] .= '\begin{itemize}'."\n";
		$inEnum .= $enumPrefix[strlen($inEnum)];
	}

	if(!empty($enumPrefix))
		$new[$i] .= '\item ';
	$new[$i] .= $enumSuffix."\n";
	$i++;
}

$content = implode("\n", $new);

echo($content);
