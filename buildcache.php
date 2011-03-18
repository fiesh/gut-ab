<?php

#
# Beschreibung des Cache-Formats:
#
# Der Cache ist ein assoziatives Array mit sechs Eintraegen.
# In der Datei 'cache' liegt es in serialisiertes Form vor.
#
# Die sechs Eintraege sind:
#
#   $cache['fragments'] =
#     Liste aller Fragmente, direkt von FragmentLoader::getFragmentsG2006().
#
#   $cache['fragmenttypes'] =
#     Liste aller Plagiatskategorien (in Kategorie:PlagiatsKategorien),
#     direkt von FragmentLoader::getFragmentTypes().
#
#   $cache['sources'] =
#     Liste aller Quellen (mit Vorlage:Quelle). Jede Quelle ist ein
#     assoziatives Array mit Feldnamen (z.B. 'Autor', 'Hrsg', 'InLit') als
#     moegliche Schluessel. Unter dem Schluessel 'title' ist der Name der
#     Kategorie gespeichert.
#
#   $cache['static'] =
#     Einleitung und Hauptteil des Abschlussberichts, im Wikitext-Format.
#
#   $cache['ignored'] =
#     Assoziatives Array:
#       $cache['ignored']['fragments'] = Liste ignorierter Fragmente
#       $cache['ignored']['fragmenttypes'] = Liste ign. Plagiatskategorien
#       $cache['ignored']['sources'] = Liste ignorierter Quellen
#
#   $cache['timestamp'] =
#     Wann der Cache zuletzt erstellt wurde (als String).
#


require_once('WikiLoader.php');
require_once('FragmentLoader.php');
require_once('BibliographyLoader.php');

$cache = array();

# Fragmente laden
print "Lade Fragmente... "; flush();
$ignoredFragments = array();
$cache['fragments'] = FragmentLoader::getFragmentsG2006($ignoredFragments);
print "fertig!\n";

# Plagiatskategorien laden
print "Lade Plagiatskategorien... "; flush();
$ignoredFragmentTypes = array();
$cache['fragmenttypes'] = FragmentLoader::getFragmentTypes($ignoredFragmentTypes);
print "fertig!\n";

# Quellen laden
print "Lade Quellen... "; flush();
$ignoredSources = array();
$cache['sources'] = BibliographyLoader::getSources($ignoredSources);
print "fertig!\n";

# Entwurf laden
print "Lade Entwurf... "; flush();
$cache['static'] = WikiLoader::getRawTextByTitle('EntwurfAbschlussbericht');
print "fertig!\n";

# Ignorierte Eintraege speichern
$cache['ignored']['fragments'] = $ignoredFragments;
$cache['ignored']['fragmenttypes'] = $ignoredFragmentTypes;
$cache['ignored']['sources'] = $ignoredSources;

# timestamp setzen
setlocale(LC_TIME, "de_DE");
$cache['timestamp'] = strftime('%c');

# cache schreiben
print "Speichere Cache..."; flush();
$file = fopen('cache', 'wb');
fwrite($file, serialize($cache));
fclose($file);
print " fertig!\n\n";
