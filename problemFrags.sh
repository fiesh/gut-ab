#!/bin/sh

# Run 'make' before running this script.

echo '<code>'
grep '^XXX' ab.bib | grep 'Ignoriere Quelle' | sed -e 's/^XXX: Ignoriere Quelle: \(.*\)$/Ignoriere Quelle: [[:\1]]/' | sed -e 's/$/<br\/>/'
grep '^%XXX' ab.tex | grep 'Ignoriere, keine Quelle gefunden!' | sed -e 's/^%XXX: \(Fragment[^:]\+\):/[[\1]]:/' | sed -e 's/$/<br\/>/'
echo '</code>'
