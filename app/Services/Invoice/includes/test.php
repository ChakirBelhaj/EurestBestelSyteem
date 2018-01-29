<?php

require_once 'tcpdf.php';

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->AddPage();

$products = [
		[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],
	[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],
	[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],
	[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],
	[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	]
	,[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],
	[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],
	[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],
	[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],
	[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	],[
		'name' => 'Broodje frikandel',
		'amount' => 1,
		'price' => 250,
		'sale_price' => 220,
	],
	[
		'name' => 'Broodje kroket',
		'amount' => 4,
		'price' => 2500,
		'sale_price' => 220,
	],
	[
		'name' => 'Salade',
		'amount' => 2,
		'price' => 750,
		'sale_price' => null,
	]
];

$html = '<table><tr><th>Product</th><th>Aantal</th><th>Prijs per stuk</th><th>Totaal</th></tr>';
foreach ($products as $product) {
	$html .= '<tr><td>' . $product['name'] . '</td><td>' . $product['amount'] . 'x</td><td>&euro; ' . ((float) $product['price'] / 100) . '</td><td>x</td></tr>';
}	
$html .= '</table>';

$pdf->writeHTML($html);

$pdf->Output('example_001.pdf');