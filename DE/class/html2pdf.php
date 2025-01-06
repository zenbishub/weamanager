<?php
session_start();
extract($_REQUEST);

// define("ROOT",substr(__DIR__,0,-6));
// error_reporting(0);
// isset($_SESSION['werk'])?$werk=$_SESSION['werk']:$werk=null;
// isset($_SESSION['bereich'])?$bereich=$_SESSION['bereich']:$bereich=null;
// isset($_SESSION['gruppe'])?$gruppe=$_SESSION['gruppe']:$gruppe=null;
// $pfad        =  ROOT."/data/$werk/$bereich/$gruppe/GGprotokoll/$filename.pdf";

require_once '../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

if(isset($_REQUEST['create'])):
try {
    ob_start();
    include  "../PDF/create.php";
    $content = ob_get_clean();
    $html2pdf = new Html2Pdf('P', 'A4', 'de',true,"UTF-8",[10,0,10,0]);
    $html2pdf->setDefaultFont('Arial');
    $html2pdf->writeHTML($content);
    //$html2pdf->output($pfad, "F");
    $html2pdf->output();
    exit;
} catch (Html2PdfException $e) {
    exit;
    $html2pdf->clean();
    $formatter = new ExceptionFormatter($e);
    echo $formatter->getHtmlMessage();
}
endif;