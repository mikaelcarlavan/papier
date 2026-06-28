# Papier
<p>
    <a href="https://packagist.org/packages/papier/papier"><img src="https://img.shields.io/packagist/v/papier/papier" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/papier/papier"><img src="https://img.shields.io/packagist/dt/papier/papier" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/papier/papier"><img src="https://img.shields.io/packagist/l/papier/papier" alt="License"></a>
</p>

A comprehensive PHP library for generating and reading PDF documents, implementing **ISO 32000-1:2008 (PDF 1.7)** with selected **ISO 32000-2 (PDF 2.0)** features (AES-256, document timestamps).

Papier was built with the assistance of [Claude](https://claude.ai) (Anthropic's AI assistant), which helped design the API, implement the PDF specification, and write the examples throughout the library.

## Features

**Documents & pages**
- Create documents with metadata (Info dictionary + XMP), viewer preferences, page labels, named destinations, open actions
- Page sizes/orientation, rotation, multiple content streams
- Page operations: merge, extract/split, N-up, import pages from other PDFs
- Repeating elements — running headers/footers with page-rule filtering (`all`/`odd`/`even`/`first`/`last`/every-Nth/callable) and "Page X of Y"

**Text & fonts**
- 14 standard Type 1 fonts; embedded TrueType/OpenType (`FontFile2`)
- **TrueType subsetting** + automatic **`/ToUnicode`** generation (searchable, copy-pasteable)
- **Type 0 / CIDFontType2 composite fonts** (Identity-H) for full Unicode / CJK
- **Type 3** user-defined glyph fonts; WinAnsi & MacRoman encodings
- Text rendering modes, char/word spacing, horizontal scaling, rise, leading; `TextBox` word-wrap

**Graphics**
- Paths, shapes, Bézier curves, clipping; line styles, dashes
- Colour spaces: DeviceGray/RGB/CMYK, CalGray/CalRGB, Lab, ICCBased, Indexed, Separation, DeviceN, Pattern
- Functions (sampled, exponential, stitching, PostScript calculator)
- Shadings: axial, radial, and **mesh types 4–7** (Gouraud free-form/lattice, Coons, tensor)
- Patterns: tiling and **shading patterns** (fill shapes/text with gradients)
- Transparency: ExtGState, soft masks, blend modes
- Images: JPEG, PNG (with alpha → SMask), image masks, decode arrays

**Tables, forms & annotations**
- Tables: column widths, header/footer rows, rowspan, per-side borders, padding, vertical alignment, colours, opacity
- AcroForm fields (text, checkbox, radio, combo, list, push button, signature); **fill existing forms** with regenerated appearance streams
- 14 annotation subtypes incl. link, text, markup, geometric, stamp, ink, **redaction**

**Document features**
- Bookmarks/outlines, optional content (layers), multimedia (sound/screen renditions), page transitions
- File attachments; **Tagged PDF** (marked content, structure tree, `/ParentTree`) for accessibility / PDF/UA
- **PDF/A** (2b and accessible 2a) with sRGB OutputIntent + a built-in `PdfAValidator`

**Security & signatures**
- Encryption: RC4 (40/128-bit), AES-128, **AES-256** (PDF 2.0); permission flags
- **Decryption** of existing encrypted PDFs (RC4/AES-128/AES-256), user & owner passwords
- **Digital signatures** (PKCS#7/CMS detached) with visible appearance; **RFC 3161 document timestamps** (PAdES)

**File structure**
- Classic xref tables **and** cross-reference streams; **object streams** (read & write) for smaller files
- **Incremental updates** (append revisions; foundation for signing)
- Damaged-file recovery (rebuild xref by scanning when `startxref` is missing/corrupt)
- Filters: FlateDecode (+predictors), LZW, ASCIIHex, ASCII85, RunLength, **CCITT Group 4**; DCT/JBIG2/JPX pass-through

**Reading & extraction**
- Layout- and `ToUnicode`-aware text extraction (Type 0/CJK/subset fonts, recurses into form XObjects)
- Read pages, fonts, images, metadata (Info + XMP), annotations, outlines, form-field values, attachments, structure tree
- Parses object streams, hybrid-reference and linearized files; robust against malformed input

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

14 annotation subtypes: `TextAnnotation`, `LinkAnnotation`, `FreeTextAnnotation`, `HighlightAnnotation`, `UnderlineAnnotation`, `StrikeOutAnnotation`, `SquigglyAnnotation`, `LineAnnotation`, `SquareAnnotation`, `CircleAnnotation`, `PolygonAnnotation`, `StampAnnotation`, `InkAnnotation`, `RedactAnnotation`.

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

### Page operations & repeating elements

```php
// Running header/footer on every page (rendered when the total is known):
$doc->footer(fn($page, $n, $total) =>
    $page->add(Text::write("Page $n of $total")->at(480, 30)->font($font, 9)));
$doc->header(fn($page, $n, $total) => /* … */, 'odd');   // 'all'|'odd'|'even'|N|callable

// Document-level page operations:
PdfDocument::merge(['a.pdf', 'b.pdf'], 'out.pdf');        // concatenate
PdfDocument::extractPages('in.pdf', [3, 1], 'out.pdf');  // subset, reordered
PdfDocument::nUp('in.pdf', 2, 2, 'out.pdf');             // 4-up
$doc->importPages('in.pdf');                              // import all pages
$page->setRotation(90);                                   // rotate
```

### Validate your own output (PDF/A)

```php
use Papier\Validation\PdfAValidator;

$issues = PdfAValidator::validate($pdfBytes);   // [] = structurally conformant
```

Plus `php tools/verify.php` renders every example with Ghostscript and runs
`qpdf --check` to confirm the files actually display.

### Fill an existing form

```php
use Papier\AcroForm\FormFiller;

$filler = new FormFiller(file_get_contents('form.pdf'));
$filler->setText('person.name', 'Alice')
       ->setCheckbox('subscribe', true);
file_put_contents('filled.pdf', $filler->save());   // appearance regenerated, incremental
```

### Visible signatures + timestamps (PAdES)

```php
use Papier\Signature\{PdfSigner, DocumentTimestamp};

$signed = (new PdfSigner($certPem, $keyPem))
    ->setName('Alice')->setReason('Approved')
    ->setVisibleAppearance(x: 360, y: 690, w: 170, h: 60, page: 1)
    ->sign(file_get_contents('in.pdf'));

// Optional RFC 3161 document timestamp (supply your TSA transport).
$timestamped = (new DocumentTimestamp($tsaClient))->apply($signed);
```

### CCITT Group 4 (fax) images

```php
use Papier\Filter\CCITTFaxDecode;

$codec   = new CCITTFaxDecode();
$encoded = $codec->encode($bilevelBytes, $params);   // K=-1; ~10× on scans
$decoded = $codec->decode($encoded, $params);        // also reads scanned PDFs
```

### Tagged PDF / accessibility

```php
use Papier\LogicalStructure\{StructElement, StructTreeRoot};

$cs->beginMarkedContentMcid('P', 0)->beginText()/* … */->endText()->endMarkedContent();

$tree = new StructTreeRoot();
$p = new StructElement('P');
$p->addMCID(0, $page->getDictionary());
$tree->addChild($p);
$doc->setStructTree($tree);   // emits /MarkInfo, /ParentTree, /StructParents
```

Enables screen-reader support and the accessible **PDF/A-2a / PDF/UA** levels.

### Shading patterns — fill shapes & text with gradients

```php
use Papier\Graphics\Pattern\ShadingPattern;

$pat = new ShadingPattern($axialShading);            // or a radial/mesh shading
$page->getResources()->addPattern('G', $pat->getDictionary());
$cs->setFillColorSpace('Pattern')->setFillColorN('G')->drawRect(...)->fill();
```

### Type 3 (user-defined glyph) fonts

```php
use Papier\Font\Type3Font;

$f = new Type3Font();
$f->setFontBBox(0, 0, 1000, 1000);
$glyph = (new ContentStream())->drawCircle(500, 350, 350)->fill();
$f->addGlyph(65, $glyph, 1000, 'disc');              // 'A' → a disc
$name = $doc->registerFont($f, 'Icons');
```

### Recover damaged PDFs

The parser rebuilds the cross-reference by scanning for objects when
`startxref` is missing or corrupt — common with third-party files.

```php
$parser = new PdfParser($maybeBrokenBytes);
$parser->parse();        // recovers automatically; no special flag needed
```

### Full-Unicode / CJK text (Type 0 fonts)

`addFont()` produces a single-byte WinAnsi font (256 codes). For CJK, Cyrillic,
Greek, and other scripts, use `addUnicodeFont()` — it embeds the font as a
CIDFontType2 with Identity-H encoding, generates a `/ToUnicode` CMap, and subsets
unused glyphs.

```php
$noto = $doc->addUnicodeFont(__DIR__ . '/NotoSansCJK-Regular.otf');
$page->add(Text::write('日本語 / 中文 / 한국어 / Ελληνικά')->at(72, 700)->font($noto, 16));
```

### Mesh shadings (types 4–7)

```php
use Papier\Graphics\Shading\{GouraudTriangleShading, CoonsPatchShading};

$tri = new GouraudTriangleShading('DeviceRGB');
$tri->addTriangle([72, 600], [1,0,0], [222, 600], [0,1,0], [147, 740], [0,0,1]);
$page->getResources()->addShading('M4', $tri->toStream());   // paint with `sh`
```

Supports free-form (4) and lattice (5) Gouraud triangle meshes, Coons (6) and
tensor-product (7) patch meshes.

### Digital signatures

```php
use Papier\Signature\PdfSigner;

$signer = new PdfSigner($certPem, $keyPem);   // PEM strings or file paths
$signer->setReason('Approved')->setLocation('Paris');
file_put_contents('signed.pdf', $signer->sign(file_get_contents('in.pdf')));
```

Adds an invisible PKCS#7 (CMS) detached signature as an incremental update, so
the original bytes and any prior signatures remain valid.

### PDF/A archival conformance

```php
$doc->enablePdfA(2, 'B');                        // sRGB OutputIntent + XMP pdfaid
$font = $doc->addFont(__DIR__ . '/Lato.ttf');    // fonts MUST be embedded
```

### Smaller files — object streams (PDF 1.5+)

```php
$doc->useObjectStreams();   // pack objects into compressed /ObjStm + /XRef stream
$doc->save('small.pdf');
```

### Font subsetting + searchable text

```php
// Embed only the glyphs that are used; a /ToUnicode CMap is generated
// automatically so the text stays searchable and copy-pasteable.
$lato = $doc->addFont(__DIR__ . '/Lato-Regular.ttf', subset: true);
```

### Incremental updates (edit without rewriting)

```php
use Papier\Writer\IncrementalUpdater;

$updater = PdfDocument::openForUpdate('in.pdf');     // preserves original bytes
$updater->updateObject($infoNum, $newInfoDict);      // override an object
$newNum = $updater->addObject($someDict);            // append a new object
$updater->save('out.pdf');                           // appends a /Prev revision
```

### Read and parse existing PDFs

Reads classic xref tables, cross-reference streams, object streams, and
hybrid-reference files. Encrypted documents (RC4, AES-128, AES-256) are
decrypted transparently once the password is supplied.

```php
use Papier\Parser\PdfParser;

$parser = new PdfParser(file_get_contents('document.pdf'));
$parser->setPassword('secret');                // for encrypted documents
$parser->parse();

echo $parser->getPageCount();                  // number of pages
echo $parser->extractText();                   // all text content
echo $parser->extractTextFromPageNumber(1);    // page 1 only
print_r($parser->getFonts());                  // font list
print_r($parser->getAnnotations());            // annotations
print_r($parser->extractImages());             // embedded images
print_r($parser->getPageInfo(1));              // page dimensions, resources
print_r($parser->getMetadata());               // title, author, subject, …
print_r($parser->getOutlines());               // bookmark tree
print_r($parser->getFormFields());             // AcroForm field values
print_r($parser->getAttachments());            // embedded file attachments
echo $parser->getXmpMetadata();                // raw XMP packet
```

## Examples

The `examples/` directory contains runnable scripts:

| File | Demonstrates |
|------|-------------|
| `01_hello_world.php` | Minimal document creation |
| `02_text_formatting.php` | Fonts, sizes, colours, alignment |
| `03_graphics.php` | Paths, shapes, gradients, clipping |
| `04_images.php` | JPEG/PNG embedding, `fromFile()`, alpha, scaling |
| `05_forms.php` | AcroForm fields |
| `06_annotations.php` | All 14 annotation subtypes (incl. redaction) |
| `07_bookmarks_and_encryption.php` | Outlines, password protection |
| `08_advanced_graphics.php` | Patterns, shadings, transparency groups |
| `09_read_pdf.php` | Parsing an existing PDF |
| `10_optional_content_layers.php` | Togglable layers |
| `11_elements.php` | High-level element API |
| `12_multimedia.php` | Embedded audio/video |
| `13_transitions_and_media_elements.php` | Page transitions |
| `14_table.php` | Tables with rowspan, footer, per-cell borders |
| `15_ttf_fonts.php` | Embedding TTF/OTF font files |
| `16_import_pages.php` | Importing pages from existing PDFs |
| `17_compressed_objects.php` | Object streams + cross-reference stream (smaller files) |
| `18_incremental_update.php` | Editing a PDF via an appended revision |
| `19_read_encrypted.php` | Decrypting + reading outlines, forms, attachments, XMP |
| `20_font_subsetting.php` | TrueType subsetting and ToUnicode generation |
| `21_unicode_cjk.php` | Full-Unicode text via Type 0 (composite) fonts |
| `22_mesh_shadings.php` | Mesh shadings (types 4–7) |
| `23_digital_signature.php` | PKCS#7 digital signature via incremental update |
| `24_pdf_a.php` | PDF/A-2b archival conformance |
| `25_xref_recovery.php` | Recovering PDFs with a corrupt/missing xref |
| `26_type3_font.php` | Type 3 user-defined glyph fonts |
| `27_shading_patterns.php` | Filling shapes and text with gradient patterns |
| `28_tagged_pdf.php` | Tagged PDF / accessibility (PDF/A-2a) |
| `29_text_extraction.php` | ToUnicode-aware text extraction (accents, Type 0) |
| `30_form_filling.php` | Filling an existing AcroForm |
| `31_ccitt_fax.php` | CCITT Group 4 bilevel image compression |
| `32_signature_appearance.php` | Visible signature + document timestamp |
| `33_page_operations.php` | Headers/footers, merge, extract, N-up, rotate |

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
