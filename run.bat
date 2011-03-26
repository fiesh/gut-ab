@echo off
rem Batch-Alternative zu 'make all', falls kein make verfuegbar ist
php buildcache.php
php abexport.php > ab.tex
php bibTeXexport.php > ab.bib
del /Q ab.bbl
pdflatex -interaction=nonstopmode ab.tex
bibtex ab
pdflatex -interaction=nonstopmode ab.tex
pdflatex -interaction=nonstopmode ab.tex
