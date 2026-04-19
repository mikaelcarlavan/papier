<?php

/**
 * Example 04: Images
 *
 * Demonstrates embedding JPEG and PNG images, alpha/transparency,
 * image scaling, tiling, and loading from a file path.
 * Sample images are generated in memory via the GD extension
 * (bundled with PHP by default — no external files needed).
 * The final section loads unsplash.png from disk via Image::fromFile().
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\{Color, Image, Line, Rectangle, Text};

if (!extension_loaded('gd')) {
    echo "SKIP: example 04 requires the GD extension.\n";
    exit(0);
}

$doc  = PdfDocument::create();
$doc->setTitle('Images');
$f1   = $doc->addFont('Helvetica-Bold');
$page = $doc->addPage();

$page->add(
    Text::write('Image Embedding Showcase')->at(72, 800)->font($f1, 20),
);

// ── Helper: create a JPEG byte string ────────────────────────────────────────
$makeJpeg = function (int $w, int $h, array $rgb): string {
    $img = imagecreatetruecolor($w, $h);
    for ($x = 0; $x < $w; $x++) {
        for ($y = 0; $y < $h; $y++) {
            $t = ($x + $y) / ($w + $h);
            $r = (int) ($rgb[0] * $t + 255 * (1 - $t));
            $g = (int) ($rgb[1] * $t + 255 * (1 - $t));
            $b = (int) ($rgb[2] * $t + 255 * (1 - $t));
            imagesetpixel($img, $x, $y, imagecolorallocate($img, $r, $g, $b));
        }
    }
    ob_start();
    imagejpeg($img, null, 85);
    imagedestroy($img);
    return (string) ob_get_clean();
};

// ── Helper: create a PNG byte string ─────────────────────────────────────────
$makePng = function (int $w, int $h, bool $alpha = false): string {
    if ($alpha) {
        $img = imagecreatetruecolor($w, $h);
        imagesavealpha($img, true);
        imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));
        $cx = $w / 2; $cy = $h / 2; $r = min($w, $h) / 2;
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $dist = sqrt(($x - $cx) ** 2 + ($y - $cy) ** 2);
                if ($dist < $r) {
                    $a = (int) (127 * $dist / $r);
                    imagesetpixel($img, $x, $y, imagecolorallocatealpha($img, 50, 150, 255, $a));
                }
            }
        }
    } else {
        $img = imagecreatetruecolor($w, $h);
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $even = ((intdiv($x, 16) + intdiv($y, 16)) % 2 === 0);
                $c    = $even
                    ? imagecolorallocate($img, 200, 50, 50)
                    : imagecolorallocate($img, 255, 200, 200);
                imagesetpixel($img, $x, $y, $c);
            }
        }
    }
    ob_start();
    imagepng($img);
    imagedestroy($img);
    return (string) ob_get_clean();
};

// ── Section 1: JPEG image ─────────────────────────────────────────────────────
$page->add(
    Text::write('JPEG image (150×100 pt, blue gradient)')->at(72, 770)->font($f1, 11),
    Image::fromJpeg($makeJpeg(300, 200, [50, 100, 200]))->at(72, 640)->size(150, 100),
    Text::write('Blue gradient JPEG, 150×100 pt')->at(72, 630)->font($f1, 9)->rgb(0.4,0.4,0.4),
);

// ── Section 2: PNG (opaque) ───────────────────────────────────────────────────
$page->add(
    Text::write('PNG image (150×150 pt)')->at(250, 770)->font($f1, 11),
    Image::fromPng($makePng(128, 128, false))->at(250, 620)->size(150, 150),
    Text::write('Checkerboard PNG, 150×150 pt')->at(250, 610)->font($f1, 9)->rgb(0.4,0.4,0.4),
);

// ── Section 3: PNG with alpha channel ────────────────────────────────────────
$page->add(
    Text::write('PNG with transparency (alpha channel)')->at(72, 595)->font($f1, 11),
    Rectangle::create(72, 490, 200, 100)->fill(Color::rgb(0.9, 0.9, 0.2)),
    Image::fromPng($makePng(200, 100, true))->at(72, 490)->size(200, 100),
    Text::write('Semi-transparent disc over yellow background')->at(72, 480)->font($f1, 9)->rgb(0.4,0.4,0.4),
);

// ── Section 4: Same image at different scales ─────────────────────────────────
$page->add(
    Text::write('Same image at different scales:')->at(72, 460)->font($f1, 11),
);

$smallPng = $makePng(64, 64, false);
$x = 72;
foreach ([30, 50, 80, 120] as $size) {
    $page->add(
        Image::fromPng($smallPng)->at($x, 370)->size($size, $size),
        Text::write("{$size}pt")->at($x, 365)->font($f1, 8),
    );
    $x += $size + 20;
}

// ── Section 5: fitWidth / fitHeight helpers ───────────────────────────────────
$page->add(
    Line::from(72, 345)->to(523, 345)->color(Color::gray(0.7))->width(0.5),
    Text::write('fitWidth() and fitHeight() — proportional scaling:')->at(72, 333)->font($f1, 11),
);

$wideJpeg = $makeJpeg(400, 120, [200, 80, 30]);
$tallJpeg = $makeJpeg(120, 300, [30, 150, 80]);

$page->add(
    Image::fromJpeg($wideJpeg)->at(72, 240)->fitWidth(160),
    Text::write('fitWidth(160)')->at(72, 233)->font($f1, 8)->rgb(0.4,0.4,0.4),

    Image::fromJpeg($tallJpeg)->at(250, 200)->fitHeight(80),
    Text::write('fitHeight(80)')->at(250, 193)->font($f1, 8)->rgb(0.4,0.4,0.4),

    Image::fromJpeg($wideJpeg)->at(350, 240)->size(140, 42)->opacity(0.4),
    Text::write('opacity(0.4)')->at(350, 233)->font($f1, 8)->rgb(0.4,0.4,0.4),
);

// ── Section 6: Load image from file path ─────────────────────────────────────
$page->add(
    Line::from(72, 220)->to(523, 220)->color(Color::gray(0.7))->width(0.5),
    Text::write('Image::fromFile() — load JPEG or PNG from a path:')->at(72, 208)->font($f1, 11),
);

$unsplashPath = __DIR__ . '/unsplash.png';
if (file_exists($unsplashPath)) {
    $page->add(
        Image::fromFile($unsplashPath)->at(72, 80)->fitWidth(200),
        Text::write('unsplash.png via fromFile(), fitted to 200 pt wide')->at(72, 72)->font($f1, 8)->rgb(0.4, 0.4, 0.4),
    );
} else {
    $page->add(
        Text::write('(unsplash.png not found — skipped)')->at(72, 160)->font($f1, 10)->rgb(0.6, 0.0, 0.0),
    );
}

$doc->save(__DIR__ . '/output/04_images.pdf');
echo "Created: 04_images.pdf\n";
