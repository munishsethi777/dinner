<?php
require_once($ConstantsArray['dbServerUrl'] ."vendor/autoload.php");
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
class html2PdfUtil{
	public static function html2Pdf($html){
		try {
			ob_start();
			//include dirname(__FILE__).'/res/example00.php';
			$content = $html;
			$html2pdf = new Html2Pdf('P', 'A4', 'fr');
			$html2pdf->setDefaultFont('Arial');
			$html2pdf->setTestTdInOnePage(false);
			$html2pdf->writeHTML($content);
			$output = $html2pdf->output('example00.pdf','S');
			return $output;
		} catch (Html2PdfException $e) {
			$html2pdf->clean();
			$formatter = new ExceptionFormatter($e);
		}
		
	}
}