<?php

use Papier\Papier;
use Papier\Factory\Factory;
use Papier\Document\ProcedureSet;
use Papier\Graphics\DeviceColourSpace;
use Papier\Filter\FilterType;
use Papier\Filter\LZWFilter;
use Papier\Type\ImageType;

/**
 * Papier - Yet another PHP Framework for PDF
 *
 * @package  Papier
 * @author   Mikael Carlavan <hello@petitmoteur.com>
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

$now = new DateTime("now");

$pdf = new Papier();
$pdf->getHeader()->setVersion(3);

$page = $pdf->addPage([210, 297], 144);


$image = $pdf->createImageWidget()->setPage($page);
$image->setSource('unsplash.jpg');
$image->setWidth(210);
//$image->setHeight(297);
//$image->setX(300);
/*
$text = $pdf->createTextWidget()->setPage($page);
$text->setNonStrokingColor(0.4, 0, 0.4);
$text->setNonStrokingColorSpace(DeviceColourSpace::RGB);

$text->setText('Hello World !');
$text->setBaseFont('Helvetica');
$text->setFontSize(24);
$text->setY(100);
$text->setX(100);

$rectangle = $pdf->createRectangleWidget()->setPage($page);
$rectangle->setWidth(100);
$rectangle->setHeight(50);
$rectangle->setY(300);
$rectangle->setX(300);
//$rectangle->setNonStrokingColor(0.4, 0, 0.4);
$rectangle->setStrokingColor(0.9, 0, 0);
$rectangle->setStrokingColorSpace(DeviceColourSpace::RGB);
//$rectangle->setNonStrokingColorSpace(DeviceColourSpace::RGB);
*/
$info = $pdf->getInfo();
$info->setTitle('Test');
$info->setAuthor('Mikaël Carlavan');
$info->setCreationDate($now);

$viewer = $pdf->getViewerPreferences();
$viewer->setDisplayDocTitle(true);
//$viewer->setHideToolbar(true);
//$viewer->setHideMenubar(true);

print $pdf->build();
$pdf->save('test.pdf');

