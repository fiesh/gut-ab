\documentclass[ngerman,final,fontsize=12pt,paper=a4,twoside,toc=bibliography,bibtotoc,BCOR=8mm,draft=false]{scrartcl}

\usepackage[T1]{fontenc}
\usepackage{babel}
\usepackage[utf8]{inputenx}
\usepackage[sort&compress,square]{natbib}
\usepackage[hyphens]{url}
\usepackage[draft=false,final,plainpages=false,pdftex]{hyperref}
\usepackage{graphicx}
\usepackage{pdflscape}
\usepackage{longtable}
\usepackage[usenames]{color}
\usepackage{framed}
\usepackage{textcomp}

\usepackage[charter,sfscaled]{mathdesign}

\usepackage[spacing=true,tracking=true,kerning=true,babel]{microtype}

\author{GuttenPlag} 

\title{Abschlussbericht}
\subtitle{Gemeinschaftliche
  Dokumentation von Plagiaten in der Dissertation „Verfassung und
  Verfassungsvertrag: Konstitutionelle Entwicklungsstufen in den USA
  und der EU“ von Karl-Theodor Freiherr zu Guttenberg}
\publishers{\url{http://de.guttenplag.wikia.com/wiki/Abschlussbericht}}
\hypersetup{%
        pdfauthor={GuttenPlag},%
	pdftitle={Abschlussbericht --- Gemeinschaftliche Dokumentation von Plagiaten in der Dissertation „Verfassung und Verfassungsvertrag: Konstitutionelle Entwicklungsstufen in den USA und der EU“ von Karl-Theodor Freiherr zu Guttenberg}%
        pdflang={en},%
        pdfduplex={DuplexFlipLongEdge},%
        pdfprintscaling={None}%
}

\definecolor{shadecolor}{rgb}{0.95,0.95,0.95} 

\newenvironment{fragment}{\begin{snugshade}}{\end{snugshade}}
\newenvironment{fragmentpart}[1]
	{\indent\textbf{#1}\nopagebreak\\\nopagebreak}
	{\\}

\begin{document}
\addtokomafont{sectionentry}{\normalfont\bfseries}
\addtokomafont{disposition}{\normalfont\boldmath\bfseries}
\maketitle\thispagestyle{empty}
\tableofcontents

<?php require_once('importWiki.php'); ?>

\newpage~\newpage
\appendix
\section{Textnachweise}

<?php require_once('importFragmente.php'); ?>
\renewcommand{\refname}{Quellenverzeichnis}
\newpage
\bibliographystyle{dinat-custom}
\bibliography{ab}
\end{document}
