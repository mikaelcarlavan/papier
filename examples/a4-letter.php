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

