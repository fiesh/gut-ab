<?php

#
# Beschreibung des Cache-Formats:
#
# Der Cache ist ein assoziatives Array mit vier Eintraegen.
# In der Datei 'cache' liegt es in serialisiertes Form vor.
#
# Die vier Eintraege sind:
#   $cache['fragments'] =
#     Liste aller Fragmente, direkt von FragmentLoader::getFragments().
#   $cache['sources'] =
#     Liste aller Quellen (mit Vorlage:Quelle). Jede Quelle ist ein
#     assoziatives Array mit Feldnamen (z.B. 'Autor', 'Hrsg', 'InLit') als
#     moegliche Schluessel. Unter dem Schluessel 'title' ist der Name der
#     Kategorie gespeichert.
#   $cache['static'] =
#     Einleitung und Hauptteil des Abschlussberichts, in Wikitext.
#   $cache['timestamp'] =
#     Wann der Cache zuletzt erstellt wurde (als String).
#


require_once('WikiLoader.php');
require_once('FragmentLoader.php');

$cache = array();

# Fragmente laden
print "Lade Fragmente... "; flush();
$cache['fragments'] = FragmentLoader::getFragments();
print "fertig!\n";

# Quellen laden
print "Lade Quellen... "; flush();
$pageids = WikiLoader::getCategoryMembers('Kategorie:Quelle');
$entries = WikiLoader::getEntries($pageids, true, true);
print "fertig!\n";

# Quellen verarbeiten
print "Verarbeite Quellen... "; flush();
$cache['sources'] = array();
foreach($entries as $entry) {
	$source = array('title' => $entry['title']);

	if(preg_match_all('/{{Quelle(.*)}}/s', $entry['revisions'][0]['*'], $matches) === 1) {
		$text = $matches[1][0];
		preg_match_all('/|\s*(\w+)\s*=\s*([^|]+)/', $text, $matches);
		$i = 0;
		$fields = array();
		while(isset($matches[1][$i])) {
			if($matches[1][$i])
				$source[$matches[1][$i]] = trim($matches[2][$i]);
			$i++;
		}
	}

	$cache['sources'][] = $source;
}
print "fertig!\n";

# Entwurf laden
print "Lade Entwurf... "; flush();
$cache['static'] = WikiLoader::getRawTextByTitle('EntwurfAbschlussbericht');
print "fertig!\n";

# timestamp setzen
setlocale(LC_TIME, "de_DE");
$cache['timestamp'] = strftime('%c');

# cache schreiben
print "Speichere Cache..."; flush();
$file = fopen('cache', 'wb');
fwrite($file, serialize($cache));
fclose($file);
print " fertig!\n\n";
