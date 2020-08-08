<?php

use Papier\Factory\Factory;
use Papier\Filter\ASCII85Filter;

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

$val1 = Factory::getInstance()->createObject('Integer');
$val2 = Factory::getInstance()->createObject('Integer');
$val3 = Factory::getInstance()->createObject('Integer');

$root = Factory::getInstance()->createType('TreeNode');
$names = $root->getNames();

$val1->setValue(30)->setIndirect();
$val2->setValue(40)->setIndirect();
$val3->setValue(50)->setIndirect();

$names->setObjectForKey('Actinium', $val1);
$names->setObjectForKey('Argon', $val2);
$names->setObjectForKey('Arsenic', $val3);

print $root->format();

print "\r\n";
print ASCII85Filter::encode("Cequiseconcoitbiensenonceclairement");
print "\r\n";
print ASCII85Filter::decode("6Y17[BldiqDf0''BlmfuASuR#DJsE&ARfObBl[d%ASuS~>");
