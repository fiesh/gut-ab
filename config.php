<?php

############################################
# Konfiguration Abschlussbericht-Generator #
############################################

#
# Bemerkung: alle hier angegebenen Parameter muessen definiert sein
# und einen gueltigen Wert haben, sonst funktioniert der AB-Generator nicht.
#

#
# SORT_BY_CATEGORY legt fest, ob die Fragmente einfach nach Seitenzahl sortiert
# angehaengt werden, oder ob erst eine Aufteilung nach Plagiatstyp erfolgt
#
define(SORT_BY_CATEGORY, TRUE);

#
# $abLinks definiert, wie Links im PDF (interne Links, Quellen-Links, URL-Links)
# dargestellt werden. Hinweis: Bei allen Werten ausser $abLinks='none' sind
# Links klickbar.
# Standard: 'color'
#
# Einzelne Farben koennen weiter unten geaendert werden.
#
# Moegliche Werte:
#
#   $abLinks = 'color';
#     Stellt Links als farbigen Text dar.
#
#   $abLinks = 'underline';
#     Stellt Links als schwarzen Text mit farbiger Unterstreichung dar.
#
#   $abLinks = 'box';
#     Stellt Links als schwarzen Text mit farbiger Umrahmung dar.
#
#   $abLinks = 'color+underline';
#     Stellt Links als farbigen Text mit farbiger Unterstreichung dar.
#
#   $abLinks = 'color+box';
#     Stellt Links als farbigen Text mit farbiger Umrahmung dar.
#
#   $abLinks = 'none';
#     Erzeugt keine Links im PDF.
#
$abLinks = 'color';

#
# Ist $abEnableLinkColors='no' gesetzt, werden alle Farben auf
# schwarz gesetzt und alle weiteren Farboptionen (s.u.) ignoriert.
# Wertebereich: 'yes', 'no'
# Standard: 'yes'
#
$abEnableLinkColors = 'yes';

#
# $abInternalLinkColor definiert die Farbe von Links, die auf eine andere
# Stelle im selben PDF verweisen (z.B. Fussnotenverweise).
# Wertebereich: xcolor-Farbname
# Standard: 'red'
#
# $abInternalLinkBorderColor definiert die Farbe von Unterstreichungen bzw.
# Rahmen um interne Links.
# Wertebereich: Rahmenfarbe (R G B -- jeweils Wert zwischen 0 und 1)
# Standard: '1 0 0'
#
$abInternalLinkColor = 'red';
$abInternalLinkBorderColor = '1 0 0';

#
# $abSourceLinkColor definiert die Farbe von Links, die auf eine
# Plagiatquelle verweisen. Jedes Fragment enthaelt einen solchen Link.
# Wertebereich: xcolor-Farbname
# Standard: 'green!50!black'
#
# $abSourceLinkBorderColor definiert die Farbe von Unterstreichungen bzw.
# Rahmen um Quellenlinks.
# Wertebereich: Rahmenfarbe (R G B -- jeweils Wert zwischen 0 und 1)
# Standard: '0 0.5 0'
#
$abSourceLinkColor = 'green!50!black';
$abSourceLinkBorderColor = '0 0.5 0';

#
# $abExternalLinkColor definiert die Farbe von Links, die auf eine
# externe Webadresse / URL verweisen.
# Wertebereich: xcolor-Farbname
# Standard: 'blue'
#
# $abExternalLinkBorderColor definiert die Farbe von Unterstreichungen bzw.
# Rahmen um externe Links.
# Wertebereich: Rahmenfarbe (R G B -- jeweils Wert zwischen 0 und 1)
# Standard: '0 0 1'
#
$abExternalLinkColor = 'blue';
$abExternalLinkBorderColor = '0 0 1';
