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

$pdf->getBody()->getPageTree()->setMediaBox([0, 0, 595.28, 841.89]);

// $page = $pdf->getBody()->addPage();
$page = $pdf->addPage();


$graphics = Factory::create('\Papier\Type\NameType', ProcedureSet::GRAPHICS);
$text = Factory::create('\Papier\Type\NameType',  ProcedureSet::TEXT);
$imageb = Factory::create('\Papier\Type\NameType', ProcedureSet::GRAYSCALE_IMAGES);
$imagec = Factory::create('\Papier\Type\NameType', ProcedureSet::COLOUR_IMAGES);
$imagei = Factory::create('\Papier\Type\NameType', ProcedureSet::INDEXED_IMAGES);

$procset = Factory::create('\Papier\Type\ArrayType', null, true)
    ->append($imageb)
    ->append($imagec)
    ->append($imagei)
    ->append($graphics)
    ->append($text);


$image = Factory::create('\Papier\Type\ImageType', null, true);
$image->setWidth(100);
$image->setHeight(125);
$image->setColorSpace(DeviceColourSpace::RGB);
$image->setBitsPerComponent(8);
$image->setFilter(FilterType::DCT_DECODE);
$image->setContent(file_get_contents('unsplash.jpg'));


$xObject = Factory::create('\Papier\Type\DictionaryType')->setEntry('Im1', $image);


$helvetica = Factory::create('\Papier\Type\Type1FontType', null, true)
    ->setName('F1')
    ->setBaseFont('Helvetica');

$font = Factory::create('\Papier\Type\DictionaryType')->setEntry('F1', $helvetica);


$resources = $page->getResources();
$resources->setEntry('ProcSet', $procset);
$resources->setEntry('Font', $font);
$resources->setEntry('XObject', $xObject);

$contents = $page->getContents();

$contents->setNonStrokingRGBColour(0.1, 0.3, 0.8);

$contents->beginText();
$contents->setFont('F1', 24);
$contents->setCharacterSpacing(-2);
$contents->moveToNextLineStartWithOffset(100, 500);
$contents->showText('Hello World !');
$contents->endText();


$contents->setCompression(FilterType::FLATE_DECODE);
$contents->save();
$contents->setCTM(75.00, 0, 0, 93.75, 28.35, 719.79);
$contents->paintXObject('Im1');
$contents->restore();


$info = $pdf->getInfo();
$info->setTitle('Test');
$info->setAuthor('Mikaël Carlavan');
$info->setCreationDate($now);

$viewer = $pdf->getViewerPreferences();
$viewer->setDisplayDocTitle(true);
//$viewer->setHideToolbar(true);
//$viewer->setHideMenubar(true);

//print $pdf->build();
$pdf->save('test.pdf');

