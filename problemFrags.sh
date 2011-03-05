#!/bin/sh

# Hint: run 'make' or 'php abexport.php > ab.tex' before running this script.

grep '^%XXX' ab.tex | grep 'Ignoriere, keine Quelle gefunden!' | sed -e 's/^%XXX: \(Fragment[^:]\+\):/%XXX: [[\1]]:/'
