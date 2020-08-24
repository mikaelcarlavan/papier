<?php

use Papier\Papier;

/**
 * Papier - Yet another PHP Framework For PDF
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

$page = $pdf->addPage();
$page->setMediaBox([0, 0, 612, 792]);
$contents = $page->getContents();

$contents->setNonStrokingRGBColour(0.1, 0.3, 0.8);

$contents->beginText();
$contents->setFont('F1', 24);
$contents->moveToNextLineStartWithOffset(100, 500);
$contents->showText('Hello World !');
$contents->endText();

$info = $pdf->getInfo();
$info->setTitle('Test');
$info->setAuthor('Mikael Carlavan');
$info->setCreationDate($now);

print $pdf->build();

$pdf->save('test.pdf');