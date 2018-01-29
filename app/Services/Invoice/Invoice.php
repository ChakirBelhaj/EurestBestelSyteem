<?php

namespace App\Services\Invoice;

require_once 'includes/tcpdf.php';

/*
	Generates an invoice using the TCPDF library
 */
class Invoice {

	private $pdf;
	private $order;

	/*
		Creates a new invoice for the given user and order
	 */
	public function new($user, $order) {
		$this->order = $order;
		$this->pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$this->pdf->AddPage();
		$this->pdf->writeHTML($this->generateHTMLForInvoice($user, $order));
		return $this;
	}

	/*
		Renders the HTML that is used for the invoice
	 */
	private function generateHTMLForInvoice($user, $order) {
		return app('renderer')->render('invoice/order/index.twig', ['user' => $user, 'order' => $order]);
	}

	/*
		Outputs the PDF, either directly in the response or in a variable which can be send as an email attachment
	 */
	public function output($show = true) {
		$filename = 'Eurest_invoice_'.$this->order->id.'.pdf';
		if ($show) {
			$this->pdf->Output($filename);	
		} else {
			return $this->pdf->Output($filename, 'S');
		}
	}
}