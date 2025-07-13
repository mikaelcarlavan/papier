<?php

/**
 * Papier - Yet another PHP Framework for PDF
 *
 * @package  Papier
 * @author   Mikael Carlavan <mikael@camina.dev>
 */


use Papier\Papier;
use Papier\Factory\Factory;
use Papier\Graphics\DeviceColourSpace;
use Papier\Text\Encoding;
use Papier\Text\RenderingMode;
use Papier\Type\ContentStreamType;
use Papier\Text\TextAlign;


define('PAPIER_START', microtime(true));
date_default_timezone_set("UTC");

/*
|--------------------------------------------------------------------------
| Letter example
|--------------------------------------------------------------------------
|
| This script shows how to create a letter with A4 format (210x297 mm).
|
| Attributes:
| - Lato font: tyPoland Lukasz Dziedzic (team@latofonts.com)
*/

require __DIR__.'/../vendor/autoload.php';


$pathToRegularLatoFontFile = 'fonts/Lato-Regular.ttf';

$margin = 20;

$pageWidth = 210;
$pageHeight = 297;

$pdf = new Papier();
$pdf->getHeader()->setVersion(7);

$font = Factory::create('Papier\Type\TrueTypeFontDictionaryType', null, true);
$font->load($pathToRegularLatoFontFile);
$font->setEncoding(Encoding::WIN_ANSI);

$page = $pdf->addPage([$pageWidth, $pageHeight]);

$address = array(
	'Mikael Carlavan',
	'One Apple Park Way,',
	'Cupertino, CA 95014,',
	'United-States'
);

$subject = 'Subject: lorem ipsum dolor sit amet';
$lorem = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque tempus lobortis blandit. Donec luctus suscipit eros a suscipit. Ut rhoncus accumsan imperdiet. Donec mattis sem id dolor dignissim, ac viverra metus maximus. Nunc gravida vestibulum ante, ut blandit nibh. Proin dapibus condimentum augue. Ut magna orci, convallis eget est sed, pharetra scelerisque ipsum. Sed bibendum laoreet leo, quis aliquam risus fermentum in. Mauris laoreet justo urna, vitae ornare nulla suscipit et. Phasellus ac ultricies purus. Donec magna felis, eleifend non neque non, consectetur scelerisque purus. Aliquam erat volutpat. In convallis nisi sed diam consectetur pellentesque. Nullam sed quam et dui vehicula posuere. Curabitur lobortis nisl in tincidunt iaculis. Proin facilisis egestas dolor ut finibus. Morbi ultrices vel justo malesuada aliquam. Sed vitae lobortis quam. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque orci sem, tempor in neque non, scelerisque maximus sem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Quisque ultricies nisi at mattis iaculis. Aliquam non finibus tortor. Suspendisse potenti. Nulla fermentum turpis nisi. Aliquam erat volutpat. Donec egestas tempor ornare. Cras sagittis, massa placerat semper lacinia, libero ante tincidunt lectus, a accumsan nisi lacus eu est. Nam non magna id sem cursus fermentum. Quisque sodales tristique est eu eleifend. Mauris fermentum felis sit amet dolor eleifend, vel tristique enim hendrerit. Cras eu nibh aliquet, tempor tortor at, placerat leo. Vivamus sagittis eros id erat blandit, viverra efficitur ex venenatis. Nulla mauris felis, vehicula non urna non, lacinia dignissim odio. Mauris sodales eleifend neque, vel dignissim augue laoreet vitae. Vestibulum posuere arcu metus, viverra fermentum mauris pellentesque sed. Maecenas blandit viverra varius. In consectetur odio quis bibendum suscipit.";

$spaceY = 2;
$currentY = $pageHeight - $margin;

$addressFontSize = 5;

foreach ($address as $item) {
	$text = $pdf->addTextComponent();
	$text->setNonStrokingColor(60/255, 60/255, 60/255);
	$text->setNonStrokingColorSpace(DeviceColourSpace::RGB);
	$text->setRenderingMode(RenderingMode::FILL);
	$text->setText($item);
	$text->setFont($font);
	$text->setFontSize($addressFontSize);
	$text->setXY($margin, $currentY);

	$currentY -= ($text->estimateHeight() + $spaceY);
}

$currentY -= 20;

$text = $pdf->addTextComponent();
$text->setNonStrokingColor(60/255, 60/255, 60/255);
$text->setNonStrokingColorSpace(DeviceColourSpace::RGB);
$text->setRenderingMode(RenderingMode::FILL);
$text->setText($subject);
$text->setFont($font);
$text->setFontSize(5);
$text->setXY($margin, $currentY);

// Main text
$currentY -= 20;

$text = $pdf->addTextComponent();
$text->setNonStrokingColor(60/255, 60/255, 60/255);
$text->setNonStrokingColorSpace(DeviceColourSpace::RGB);
$text->setRenderingMode(RenderingMode::FILL);
$text->setText($lorem);
$text->setFont($font);
$text->setFontSize(5);
$text->setXY($margin, $currentY);
$text->setInterlineSpacing(1.5);
$text->setWidth($pageWidth - (2 * $margin));
$text->setTextAlign(\Papier\Text\TextAlign::LEFT);

// Sign
$currentY -= $text->estimateHeight();
$currentY -= 20;

$text = $pdf->addTextComponent();
$text->setNonStrokingColor(60/255, 60/255, 60/255);
$text->setNonStrokingColorSpace(DeviceColourSpace::RGB);
$text->setRenderingMode(RenderingMode::FILL);
$text->setText('Mikaël Carlavan');
$text->setFont($font);
$text->setFontSize(5);
$text->setXY($margin, $currentY);
$text->setWidth($pageWidth - (2 * $margin));
$text->setTextAlign(\Papier\Text\TextAlign::RIGHT);

$pageLabels = $pdf->getPageLabels();
$pageLabels->addLabel(0, \Papier\Document\NumberingStyle::LOWERCASE_ROMAN);

$info = $pdf->getInfo();
$info->setTitle('Letter');
$info->setProducer('Papier');
$info->setAuthor('Mikaël Carlavan');

$viewer = $pdf->getViewerPreferences();
$viewer->setDisplayDocTitle(true);

$pdf->save('letter-a4.pdf');

