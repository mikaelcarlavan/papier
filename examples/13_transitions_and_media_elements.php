<?php

/**
 * Example 13: Page transitions and media elements
 *
 * Demonstrates:
 *   - PageTransition — transition effects in presentation mode
 *   - PdfPage::setDuration() — auto-advance timing
 *   - SoundElement — inline audio with click-to-play icon (annotation-backed)
 *   - VideoElement — video placeholder with ScreenAnnotation + Rendition
 *
 * Open the output PDF in Adobe Acrobat's full-screen (Ctrl+L / ⌘L) mode to
 * see the transition effects and auto-advance.  Place sample media files
 * (see notes at end) alongside the PDF to enable audio/video playback.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\{Color, Line, Rectangle, SoundElement, Text, VideoElement};
use Papier\Multimedia\{MediaClip, MediaPlayParams, SoundStream};
use Papier\Structure\{PdfPage, PageTransition};

$doc = PdfDocument::create();
$doc->setTitle('Transitions & Media Elements');
$f1  = $doc->addFont('Helvetica');
$f2  = $doc->addFont('Helvetica-Bold');

// ── Helper: add a standard slide header ──────────────────────────────────────
$addHeader = function (string $title, string $subtitle = '') use ($f1, $f2): \Closure {
    return function (\Papier\Structure\PdfPage $page) use ($title, $subtitle, $f1, $f2): void {
        $items = [
            Rectangle::create(0, 780, 595, 61)->fill(Color::rgb(0.12, 0.22, 0.42)),
            Text::write($title)->at(72, 802)->font($f2, 18)->color(Color::white()),
        ];
        if ($subtitle !== '') {
            $items[] = Text::write($subtitle)->at(72, 784)->font($f1, 11)->color(Color::rgb(0.7, 0.8, 1.0));
        }
        $page->add(...$items);
    };
};

// ════════════════════════════════════════════════════════════════════
// Slide 1: Title slide with Dissolve transition
// ════════════════════════════════════════════════════════════════════
$page1 = $doc->addPage();
$page1->add(
    Rectangle::create(0, 0, 595, 841)->fill(Color::rgb(0.05, 0.1, 0.22)),
    Text::write('Papier PDF Library')->at(72, 560)->font($f2, 36)->color(Color::white()),
    Text::write('Media Elements & Page Transitions')->at(72, 520)->font($f1, 18)->color(Color::rgb(0.6, 0.75, 1.0)),
    Line::from(72, 510)->to(523, 510)->color(Color::rgb(0.3, 0.5, 0.9))->width(1.0),
    Text::write('Press Ctrl+L (⌘L on macOS) to enter full-screen presentation mode')
        ->at(72, 490)->font($f1, 10)->color(Color::rgb(0.5, 0.6, 0.8)),
);
$page1->setTransition(new PageTransition(PageTransition::DISSOLVE, 1.0));
$page1->setDuration(5.0);

// ════════════════════════════════════════════════════════════════════
// Slide 2: SoundElement demo (Wipe transition)
// ════════════════════════════════════════════════════════════════════
$page2 = $doc->addPage();
$addHeader('Sound Element', 'Embedded PCM audio triggered by a click-to-play icon')($page2);

$page2->add(
    Text::write('SoundElement renders a clickable icon and automatically registers')
        ->at(72, 740)->font($f1, 11)->rgb(0.1, 0.1, 0.1),
    Text::write('a SoundAnnotation with the page — no manual addAnnotation() call needed.')
        ->at(72, 724)->font($f1, 11)->rgb(0.1, 0.1, 0.1),

    Text::write('Generated 440 Hz tone (0.5 s):')
        ->at(72, 695)->font($f2, 11)->rgb(0.1, 0.2, 0.4),
    Text::write('Generated 880 Hz tone (0.25 s):')
        ->at(72, 655)->font($f2, 11)->rgb(0.1, 0.2, 0.4),
    Text::write('Click the icon to play each sound.')
        ->at(72, 580)->font($f1, 10)->rgb(0.4, 0.4, 0.4),
);

// Build two sine-wave tones and embed them as SoundElements
$makeTone = static function (float $hz, float $dur): SoundStream {
    $sampleRate = 44100;
    $numSamples = (int) ($sampleRate * $dur);
    $pcm        = '';
    for ($i = 0; $i < $numSamples; $i++) {
        $env    = min(1.0, min($i / 200.0, ($numSamples - $i) / 200.0));
        $sample = (int) (28000 * $env * sin(2.0 * M_PI * $hz * $i / $sampleRate));
        $pcm   .= pack('v', $sample & 0xFFFF);
    }
    return SoundStream::fromPcm($pcm, $sampleRate, 1, 16);
};

$page2->add(
    SoundElement::create($makeTone(440, 0.5), 72, 670)
        ->size(22)
        ->icon('Speaker')
        ->color(Color::rgb(0.15, 0.45, 0.8))
        ->contents('440 Hz tone, 0.5 s'),

    SoundElement::create($makeTone(880, 0.25), 72, 630)
        ->size(22)
        ->icon('Speaker')
        ->color(Color::rgb(0.6, 0.2, 0.6))
        ->contents('880 Hz tone, 0.25 s'),
);

$page2->setTransition(
    (new PageTransition(PageTransition::WIPE, 0.6))->setDirection(0) // left → right
);
$page2->setDuration(8.0);

// ════════════════════════════════════════════════════════════════════
// Slide 3: VideoElement demo (Box transition, outward)
// ════════════════════════════════════════════════════════════════════
$page3 = $doc->addPage();
$addHeader('Video Element', 'ScreenAnnotation + MediaRendition via the elements API')($page3);

$page3->add(
    Text::write('VideoElement draws a placeholder rectangle and attaches a ScreenAnnotation')
        ->at(72, 740)->font($f1, 11)->rgb(0.1, 0.1, 0.1),
    Text::write('with a RenditionAction.  Place the referenced files alongside the PDF.')
        ->at(72, 724)->font($f1, 11)->rgb(0.1, 0.1, 0.1),
);

// Video clip (external reference)
$videoClip = MediaClip::fromFile('demo.mp4', 'video/mp4', 'Demo Video');
$page3->add(
    VideoElement::create($videoClip, 72, 490, 220, 138)
        ->bgColor(Color::rgb(0.05, 0.05, 0.05))
        ->label('▶  demo.mp4', $f2, 11)
        ->title('Demo Video')
        ->playParams(
            (new MediaPlayParams())
                ->setVolume(90)
                ->setShowControls(true)
                ->setAutoPlay(false)
                ->setFitStyle(1)     // meet — proportional fit
        ),
);

// Audio clip alongside
$audioClip = MediaClip::fromFile('background.mp3', 'audio/mpeg', 'Background');
$page3->add(
    VideoElement::create($audioClip, 320, 510, 200, 50)
        ->bgColor(Color::rgb(0.12, 0.12, 0.12))
        ->label('♫  background.mp3', $f1, 10)
        ->title('Background Audio')
        ->playParams(
            (new MediaPlayParams())
                ->setVolume(60)
                ->setShowControls(false)
                ->setAutoPlay(true)
                ->setRepeatCount(0.0)  // loop forever
        ),

    Text::write('Video (click to play)')->at(72, 483)->font($f1, 8)->rgb(0.5, 0.5, 0.5),
    Text::write('Audio (auto-plays)')->at(320, 503)->font($f1, 8)->rgb(0.5, 0.5, 0.5),
);

$page3->setTransition(
    (new PageTransition(PageTransition::BOX, 0.8))->setMotion(PageTransition::MOTION_OUT)
);
$page3->setDuration(8.0);

// ════════════════════════════════════════════════════════════════════
// Slide 4: Transition gallery (Glitter)
// ════════════════════════════════════════════════════════════════════
$page4 = $doc->addPage();
$addHeader('Transition Gallery', 'All transition styles defined in ISO 32000-1 §12.4.4')($page4);

$transitions = [
    [PageTransition::REPLACE,  'R',       'REPLACE  — instant page swap (default)'],
    [PageTransition::DISSOLVE, 'Dissolve','DISSOLVE — random pixel dissolve'],
    [PageTransition::WIPE,     'Wipe',    'WIPE     — single sweeping line'],
    [PageTransition::GLITTER,  'Glitter', 'GLITTER  — glitter sweep'],
    [PageTransition::SPLIT,    'Split',   'SPLIT    — two lines sweep inward/outward'],
    [PageTransition::BLINDS,   'Blinds',  'BLINDS   — multiple parallel lines'],
    [PageTransition::BOX,      'Box',     'BOX      — rectangular box expand/contract'],
    [PageTransition::FLY,      'Fly',     'FLY      — new page flies in (PDF 1.5)'],
    [PageTransition::PUSH,     'Push',    'PUSH     — page pushed by new one (PDF 1.5)'],
    [PageTransition::COVER,    'Cover',   'COVER    — new page slides over (PDF 1.5)'],
    [PageTransition::UNCOVER,  'Uncover', 'UNCOVER  — old page slides away (PDF 1.5)'],
    [PageTransition::FADE,     'Fade',    'FADE     — cross-fade (PDF 1.5)'],
];

$y = 745;
foreach ($transitions as [$style, $short, $label]) {
    $page4->add(
        Rectangle::create(72, $y - 2, 8, 10)->fill(Color::rgb(0.2, 0.4, 0.8)),
        Text::write($label)->at(86, $y)->font($f1, 9)->rgb(0.1, 0.1, 0.1),
    );
    $y -= 16;
}

$page4->add(
    Line::from(72, $y - 4)->to(523, $y - 4)->color(Color::gray(0.75))->width(0.4),
    Text::write('Use PageTransition constants: PageTransition::DISSOLVE, ::WIPE, etc.')
        ->at(72, $y - 18)->font($f1, 9)->rgb(0.4, 0.4, 0.4),
    Text::write('$page->setTransition(new PageTransition(PageTransition::GLITTER, 0.8));')
        ->at(72, $y - 34)->font($f1, 9)->rgb(0.2, 0.4, 0.2),
);

$page4->setTransition(new PageTransition(PageTransition::GLITTER, 1.0));

// ════════════════════════════════════════════════════════════════════
$doc->save(__DIR__ . '/output/13_transitions_and_media_elements.pdf');
echo "Created: 13_transitions_and_media_elements.pdf\n";
echo "  — Open in Acrobat full-screen (Ctrl+L / ⌘L) to see transition effects.\n";
echo "  — Place demo.mp4 and background.mp3 next to the PDF for media playback.\n";
