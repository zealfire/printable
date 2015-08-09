<?php
// if you are using composer, just use this
//include 'vendor/autoload.php';// Include Composer autoloader if not already done.

$autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload)) {
  require_once $autoload;
}
 
// Parse pdf file and build necessary objects.
$parser = new \Smalot\PdfParser\Parser();
$pdf    = $parser->parseFile('testPDF.pdf');
 
$text = $pdf->getText();
echo $text;
//see:C:\xampp\htdocs\sample\Pdf-to-text-via-PHP-master\examples
//https://github.com/smalot/pdfparser/tree/master/src/Smalot/PdfParser
//https://github.com/angeloskath/Pdf-to-text-via-PHP/issues/1
//http://stackoverflow.com/questions/6999889/how-to-extract-text-from-the-pdf-document
//http://stackoverflow.com/questions/14782751/convert-pdf-to-html-in-php
?>