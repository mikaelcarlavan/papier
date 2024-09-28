# Papier

## Introduction
Papier is a low-level PHP library for generating PDF files. It implements the ISO 32000-1 standard.

## Why not use FPDF or TCPDF?
Although these are excellent libraries, they don't allow you to use the many features offered by the PDF format.

## What can be done with Papier ?
The library is growing every day, but for the moment Papier can be used to write text and display images and graphics.

## Example usage
```php
use Papier\Papier;

$pdf = new Papier();
$pdf->getHeader()->setVersion(3);

$page = $pdf->addPage([210, 297]);


$image = $pdf->createImageWidget()->setPage($page);
$image->setSource('unsplash.jpg');
$image->setWidth(210);

$text = $pdf->createTextWidget()->setPage($page);
$text->setNonStrokingColor(0.4, 0, 0.4);
$text->setNonStrokingColorSpace(DeviceColourSpace::RGB);

$text->setText('Hello World !');
$text->setBaseFont('Helvetica');
$text->setFontSize(24);
$text->setY(100);
$text->setX(100);

$info = $pdf->getInfo();
$info->setTitle('Test');
$info->setAuthor('Mikaël Carlavan');

$viewer = $pdf->getViewerPreferences();
$viewer->setDisplayDocTitle(true);

$pdf->save('test.pdf');
```

