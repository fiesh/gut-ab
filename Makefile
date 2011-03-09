TARGET=ab


.PHONY: all buildcache clean distclean maintainerclean

# If invoked as 'make' or 'make all', always rebuild 'cache' first.
ifeq ($(MAKECMDGOALS),)
  .PHONY: cache
endif
ifeq ($(MAKECMDGOALS),all)
  .PHONY: cache
endif



all: cache ${TARGET}.bib ${TARGET}.tex ${TARGET}.pdf

buildcache:
	@php buildcache.php

cache:
	@php buildcache.php

${TARGET}.pdf: ${TARGET}.bbl ${TARGET}.tex
	@pdflatex -interaction=nonstopmode ${TARGET}.tex

${TARGET}.bbl: ${TARGET}.bib ${TARGET}.tex
	@rm -f ${TARGET}.bbl
	@pdflatex -interaction=nonstopmode ${TARGET}.tex
	@bibtex ${TARGET}
	@pdflatex -interaction=nonstopmode ${TARGET}.tex
	@pdflatex -interaction=nonstopmode ${TARGET}.tex

${TARGET}.tex: cache
	@php abexport.php > ${TARGET}.tex

${TARGET}.bib: cache
	@php bibTeXexport.php > ${TARGET}.bib

clean:
	@rm -f ${TARGET}.log
	@rm -f ${TARGET}.aux
	@rm -f ${TARGET}.bbl
	@rm -f ${TARGET}.blg
	@rm -f ${TARGET}.log
	@rm -f ${TARGET}.toc
	@rm -f ${TARGET}.idx
	@rm -f ${TARGET}.ilg
	@rm -f ${TARGET}.ind
	@rm -f ${TARGET}.thm
	@rm -f ${TARGET}.out
	@rm -f ${TARGET}.tex
	@rm -f ${TARGET}.bib
	@rm -f ${TARGET}.pdf
	@rm -f texput.log

distclean: clean
	@rm -f cache

maintainerclean: distclean
