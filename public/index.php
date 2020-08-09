<?php

use Papier\Factory\Factory;
use Papier\Type\NameTreeType;
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

$tree = Factory::getInstance()->createType('NameTree');

$val1OfKid1 = Factory::getInstance()->createObject('Integer')->setValue(30);
$val2OfKid1 = Factory::getInstance()->createObject('Integer')->setValue(40);
$val3OfKid1 = Factory::getInstance()->createObject('Integer')->setValue(50);

$val1OfKid2 = Factory::getInstance()->createObject('Integer')->setValue(80);
$val2OfKid2 = Factory::getInstance()->createObject('Integer')->setValue(90);

$root = $tree->getRoot();

$kid0 = $tree->addKid();

$kid1 = $kid0->addKid();
$kid2 = $kid0->addKid();

$kid1->addName('Actinium', $val1OfKid1)->addName('Argon', $val2OfKid1)->addName('Arsenic', $val3OfKid1);
$kid2->addName('Zinc', $val1OfKid2)->addName('Xenon', $val2OfKid2);

print $kid0->format();


