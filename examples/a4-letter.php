<?php


use Papier\Papier;
use Papier\Factory\Factory;
use Papier\Document\ProcedureSet;
use Papier\Graphics\DeviceColourSpace;
use Papier\Filter\FilterType;
use Papier\Filter\LZWFilter;
use Papier\Type\ImageType;
use Papier\Text\RenderingMode;
use Papier\Type\Type1FontType;

/**
 * Papier - Yet another PHP Framework for PDF
 *
 * @package  Papier
 * @author   Mikael Carlavan <mikael@camina.dev>
 */

define('PAPIER_START', microtime(true));
date_default_timezone_set("UTC");

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';

$pathToFontFile = 'Lato-Regular.ttf';
$pathToLogo = 'test.png';

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
