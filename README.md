# Papier

## Introduction
Papier is a low-level PHP library for generating PDF files. It implements the ISO 32000-1 standard.

## Why not use FPDF or TCPDF?
Although these are excellent libraries, they don't allow you to use the many features offered by the PDF format.

## What can be done with Papier ?
The library is growing every day; for the moment Papier can be used to write texts, draw shapes and display images and graphics. 
Papier supports the following:
- JPEG and PNG with transparency images.
- RGB and CMYK colors spaces for texts.
- Texts and images can be transformed (rotation, translation, skew).

## Example usage
```php

$pdf = new Papier();
$pdf->getHeader()->setVersion(3);

$page = $pdf->addPage([210, 297]);

$image = $pdf->createImageWidget()->setPage($page);
$image->setSource('https://images.unsplash.com/photo-1709468864471-a378b7435d03');
$image->setWidth(20);
$image->translate(10, 10);

$text = $pdf->createTextWidget()->setPage($page);
$text->setNonStrokingColor(1, 0, 0);
$text->setNonStrokingColorSpace(DeviceColourSpace::RGB);
$text->setRenderingMode(RenderingMode::STROKE);
$text->setText('Hello World !');
$text->setBaseFont('Helvetica');
$text->setFontSize(12);
$text->translate(100, 100);

$page = $pdf->addPage([210, 297]);

$draw = $pdf->createDrawWidget()->setPage($page);
$draw->setNonStrokingColor(0.4, 0, 0.4);
$draw->setStrokingColor(0.9, 0, 0);
$draw->setStrokingColorSpace(DeviceColourSpace::RGB);
$draw->setNonStrokingColorSpace(DeviceColourSpace::RGB);
$draw->addPoint(50, 50);
$draw->addPointWithControlPoints(150, 5, 74, 120, 150, 150);
$draw->addPoint(200, 200);

$info = $pdf->getInfo();
$info->setTitle('Test');
$info->setAuthor('Mikaël Carlavan');

$viewer = $pdf->getViewerPreferences();
$viewer->setDisplayDocTitle(true);

$pdf->save('test.pdf');
```

