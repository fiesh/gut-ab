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

\newcommand{\fragment}[6]{\begin{snugshade}%
	\indent\textbf{Dissertation S.~#1 Z.~#2}\nopagebreak\\\nopagebreak%
	#4\\%
	\indent\textbf{Original #6}\nopagebreak\\\nopagebreak%
	#5\\%
	\end{snugshade}}
\newcommand{\fragmentInFN}[6]{\begin{snugshade}%
	\indent\textbf{Dissertation S.~#1 Z.~#2}\nopagebreak\\\nopagebreak%
	#4\\%
	\indent\textbf{Original #6 (Nur in Fu\ss{}note, aber \emph{nicht} im Literaturverzeichnis angef\"uhrt!)}\nopagebreak\\\nopagebreak%
	#5\\%
	\end{snugshade}}
\newcommand{\fragmentNichtLit}[6]{\begin{snugshade}%
	\indent\textbf{Dissertation S.~#1 Z.~#2}\nopagebreak\\\nopagebreak%
	#4\\%
	\indent\textbf{Original #6 (\emph{Weder} in Fu\ss{}note noch im Literaturverzeichnis angef\"uhrt!)}\nopagebreak\\\nopagebreak%
	#5\\%
	\end{snugshade}}

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
