<?php

require_once('korrekturen.php');
require_once('WikiLoader.php');

setlocale(LC_ALL, 'de_DE');

$content = WikiLoader::getRawTextByTitle('EntwurfAbschlussbericht');

$content = preg_replace('/.*BEGIN_ABSCHLUSSBERICHT/s', '', $content);

$content = preg_replace('/===\s*([^=]+?)\s*===/s', '\subsection{$1}', $content);

$content = preg_replace('/==\s*([^=]+?)\s*==/s', '\section{$1}', $content);

// references
$content = preg_replace('/\[\[([^]|]*)[^]]*\]\]/se', '\'\hyperlink{\'.titleToKey(\'$1\').\'}{$1}\'', $content);

// external links
// FIXME: doesn't work with [http://... Link text] style links yet
$content = preg_replace('!\[((http|https|ftp)://[^\]]*)\]!s', '\footnote{\url{$1}}', $content);

$content = preg_replace('/\'\'\'([^\']*)\'\'\'/s', '\textbf{$1}', $content);
$content = preg_replace('/\'\'([^\']*)\'\'/s', '\textsl{$1}', $content);
$content = preg_replace(';<u>([^<]*)</u>;s', '\underline{$1}', $content);

$arr = explode("\n", $content);

$i = 0;
$inEnum = false;
$inItem = false;
foreach($arr as $a) {
	$a = korrStringWiki($a);
	$new[$i] = '';
	if(substr($a, 0, 1) === '#') {
		if(!$inEnum) {
			$inEnum = true;
			$new[$i] .= '\begin{enumerate}'."\n";
		}
		$new[$i] .= '\item '.substr($a, 1)."\n";
	} else if(substr($a, 0, 1) === '*') {
		if(!$inItem) {
			$inItem = true;
			$new[$i] .= '\begin{itemize}'."\n";
		}
		$new[$i] .= '\item '.substr($a, 1)."\n";
	} else {
		if($inEnum) {
			$new[$i] .= '\end{enumerate}'."\n";
			$inEnum = false;
		}
		if($inItem) {
			$new[$i] .= '\end{itemize}'."\n";
			$inItem = false;
		}
		$new[$i] .= $a."\n";
	}
	$i++;
}

$content = implode("\n", $new);

echo($content);
