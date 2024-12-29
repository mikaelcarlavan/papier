# Papier
<p>
    <a href="https://packagist.org/packages/papier/papier">
        <img src="https://poser.pugx.org/papier/papier/d/total.svg" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/papier/papier">
        <img src="https://poser.pugx.org/papier/papier/v/stable.svg" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/papier/papier">
        <img src="https://poser.pugx.org/papier/papier/license.svg" alt="License">
    </a>
</p>

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
- True type fonts are supported.

## Example usage
```php
require __DIR__.'/../vendor/autoload.php';

$pathToLocalFontFile = 'Lato-Regular.ttf';
$pathToLocalImage = 'test.png';

$pdf = new Papier();
$pdf->getHeader()->setVersion(7);

$page = $pdf->addPage([210, 297]);

$image = $pdf->createImageComponent()->setPage($page);
$image->setSource($pathToLocalImage);
$image->setWidth(50);
$image->translate(10, 10);

$font = Factory::create('Papier\Type\TrueTypeFontType', null, true);
$font->load($pathToLocalFontFile);

$text = $pdf->createTextComponent()->setPage($page);
$text->setNonStrokingColor(1, 0, 0);
$text->setStrokingColor(0, 1, 0);
$text->setNonStrokingColorSpace(DeviceColourSpace::RGB);
$text->setStrokingColorSpace(DeviceColourSpace::RGB);
$text->setRenderingMode(RenderingMode::FILL_THEN_STROKE);
$text->setText('Hello World !');
$text->setFont($font);
$text->setFontSize(12);
$text->translate(100, 100);

$draw = $pdf->createDrawComponent()->setPage($page);
$draw->setNonStrokingColor(0.4, 0, 0.4);
$draw->setStrokingColor(0.9, 0, 0);
$draw->setStrokingColorSpace(DeviceColourSpace::RGB);
$draw->setNonStrokingColorSpace(DeviceColourSpace::RGB);
$draw->addPoint(50, 50);
$draw->addPointWithControlPoints(150, 5, 74, 120, 150, 150);
$draw->addPoint(200, 200);

$line = $pdf->createSegmentComponent()->setPage($page);
$line->setNonStrokingColor(0.4, 0, 0.4);
$line->setStrokingColor(0.9, 0, 0);
$line->setStrokingColorSpace(DeviceColourSpace::RGB);
$line->setNonStrokingColorSpace(DeviceColourSpace::RGB);
$line->setStartPoint(45, 80);
$line->setEndPoint(145, 180);
$line->setLineWidth(5);

$info = $pdf->getInfo();
$info->setTitle('Papier');
$info->setAuthor('Mikaël Carlavan');

$viewer = $pdf->getViewerPreferences();
$viewer->setDisplayDocTitle(true);

$pdf->save('test.pdf');
```
## License
Papier is open-sourced software licensed under the [MIT license](LICENSE.md).

## Known issues
At the moment, the biggest issue is the incorrect display of accents in texts. Generally speaking, work needs to be done on encoding and font management.
