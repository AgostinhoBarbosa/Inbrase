<?php
/**
 * Pdf Advanced Multicell - Example
 * Copyright (c), Interpid, http://www.interpid.eu
 */

require_once __DIR__ . "/../autoload.php";
    
    use Interpid\PdfExamples\PdfFactory;
    use Interpid\PdfLib\Multicell;
    
    $factory = new PdfFactory();

//get the PDF object
$pdf = PdfFactory::newPdf('multicell');

// Create the Advanced Multicell Object and inject the PDF object
$multicell = new Multicell($pdf);

// Set the styles for the advanced multicell
$multicell->setStyle("p", $pdf->getDefaultFontName(), "", 11, "130,0,30");
$multicell->setStyle("b", $pdf->getDefaultFontName(), "B", 11, "130,0,30");
$multicell->setStyle("i", $pdf->getDefaultFontName(), "I", 11, "80,80,260");
$multicell->setStyle("u", $pdf->getDefaultFontName(), "U", 11, "80,80,260");
$multicell->setStyle("h1", $pdf->getDefaultFontName(), "B", 14, "203,0,48");
$multicell->setStyle("h3", $pdf->getDefaultFontName(), "B", 12, "203,0,48");
$multicell->setStyle("h4", $pdf->getDefaultFontName(), "BI", 11, "0,151,200");
$multicell->setStyle("hh", $pdf->getDefaultFontName(), "B", 11, "255,189,12");
$multicell->setStyle("ss", $pdf->getDefaultFontName(), "", 7, "203,0,48");
$multicell->setStyle("font", $pdf->getDefaultFontName(), "", 10, "0,0,255");
$multicell->setStyle("style", $pdf->getDefaultFontName(), "BI", 10, "0,0,220");
$multicell->setStyle("size", $pdf->getDefaultFontName(), "BI", 12, "0,0,120");
$multicell->setStyle("color", $pdf->getDefaultFontName(), "BI", 12, "0,255,255");

$pdf->Ln(10); //line break

// create the advanced multicell
$title = file_get_contents(PDF_APPLICATION_PATH . '/content/multicell-title.txt');
$multicell->multiCell(0, 5, $title, 1, "J", 1, 3, 3, 3, 3);

$pdf->Ln(10); //line break


//read TAG formatted text from files
$txt = file_get_contents(PDF_APPLICATION_PATH . '/content/multicell.txt');
$s = $txt . "\n\n\n\nRepeat the text to trigger a page break \n\n\n" . $txt;

//create an advanced multicell
$multicell->multiCell(0, 5, $s, 1, "J", 1, 3, 3, 3, 3);
$pdf->Ln(10); //new line


//send the pdf to the browser
$pdf->Output();
