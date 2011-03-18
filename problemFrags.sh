#!/bin/sh

# Run 'make' before running this script.

echo '<code>'
grep '^%XXX' ab.tex | sed -e 's/^%XXX: \(Fragment[^:]\+\):/[[\1]]:/' | sed -e 's/^%XXX: Ignoriere \([^:]\+\): \(.*\)$/Ignoriere \1: [[:\2]]/' | sed -e 's/$/<br\/>/'
echo '</code>'
