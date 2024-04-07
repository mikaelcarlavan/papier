<?php

require __DIR__.'/fpdf.php';

$pdf = new FPDF('P','mm','A4');
$pdf->SetCompression(false);
$pdf->AddPage();
$pdf->Image('unsplash.jpg');
$pdf->Output('F');

print $pdf->Output('S');

