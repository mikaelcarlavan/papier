<?php

use Papier\Factory\Factory;

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

$val1OfKid1 = Factory::getInstance()->createObject('Integer');
$val2OfKid1 = Factory::getInstance()->createObject('Integer');
$val3OfKid1 = Factory::getInstance()->createObject('Integer');

$val1OfKid2 = Factory::getInstance()->createObject('Integer');
$val2OfKid2 = Factory::getInstance()->createObject('Integer');

$root = Factory::getInstance()->createType('TreeNode')->setRoot();

$kid1 = Factory::getInstance()->createType('TreeNode')->setIndirect();
$kid2 = Factory::getInstance()->createType('TreeNode')->setIndirect();
$kid0 = Factory::getInstance()->createType('TreeNode')->setIndirect();

$namesOfKid1 = $kid1->getNames();
$namesOfKid2 = $kid2->getNames();

$val1OfKid1->setValue(30)->setIndirect();
$val2OfKid1->setValue(40)->setIndirect();
$val3OfKid1->setValue(50)->setIndirect();

$val1OfKid2->setValue(80)->setIndirect();
$val2OfKid2->setValue(90)->setIndirect();

$namesOfKid1->setObjectForKey('Actinium', $val1OfKid1);
$namesOfKid1->setObjectForKey('Argon', $val2OfKid1);
$namesOfKid1->setObjectForKey('Arsenic', $val3OfKid1);

$namesOfKid2->setObjectForKey('Zinc', $val1OfKid2);
$namesOfKid2->setObjectForKey('Xenon', $val2OfKid2);

$kids = $root->getKids();
$kids->append($kid0);

$kids2 = $kid0->getKids();
$kids2->append($kid1);
$kids2->append($kid2);

print $root->getObject();

print "\r\n\n";
print $kid0->getObject();


