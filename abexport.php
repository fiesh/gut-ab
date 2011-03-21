\documentclass[ngerman,final,fontsize=12pt,paper=a4,twoside,toc=bibliography,bibtotoc,BCOR=8mm,draft=false]{scrreprt}

\usepackage[T1]{fontenc}
\usepackage{babel}
\usepackage[utf8]{inputenx}
\usepackage[sort&compress,square]{natbib}
\usepackage[babel]{csquotes}
\usepackage[hyphens]{url}
\usepackage[draft=false,final,plainpages=false,pdftex]{hyperref}
\usepackage{eso-pic}
\usepackage{graphicx}
\usepackage{xcolor}
\usepackage{pdflscape}
\usepackage{longtable}
\usepackage{framed}
\usepackage{textcomp}

\usepackage[charter,sfscaled]{mathdesign}

%\usepackage[spacing=true,tracking=true,kerning=true,babel]{microtype}
\usepackage[spacing=true,kerning=true,babel]{microtype}

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
        pdfprintscaling={None},%
	linktoc=all,%
<?php
require 'config.php';
if($abLinks === 'color' || $abLinks === 'color+underline' || $abLinks === 'color+box') {
	print "\t".'colorlinks,%'."\n";
} else if($abLinks === 'underline') {
	print "\t".'colorlinks=false,%'."\n";
	print "\t".'pdfborderstyle={/S/U/W 1},%'."\n";
	print "\t".'pdfborder=0 0 1,%'."\n";
} else if($abLinks === 'box') {
	// nothing to do
} else if($abLinks === 'none') {
	print "\t".'draft,%'."\n";
}
if($abEnableLinkColors === 'yes') {
	print "\t".'linkcolor='.$abInternalLinkColor.',%'."\n";
	print "\t".'citecolor='.$abSourceLinkColor.',%'."\n";
	print "\t".'filecolor='.$abExternalLinkColor.',%'."\n";
	print "\t".'urlcolor='.$abExternalLinkColor.',%'."\n";
	print "\t".'linkbordercolor={'.$abInternalLinkBorderColor.'},%'."\n";
	print "\t".'citebordercolor={'.$abSourceLinkBorderColor.'},%'."\n";
	print "\t".'filebordercolor={'.$abExternalLinkBorderColor.'},%'."\n";
	print "\t".'urlbordercolor={'.$abExternalLinkBorderColor.'},%'."\n";
} else {
	print "\t".'linkcolor=black,'."\n";
	print "\t".'citecolor=black,'."\n";
	print "\t".'filecolor=black,'."\n";
	print "\t".'urlcolor=black,'."\n";
	print "\t".'linkbordercolor={0 0 0},'."\n";
	print "\t".'citebordercolor={0 0 0},'."\n";
	print "\t".'filebordercolor={0 0 0},'."\n";
	print "\t".'urlbordercolor={0 0 0},'."\n";
}

?>
}

\definecolor{shadecolor}{rgb}{0.95,0.95,0.95} 

\newenvironment{fragment}{\begin{snugshade}}{\end{snugshade}}
\newenvironment{fragmentpart}[1]
	{\indent\textbf{#1}\nopagebreak\\\nopagebreak}
	{\\}
\newcommand{\BackgroundPic}
	{\put(0,0){\parbox[b][\paperheight]{\paperwidth}{%
		\vfill%
		\centering%
		\includegraphics[width=\paperwidth,height=\paperheight,%
			keepaspectratio]{background.png}%
		\vfill%
	}}}

\setkomafont{chapter}{\Large}
\setkomafont{section}{\large}
\addtokomafont{disposition}{\normalfont\boldmath\bfseries}
\urlstyle{rm}

\begin{document}

<?php
# color+underline und color+box muessen nach \begin{document} behandelt werden
if($abLinks === 'color+underline') {
	print "\hypersetup{%\n";
	print "\t".'pdfborderstyle={/S/U/W 1},%'."\n";
	print "\t".'pdfborder=0 0 1,%'."\n";
	print "}\n";
} else if($abLinks === 'color+box') {
	print "\hypersetup{%\n";
	print "\t".'pdfborderstyle={/S/S/W 1},%'."\n";
	print "\t".'pdfborder=0 0 1,%'."\n";
	print "}\n";
}
?>

%\AddToShipoutPicture*{\BackgroundPic}
\maketitle\thispagestyle{empty}
%\ClearShipoutPicture

\tableofcontents

<?php require_once('importWiki.php'); ?>

\appendix
\chapter{Textnachweise}

<?php require_once('importFragmente.php'); ?>
\renewcommand{\bibname}{Quellenverzeichnis}
\newpage
\bibliographystyle{dinat-custom}
\bibliography{ab}
\end{document}
