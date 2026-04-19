<?php

/**
 * Example 12: Multimedia — Sound, Movie, and Screen annotations
 *
 * Demonstrates three ways to embed or reference media in a PDF:
 *
 *   1. SoundAnnotation  — audio clip triggered by clicking an icon
 *   2. MovieAnnotation  — legacy video with activation controls
 *   3. ScreenAnnotation — modern media playback via Rendition (PDF 1.5+)
 *
 * The demo generates synthetic audio (a sine-wave burst) and creates
 * external file references for video (no actual video file is required
 * to generate the PDF; viewers will report "file not found" for the
 * video clips unless real files are placed next to the PDF).
 *
 * Page decoration uses the elements API; media objects use the Multimedia API.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\{Color, Line, Rectangle, Text};
use Papier\Annotation\{MovieAnnotation, ScreenAnnotation, SoundAnnotation};
use Papier\Multimedia\{
    MediaClip, MediaPlayParams, MediaRendition,
    MovieActivation, MovieDictionary, SoundStream,
};

$doc  = PdfDocument::create();
$doc->setTitle('Multimedia Demo');
$f1   = $doc->addFont('Helvetica');
$f2   = $doc->addFont('Helvetica-Bold');
$page = $doc->addPage();

// ── Page header ───────────────────────────────────────────────────────────────
$page->add(
    Rectangle::create(0, 780, 595, 61)->fill(Color::rgb(0.1, 0.2, 0.45)),
    Text::write('Papier PDF — Multimedia Demo')
        ->at(72, 800)->font($f2, 20)->color(Color::white()),
);

// ── Section 1: SoundAnnotation ────────────────────────────────────────────────
$page->add(
    Text::write('1. Sound Annotation')->at(72, 755)->font($f2, 13)->color(Color::rgb(0.1, 0.2, 0.45)),
    Line::from(72, 750)->to(523, 750)->color(Color::rgb(0.1, 0.2, 0.45))->width(0.5),
    Text::write('A Sound annotation embeds raw audio in the PDF. Click the speaker icon to play.')
        ->at(72, 733)->font($f1, 10)->rgb(0.2, 0.2, 0.2),
);

// Generate a 0.5-second 440 Hz sine wave (signed 16-bit PCM, 44100 Hz, mono)
$sampleRate = 44100;
$duration   = 0.5;
$frequency  = 440.0;
$numSamples = (int) ($sampleRate * $duration);
$pcmData    = '';
for ($i = 0; $i < $numSamples; $i++) {
    $angle    = 2.0 * M_PI * $frequency * $i / $sampleRate;
    $envelope = min(1.0, min($i / 200, ($numSamples - $i) / 200)); // fade in/out
    $sample   = (int) (28000 * $envelope * sin($angle));
    $pcmData .= pack('v', $sample & 0xFFFF);  // little-endian int16
}

$soundStream = SoundStream::fromPcm($pcmData, $sampleRate, 1, 16);

$soundAnnot = new SoundAnnotation(72, 695, 92, 715);
$soundAnnot->setSound($soundStream->getStream())
           ->setIcon('Speaker')
           ->setContents('440 Hz tone (0.5 s, 44100 Hz PCM)')
           ->setColor(0.1, 0.5, 0.8);
$page->addAnnotation($soundAnnot);

$page->add(
    Text::write('← Click the speaker icon to play the embedded 440 Hz tone')
        ->at(98, 703)->font($f1, 10)->rgb(0.3, 0.3, 0.3),
);

// ── Section 2: MovieAnnotation (legacy) ───────────────────────────────────────
$page->add(
    Text::write('2. Movie Annotation (legacy §13.4)')
        ->at(72, 675)->font($f2, 13)->color(Color::rgb(0.1, 0.2, 0.45)),
    Line::from(72, 670)->to(523, 670)->color(Color::rgb(0.1, 0.2, 0.45))->width(0.5),
    Text::write(
        'Movie annotations (PDF 1.2) reference an external video file. '
      . 'Place "sample.mp4" next to the PDF to enable playback.'
    )->at(72, 653)->font($f1, 10)->rgb(0.2, 0.2, 0.2),
);

// Visual placeholder for the movie frame
$page->add(
    Rectangle::create(72, 560, 200, 80)->fill(Color::rgb(0.05, 0.05, 0.05)),
    Text::write('▶  sample.mp4')->at(100, 595)->font($f2, 12)->color(Color::white()),
    Text::write('1280 × 720 px — click to play')
        ->at(72, 553)->font($f1, 8)->rgb(0.4, 0.4, 0.4),
);

$movieDict = MovieDictionary::fromFile('sample.mp4')
    ->setAspect(1280, 720)
    ->setPoster(true);

$movieActivation = (new MovieActivation())
    ->setVolume(1.0)
    ->setShowControls(true)
    ->setMode(MovieActivation::MODE_ONCE);

$movieAnnot = new MovieAnnotation(72, 560, 272, 640);
$movieAnnot->setTitle('Sample Movie')
           ->setMovie($movieDict->getDictionary())
           ->setActivation($movieActivation->getDictionary());
$page->addAnnotation($movieAnnot);

// ── Section 3: ScreenAnnotation + Rendition (modern, PDF 1.5+) ───────────────
$page->add(
    Text::write('3. Screen Annotation + Rendition (modern §13.2)')
        ->at(72, 540)->font($f2, 13)->color(Color::rgb(0.1, 0.2, 0.45)),
    Line::from(72, 535)->to(523, 535)->color(Color::rgb(0.1, 0.2, 0.45))->width(0.5),
    Text::write(
        'Screen annotations (PDF 1.5) use a Rendition action for richer control: '
      . 'MIME type, playback params, and auto-play. '
      . 'Place "intro.mp4" and "background.mp3" next to the PDF.'
    )->at(72, 518)->font($f1, 10)->rgb(0.2, 0.2, 0.2),
);

// ── 3a. Video via ScreenAnnotation ───────────────────────────────────────────
$page->add(
    Rectangle::create(72, 400, 220, 110)->fill(Color::rgb(0.05, 0.05, 0.05)),
    Text::write('▶  intro.mp4')->at(100, 450)->font($f2, 12)->color(Color::white()),
    Text::write('Video — click to play')->at(72, 393)->font($f1, 8)->rgb(0.4, 0.4, 0.4),
);

$videoClip   = MediaClip::fromFile('intro.mp4', 'video/mp4', 'Intro Video');
$videoParams = (new MediaPlayParams())
    ->setVolume(90)
    ->setShowControls(true)
    ->setAutoPlay(false)
    ->setFitStyle(1);  // meet — proportional fit
$videoRendition = new MediaRendition($videoClip, 'Intro Video');
$videoRendition->setPlayParams($videoParams);

$videoScreen = new ScreenAnnotation(72, 400, 292, 510);
$videoScreen->setTitle('Intro Video')
            ->setRendition($videoRendition->getDictionary());
$page->addAnnotation($videoScreen);

// ── 3b. Audio via ScreenAnnotation ───────────────────────────────────────────
$page->add(
    Rectangle::create(320, 435, 200, 40)->fill(Color::rgb(0.15, 0.15, 0.15)),
    Text::write('♫  background.mp3')->at(330, 450)->font($f2, 11)->color(Color::white()),
    Text::write('Audio — auto-plays on page open')
        ->at(320, 428)->font($f1, 8)->rgb(0.4, 0.4, 0.4),
);

$audioClip   = MediaClip::fromFile('background.mp3', 'audio/mpeg', 'Background Music');
$audioParams = (new MediaPlayParams())
    ->setVolume(60)
    ->setShowControls(false)
    ->setAutoPlay(true)
    ->setRepeatCount(0.0);  // loop forever
$audioRendition = new MediaRendition($audioClip, 'Background Music');
$audioRendition->setPlayParams($audioParams);

$audioScreen = new ScreenAnnotation(320, 435, 520, 475);
$audioScreen->setTitle('Background Music')
            ->setRendition($audioRendition->getDictionary());
$page->addAnnotation($audioScreen);

// ── Summary ───────────────────────────────────────────────────────────────────
$page->add(
    Line::from(72, 380)->to(523, 380)->color(Color::gray(0.7))->width(0.5),
    Text::write('Media types supported by this library:')
        ->at(72, 365)->font($f2, 10),
    Text::write('SoundAnnotation — raw PCM, μ-law, A-law audio embedded in the PDF (§13.3)')
        ->at(82, 350)->font($f1, 9)->rgb(0.2, 0.2, 0.2),
    Text::write('MovieAnnotation — external video with activation controls (§13.4, PDF 1.2+)')
        ->at(82, 336)->font($f1, 9)->rgb(0.2, 0.2, 0.2),
    Text::write('ScreenAnnotation + Rendition — video/audio via MIME-typed renditions (§13.2, PDF 1.5+)')
        ->at(82, 322)->font($f1, 9)->rgb(0.2, 0.2, 0.2),
    Text::write('FileSpec supports both external file references and embedded (self-contained) media')
        ->at(82, 308)->font($f1, 9)->rgb(0.2, 0.2, 0.2),
);

$doc->save(__DIR__ . '/output/12_multimedia.pdf');
echo "Created: 12_multimedia.pdf\n";
echo "Note: video/audio clips are referenced externally; place sample.mp4, intro.mp4,\n";
echo "      and background.mp3 alongside the PDF to enable playback in a compatible viewer.\n";
