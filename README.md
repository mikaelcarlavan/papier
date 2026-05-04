# Papier
<p>
    <a href="https://packagist.org/packages/papier/papier"><img src="https://poser.pugx.org/papier/papier/d/total.svg" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/papier/papier"><img src="https://poser.pugx.org/papier/papier/v/stable.svg" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/papier/papier"><img src="https://poser.pugx.org/papier/papier/license.svg" alt="License"></a>
</p>

A comprehensive PHP library for generating and reading PDF documents, implementing **ISO 32000-1:2008 (PDF 1.7)**.

Papier was built with the assistance of [Claude](https://claude.ai) (Anthropic's AI assistant), which helped design the API, implement the PDF specification, and write the examples throughout the library.

## Requirements

- PHP 8.1+
- Extensions: `ext-zlib`, `ext-mbstring`, `ext-openssl`

## Installation

```bash
composer require papier/papier
```

## What you can do

### Create documents

```php
use Papier\PdfDocument;

$doc = PdfDocument::create();
$doc->setTitle('My Document')
    ->setAuthor('Jane Doe')
    ->setSubject('A sample PDF');

$page = $doc->addPage();          // A4 by default
$doc->save('output.pdf');
```

### Text and typography

Add text with fonts, sizes, colours, and opacity. Latin characters (é, à, ü, ñ, …) work out of the box.

```php
use Papier\Elements\{Color, Text, TextBox};

$font = $doc->addFont('Helvetica');
$bold = $doc->addFont('Helvetica-Bold');

$page->add(
    Text::write('Bonjour, Ré!')->at(72, 750)->font($bold, 24)->color(Color::hex('#1a1a2e')),
    TextBox::write('Un paragraphe avec retour à la ligne automatique.')
        ->at(72, 700)->size(450, 80)
        ->font($font, 12)
        ->lineHeight(1.5),
);
```

### Embedded TrueType / OpenType fonts

```php
$lato = $doc->addFont(__DIR__ . '/Lato-Regular.ttf');

$page->add(
    Text::write('Lato at 18 pt')->at(72, 680)->font($lato, 18),
);
```

Papier extracts the PostScript name from the font's `name` table, parses metrics from `hhea` / `OS/2` / `hmtx`, and embeds the full font program as `FontFile2`.

### Images

Load JPEG or PNG images from a file path — type is auto-detected:

```php
use Papier\Elements\Image;

$page->add(
    Image::fromFile(__DIR__ . '/photo.png')->at(72, 500)->fitWidth(300),
);
```

Or from raw bytes if you already have them in memory:

```php
Image::fromJpeg(file_get_contents('photo.jpg'))->at(72, 400)->size(200, 150)->opacity(0.8),
Image::fromPng(file_get_contents('logo.png'))->at(300, 400)->fitHeight(60),
```

PNG alpha channels are handled automatically via an SMask XObject.

### Graphics

Rectangles, circles, lines, curves, complex paths, gradients, patterns, and clipping:

```php
use Papier\Elements\{Circle, Line, Rectangle};

$page->add(
    Rectangle::create(72, 400, 200, 100)->fill(Color::rgb(0.2, 0.5, 0.9))->stroke(Color::black(), 1.5),
    Circle::create(400, 450, 50)->fill(Color::hex('#e74c3c')),
    Line::from(72, 380)->to(523, 380)->color(Color::gray(0.5))->width(0.5),
);
```

For lower-level control use `ContentStream` directly (PDF operators: `re`, `m`, `l`, `c`, `Tf`, `Tj`, …).

### Tables

```php
use Papier\Elements\Table;

$table = Table::create(430, [100, 150, 180])
    ->at(72, 700)
    ->header(['Name', 'Role', 'Email'], $bold)
    ->row(['Alice', 'Engineer', 'alice@example.com'])
    ->row(['Bob',   'Designer', 'bob@example.com']);

$page->add($table);
```

Features: column widths, header/footer rows, row/cell colours, padding, border control per side, `rowspan`, vertical alignment, opacity.

### Interactive forms (AcroForm)

```php
use Papier\AcroForm\{AcroForm, TextField, CheckboxField, ComboBoxField};

$form = new AcroForm();

$name = new TextField('person.name');
$name->setRect(200, 700, 400, 718)
     ->setDefaultAppearance($font, 12)
     ->setValue('Alice');
$form->addField($name);

$doc->setAcroForm($form);
```

Supported field types: `TextField`, `CheckboxField`, `RadioButtonField`, `ComboBoxField`, `ListBoxField`, `SignatureField`.

### Annotations

```php
use Papier\Annotation\{HighlightAnnotation, LinkAnnotation, StampAnnotation};
use Papier\Elements\Color;

$link = new LinkAnnotation(72, 680, 272, 700);
$link->setURI('https://example.com')->setBorderStyle(0);
$page->addAnnotation($link);

$stamp = new StampAnnotation(390, 555, 520, 605);
$stamp->setIcon('Draft')->setColor(Color::rgb(1, 0, 0));
$page->addAnnotation($stamp);
```

13 annotation subtypes: `TextAnnotation`, `LinkAnnotation`, `FreeTextAnnotation`, `HighlightAnnotation`, `UnderlineAnnotation`, `StrikeOutAnnotation`, `SquigglyAnnotation`, `LineAnnotation`, `SquareAnnotation`, `CircleAnnotation`, `PolygonAnnotation`, `StampAnnotation`, `InkAnnotation`.

### Bookmarks (outlines)

```php
use Papier\Structure\PdfOutline;

$outline = new PdfOutline();
$ch1 = $outline->addItem('Chapter 1', $page1);
$ch1->addChild('Section 1.1', $page2);
$doc->setOutline($outline);
```

### Document-level features

```php
// Named destinations and open action
$doc->addNamedDestination('intro', $page, 72, 750);
$doc->setOpenDestination($page, 0, 841);

// Page labels (Roman numerals, letters, custom prefix…)
use Papier\Structure\PageLabelStyle;
$doc->addPageLabel(0, PageLabelStyle::RomanLower);              // i, ii, iii, …
$doc->addPageLabel(4, PageLabelStyle::Decimal);                 // 1, 2, 3, …
$doc->addPageLabel(4, PageLabelStyle::Decimal, prefix: 'p.');   // p.1, p.2, …

// File attachments
$doc->attachFile('data.json', file_get_contents('data.json'), 'application/json');

// Password protection and permissions
use Papier\Encryption\PdfEncryption;
$enc = new PdfEncryption();
$enc->setUserPassword('user')->setOwnerPassword('owner');
$doc->setEncryption($enc);
```

### Optional content (layers)

```php
use Papier\OptionalContent\{OCGroup, OCProperties};

$bg   = new OCGroup('Background');
$text = new OCGroup('Text');

$ocProps = new OCProperties();
$ocProps->addOCG($bg)->addOCG($text)
        ->setDefaultConfig('Default', on: [$bg, $text]);
$doc->setOCProperties($ocProps);
```

Wrap content in `ContentStream::beginMarkedContentProps('OC', …) / endMarkedContent()` to bind it to a layer.

### Multimedia

```php
use Papier\Multimedia\{MediaRendition, MediaPlayParams};
use Papier\Annotation\ScreenAnnotation;

$params = (new MediaPlayParams())->setVolume(80)->setAutoPlay(true)->setShowControls(true);
$rendition = new MediaRendition('video/mp4', 'demo.mp4');
$rendition->setPlayParams($params);

$screen = new ScreenAnnotation(72, 400, 372, 600);
$screen->setRendition($rendition);
$page->addAnnotation($screen);
```

### Page transitions

```php
use Papier\Structure\PageTransition;

$page->setTransition((new PageTransition())->setStyle('Fly')->setDuration(1.0));
```

### Read and parse existing PDFs

```php
use Papier\Parser\PdfParser;

$parser = new PdfParser('document.pdf');

echo $parser->getPageCount();                  // number of pages
echo $parser->extractText();                   // all text content
echo $parser->extractTextFromPageNumber(1);    // page 1 only
print_r($parser->getFonts());                  // font list
print_r($parser->getAnnotations());            // annotations
print_r($parser->extractImages());             // embedded images
print_r($parser->getPageInfo(1));              // page dimensions, resources
print_r($parser->getMetadata());               // title, author, subject, …
```

## Examples

The `examples/` directory contains 15 runnable scripts:

| File | Demonstrates |
|------|-------------|
| `01_hello_world.php` | Minimal document creation |
| `02_text_formatting.php` | Fonts, sizes, colours, alignment |
| `03_graphics.php` | Paths, shapes, gradients, clipping |
| `04_images.php` | JPEG/PNG embedding, `fromFile()`, alpha, scaling |
| `05_forms.php` | AcroForm fields |
| `06_annotations.php` | All 13 annotation subtypes |
| `07_bookmarks_and_encryption.php` | Outlines, password protection |
| `08_advanced_graphics.php` | Patterns, shadings, transparency groups |
| `09_read_pdf.php` | Parsing an existing PDF |
| `10_optional_content_layers.php` | Togglable layers |
| `11_elements.php` | High-level element API |
| `12_multimedia.php` | Embedded audio/video |
| `13_transitions_and_media_elements.php` | Page transitions |
| `14_table.php` | Tables with rowspan, footer, per-cell borders |
| `15_ttf_fonts.php` | Embedding TTF/OTF font files |

Run any example:

```bash
php examples/01_hello_world.php
```

Output PDFs are written to `examples/output/`.

## Architecture

```
src/
├── PdfDocument.php           Entry point — document, pages, fonts, metadata
├── Elements/                 High-level fluent API (Text, TextBox, Image, Table, …)
├── Content/ContentStream.php Low-level PDF operators
├── Annotation/               13 annotation types
├── AcroForm/                 Interactive form fields
├── Font/                     Type1, TrueType, Type0/CID, font descriptors
├── Structure/                Pages, outlines, resources, transitions
├── OptionalContent/          Layers (OCG / OCProperties)
├── Multimedia/               Renditions, media play parameters
├── Encryption/               RC4 / AES password protection
├── Parser/                   PDF parser and text extractor
└── Writer/                   Low-level binary PDF writer
```

## Licence

Papier is open-sourced software licensed under the [MIT license](LICENSE.md).
