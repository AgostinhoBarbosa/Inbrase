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
$multicell->setStyle("b", $pdf->getDefaultFontName(), "B", 11, "130,0,30");

$txt = "This is a demo of <b>NON BREAKING > S P>A C E EXAMPLE</b>";

//create an advanced multicell
$multicell->multiCell(0, 5, "Default line breaking characters:  ,.:;", 0);
$multicell->multiCell(100, 5, $txt, 1, 'R', 0, 0, 1);
$pdf->Ln(10); //new line


//create an advanced multicell
$multicell->multiCell(0, 5, "Setting > as line breaking character", 0);
$multicell->setLineBreakingCharacters(">");
$multicell->multiCell(100, 5, $txt, 1);
$pdf->Ln(10); //new line


//create an advanced multicell
$multicell->multiCell(0, 5, "Reseting the line breaking characters", 0);
$multicell->resetLineBreakingCharacters();
$multicell->multiCell(100, 5, $txt, 1);
$pdf->Ln(10); //new line


//send the pdf to the browser
$pdf->Output();
