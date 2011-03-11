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
require_once('BibliographyLoader.php');

$cache = array();

# Fragmente laden
print "Lade Fragmente... "; flush();
$cache['fragments'] = FragmentLoader::getFragments();
print "fertig!\n";

# Quellen laden
print "Lade Quellen... "; flush();
$cache['sources'] = BibliographyLoader::getSources();
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
