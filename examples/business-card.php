<?php


use Papier\Papier;
use Papier\Factory\Factory;
use Papier\Graphics\DeviceColourSpace;
use Papier\Text\RenderingMode;
use Papier\Text\Encoding;

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
| Business card example
|--------------------------------------------------------------------------
|
| This script shows how to create a business card using Papier.
|
| Attributes:
| - Leaf image: Pixel perfect (https://www.flaticon.com/free-icons/leaf)
| - Lato font: tyPoland Lukasz Dziedzic (team@latofonts.com)
*/

require __DIR__.'/../vendor/autoload.php';

$pathToRegularLatoFontFile = 'fonts/Lato-Regular.ttf';
$pathToBoldLatoFontFile = 'fonts/Lato-Bold.ttf';
$pathToItalicLatoFontFile = 'fonts/Lato-Italic.ttf';

$pathToLogo = 'images/leaf.png';
$pathToQrCode = 'images/qr-code.png';

$pdf = new Papier();
$pdf->getHeader()->setVersion(7);

// Front
$front = $pdf->addPage([89, 51]);

$logo = $pdf->addImageComponent();
$logo->setSource($pathToLogo);
$logo->setWidth(25);
$logo->translate(89/2 - 25/2, 51/2 - 25/2); // Lower-left of page is (0,0) coordinate

// Back
$back = $pdf->addPage([89, 51]);

$boldFont = Factory::create('Papier\Type\TrueTypeFontDictionaryType', null, true);
$boldFont->load($pathToBoldLatoFontFile);
$boldFont->setEncoding(Encoding::WIN_ANSI);

$regularFont = Factory::create('Papier\Type\TrueTypeFontDictionaryType', null, true);
$regularFont->load($pathToRegularLatoFontFile);
$regularFont->setEncoding(Encoding::WIN_ANSI);

$italicFont = Factory::create('Papier\Type\TrueTypeFontDictionaryType', null, true);
$italicFont->load($pathToItalicLatoFontFile);
$italicFont->setEncoding(Encoding::WIN_ANSI);

$name = $pdf->addTextComponent();
$name->setNonStrokingColor(0, 0, 0);
$name->setNonStrokingColorSpace(DeviceColourSpace::RGB);
$name->setRenderingMode(RenderingMode::FILL);
$name->setText('Mikaël Carlavan');
$name->setFont($boldFont);
$name->setFontSize(5);
$name->translate(8, 41);

$job = $pdf->addTextComponent();
$job->setNonStrokingColor(0, 0, 0);
$job->setNonStrokingColorSpace(DeviceColourSpace::RGB);
$job->setRenderingMode(RenderingMode::FILL);
$job->setText('PHP developer');
$job->setFont($italicFont);
$job->setFontSize(3.5);
$job->translate(8, 36);

$email = $pdf->addTextComponent();
$email->setNonStrokingColor(0, 0, 0);
$email->setNonStrokingColorSpace(DeviceColourSpace::RGB);
$email->setRenderingMode(RenderingMode::FILL);
$email->setText('mikael@camina.dev');
$email->setFont($regularFont);
$email->setFontSize(3.5);
$email->translate(8, 31);

// Add nice circles on top-right of the card
for ($nCircle = 1; $nCircle <= 3; $nCircle++) {
	$circle = $pdf->addCircleComponent();
	$circle->setStrokingColor(0.3 * $nCircle, 1 - 0.3 * $nCircle, 0);
	$circle->setStrokingColorSpace(DeviceColourSpace::RGB);
	$circle->setRenderingMode(RenderingMode::STROKE);
	$circle->setCenterPoint(89, 51);
	$circle->setRadius(5 + $nCircle * 5);
	$circle->setLineWidth(2.5);
}

// Add qr code
$qrCode = $pdf->addImageComponent();
$qrCode->setSource($pathToQrCode);
$qrCode->setWidth(20);
$qrCode->translate(8, 6); // Lower-left of page is (0,0) coordinate


$info = $pdf->getInfo();
$info->setTitle('Business Card');
$info->setProducer('Papier');
$info->setAuthor('Mikaël Carlavan');

$viewer = $pdf->getViewerPreferences();
$viewer->setDisplayDocTitle(true);

$pdf->save('business-card.pdf');
