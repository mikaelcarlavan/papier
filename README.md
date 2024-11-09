# Papier

## Introduction
Papier is a low-level PHP library for generating PDF files. It implements the ISO 32000-1 standard.

## Why not use FPDF or TCPDF?
Although these are excellent libraries, they don't allow you to use the many features offered by the PDF format.

## What can be done with Papier ?
The library is growing every day; for the moment Papier can be used to write texts, draw shapes and display images and graphics.

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
$text->setNonStrokingColor(1, 0, 0);
$text->setNonStrokingColorSpace(DeviceColourSpace::RGB);
$text->setRenderingMode(RenderingMode::STROKE);
$text->setText('Hello World !');
$text->setBaseFont('Helvetica');
$text->setFontSize(12);
$text->setY(100);
$text->setX(100);

$page = $pdf->addPage([210, 297]);

$curve = $pdf->createDrawWidget()->setPage($page);
$curve->setNonStrokingColor(0.4, 0, 0.4);
$curve->setStrokingColor(0.9, 0, 0);
$curve->setStrokingColorSpace(DeviceColourSpace::RGB);
$curve->setNonStrokingColorSpace(DeviceColourSpace::RGB);
$curve->addPoint(50, 50);
$curve->addPointWithControlPoints(150, 5, 74, 120, 150, 150);
$curve->addPoint(200, 200);

$info = $pdf->getInfo();
$info->setTitle('Test');
$info->setAuthor('Mikaël Carlavan');

$viewer = $pdf->getViewerPreferences();
$viewer->setDisplayDocTitle(true);

$pdf->save('test.pdf');
```

