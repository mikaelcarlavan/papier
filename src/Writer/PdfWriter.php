<?php

declare(strict_types=1);

namespace Papier\Writer;

use Papier\AcroForm\{AcroForm, FormField};
use Papier\Content\ContentStream;
use Papier\Encryption\StandardSecurityHandler;
use Papier\Font\{Font, TrueTypeFont, Type3Font};
use Papier\Graphics\Image\{JpegImage, PngImage};
use Papier\Graphics\Pattern\TilingPattern;
use Papier\Graphics\Shading\Shading;
use Papier\Graphics\Transparency\ExtGState;
use Papier\LogicalStructure\{StructElement, StructTreeRoot};
use Papier\Metadata\{DocumentInfo, XmpMetadata};
use Papier\Objects\{
    PdfArray, PdfDictionary, PdfIndirectReference,
    PdfInteger, PdfName, PdfNull, PdfObject, PdfReal, PdfStream, PdfString
};
use Papier\OptionalContent\OCProperties;
use Papier\Structure\{PdfOutline, PdfOutlineItem, PdfPage, PdfResources};

/**
 * PDF document writer (ISO 32000-1 §7.5).
 *
 * Assembles all PDF objects into a complete, valid PDF byte stream:
 * 1. %PDF-x.x header
 * 2. Indirect object body
 * 3. Cross-reference table (or stream for PDF 1.5+)
 * 4. Trailer dictionary
 * 5. %%EOF marker
 */
final class PdfWriter
{
    private string  $version   = '1.7';
    private array   $objects   = [];   // [int $num => PdfObject]
    private int     $nextObjNum = 1;
    /** @var array<int, int>  object number → byte offset */
    private array   $offsets   = [];
    private string  $body      = '';

    // Document components
    /** @var PdfPage[] */
    private array   $pages     = [];
    private ?DocumentInfo    $info        = null;
    private ?XmpMetadata     $xmp         = null;
    private ?PdfOutline      $outline     = null;
    private ?AcroForm        $acroForm    = null;
    private ?OCProperties    $ocProperties = null;
    private ?StructTreeRoot  $structTree  = null;
    private ?StandardSecurityHandler $securityHandler = null;

    // Viewer preferences
    private ?PdfDictionary   $viewerPrefs = null;

    // Named destinations: name → PdfArray destination
    /** @var array<string, PdfArray> */
    private array $namedDests = [];

    // Open action (destination array or action dictionary)
    private ?PdfObject $openAction = null;

    // Page labels: number tree  [pageIndex => labelDict, ...]
    /** @var array<int, PdfDictionary> */
    private array $pageLabels = [];

    // Embedded file attachments: display name → [data, mimeType]
    /** @var array<string, array{data:string,mime:string}> */
    private array $embeddedFiles = [];

    // Font registry: resource name → (Font, registered object number)
    /** @var array<string, array{font: Font, objNum: int}> */
    private array $fontRegistry = [];

    public function __construct(string $version = '1.7')
    {
        $this->version = $version;
    }

    // ── Page management ───────────────────────────────────────────────────────

    public function addPage(PdfPage $page): static
    {
        $this->pages[] = $page;
        return $this;
    }

    /** @return PdfPage[] */
    public function getPages(): array { return $this->pages; }

    // ── Document metadata ─────────────────────────────────────────────────────

    public function setInfo(DocumentInfo $info): static { $this->info = $info; return $this; }
    public function setXmpMetadata(XmpMetadata $xmp): static { $this->xmp = $xmp; return $this; }
    public function setOutline(PdfOutline $outline): static { $this->outline = $outline; return $this; }
    public function setAcroForm(AcroForm $form): static { $this->acroForm = $form; return $this; }
    public function setOCProperties(OCProperties $oc): static { $this->ocProperties = $oc; return $this; }
    public function setStructTree(StructTreeRoot $st): static { $this->structTree = $st; return $this; }
    public function setViewerPreferences(PdfDictionary $vp): static { $this->viewerPrefs = $vp; return $this; }

    /**
     * Register a named destination.
     *
     * The name can then be used in GoTo / GoToR actions or outline items.
     *
     * @param string   $name  Unique destination name.
     * @param PdfArray $dest  Destination array (e.g. from {@see XYZDestination::create()}).
     */
    public function addNamedDestination(string $name, PdfArray $dest): static
    {
        $this->namedDests[$name] = $dest;
        return $this;
    }

    /**
     * Set the action or destination to use when the document is opened.
     *
     * Pass a destination array (e.g. from XYZDestination::create()) or an
     * action dictionary.
     *
     * @param PdfObject $action  Destination array or action dictionary.
     */
    public function setOpenAction(PdfObject $action): static
    {
        $this->openAction = $action;
        return $this;
    }

    /**
     * Add a page-label range to the document's /PageLabels number tree.
     *
     * @param int           $startPage   0-based page index where this range starts.
     * @param PdfDictionary $labelDict   Label dictionary (`/S`, `/P`, `/St` entries).
     */
    public function addPageLabel(int $startPage, PdfDictionary $labelDict): static
    {
        $this->pageLabels[$startPage] = $labelDict;
        return $this;
    }

    /**
     * Embed a file attachment in the PDF.
     *
     * The file is accessible via the PDF viewer's "Attachments" panel
     * (§7.11.4, /Names /EmbeddedFiles).
     *
     * @param string $filename  Display name shown in the viewer.
     * @param string $data      Raw file contents.
     * @param string $mimeType  MIME type (e.g. `text/plain`, `application/json`).
     */
    public function attachFile(string $filename, string $data, string $mimeType = 'application/octet-stream'): static
    {
        $this->embeddedFiles[$filename] = ['data' => $data, 'mime' => $mimeType];
        return $this;
    }

    public function setEncryption(StandardSecurityHandler $handler): static
    {
        $this->securityHandler = $handler;
        return $this;
    }

    // ── Font registration ─────────────────────────────────────────────────────

    /**
     * Register a font globally (it will be available on all pages unless
     * per-page resources override it).  Returns the resource name.
     */
    public function registerFont(Font $font, string $name = ''): string
    {
        if ($name === '') {
            $name = 'F' . (count($this->fontRegistry) + 1);
        }
        $font->setResourceName($name);
        $this->fontRegistry[$name] = ['font' => $font, 'objNum' => -1];
        return $name;
    }

    // ── PDF generation ────────────────────────────────────────────────────────

    /**
     * Generate the complete PDF byte stream.
     */
    public function generate(): string
    {
        $this->objects = [];
        $this->nextObjNum = 1;
        $this->offsets    = [];
        $this->body       = '';

        // 1. Generate document file identifier
        $fileId = md5(uniqid('', true) . microtime(true), true);

        // 2. Build encryption dictionary (if configured)
        $encryptObjNum = null;
        if ($this->securityHandler !== null) {
            $encryptDict = $this->securityHandler->buildEncryptDictionary($fileId);
            $encryptObjNum = $this->allocateObject($encryptDict);
        }

        // 3. Build fonts
        $fontObjNums = $this->buildFontObjects();

        // 4. Build AcroForm fields FIRST so field object numbers are available
        //    when building page /Annots arrays (merged field+widget approach).
        $acroFormDict = null;
        if ($this->acroForm !== null) {
            $acroFormDict = $this->buildAcroForm();
        }

        // 5. Build pages
        [$pageTreeObjNum, $pageObjNums] = $this->buildPageTree($fontObjNums);

        // 6. Build info dictionary
        $infoObjNum = null;
        if ($this->info !== null) {
            $infoObjNum = $this->allocateObject($this->info->getDictionary());
        }

        // 7. Build XMP metadata stream
        $xmpObjNum = null;
        if ($this->xmp !== null) {
            $xmpObjNum = $this->allocateObject($this->xmp->getStream());
        }

        // 8. Build outlines (bookmarks)
        $outlinesObjNum = null;
        if ($this->outline !== null) {
            $outlinesObjNum = $this->buildOutlines();
        }

        // 9. Optional content properties
        $ocObjNum = null;
        if ($this->ocProperties !== null) {
            $ocObjNum = $this->allocateObject($this->ocProperties->toDictionary());
        }

        // 10. Structure tree root
        $structObjNum = null;
        if ($this->structTree !== null) {
            $structObjNum = $this->buildStructTree();
        }

        // 11. Build catalog
        $catalogObjNum = $this->buildCatalog(
            $pageTreeObjNum,
            $outlinesObjNum,
            $acroFormDict,
            $xmpObjNum,
            $ocObjNum,
            $structObjNum,
        );

        // 12. Encrypt object data (streams and strings) before serialization
        if ($this->securityHandler !== null && $encryptObjNum !== null) {
            foreach ($this->objects as $objNum => $obj) {
                if ($objNum === $encryptObjNum) {
                    continue; // never encrypt the Encrypt dictionary itself
                }
                $this->encryptObjectTree($obj, $objNum);
            }
        }

        // 13. Serialize everything to bytes
        $output  = "%PDF-{$this->version}\n";
        $output .= "%\xE2\xE3\xCF\xD3\n"; // binary comment (marks as binary file)

        foreach ($this->objects as $objNum => $obj) {
            $this->offsets[$objNum] = strlen($output);
            $output .= "{$objNum} 0 obj\n";
            $output .= $obj->toString();
            $output .= "\nendobj\n";
        }

        // 13. Cross-reference table
        $xrefOffset = strlen($output);
        $output    .= $this->buildXRefTable();

        // 14. Trailer
        $output .= "trailer\n";
        $trailer = new PdfDictionary();
        $trailer->set('Size', new PdfInteger($this->nextObjNum));
        $trailer->set('Root', new PdfIndirectReference($catalogObjNum));
        if ($infoObjNum !== null) {
            $trailer->set('Info', new PdfIndirectReference($infoObjNum));
        }
        if ($encryptObjNum !== null) {
            $trailer->set('Encrypt', new PdfIndirectReference($encryptObjNum));
        }
        $id = new PdfArray();
        $id->add(PdfString::hex($fileId));
        $id->add(PdfString::hex($fileId));
        $trailer->set('ID', $id);
        $output .= $trailer->toString();
        $output .= "\nstartxref\n{$xrefOffset}\n%%EOF\n";

        return $output;
    }

    /**
     * Write the PDF to a file.
     */
    public function save(string $path): void
    {
        $result = file_put_contents($path, $this->generate());
        if ($result === false) {
            throw new \RuntimeException("Cannot write PDF to: $path");
        }
    }

    // ── Internal builders ─────────────────────────────────────────────────────

    /** Reserve and return the next object number, registering the object. */
    private function allocateObject(PdfObject $obj): int
    {
        $num = $this->nextObjNum++;
        $this->objects[$num] = $obj;
        return $num;
    }

    private function buildFontObjects(): array
    {
        $fontObjNums = [];
        foreach ($this->fontRegistry as $name => $reg) {
            $font = $reg['font'];

            // TrueType fonts need a FontFile2 stream
            if ($font instanceof TrueTypeFont) {
                $ffStream = $font->buildFontFileStream();
                $ffObjNum = null;
                if ($ffStream !== null) {
                    $ffObjNum = $this->allocateObject($ffStream);
                    $desc = $font->getDescriptor();
                    if ($desc !== null) {
                        $desc->setFontFile2(new PdfIndirectReference($ffObjNum));
                        $descObjNum = $this->allocateObject($desc->toDictionary());
                        $font->getDictionary()->set('FontDescriptor', new PdfIndirectReference($descObjNum));
                    }
                }
            }

            // Type3 fonts: register glyph streams
            if ($font instanceof Type3Font) {
                $charProcs = new PdfDictionary();
                foreach ($font->getGlyphs() as $code => $glyph) {
                    $glyphStream = new PdfStream();
                    $glyphStream->setData($glyph['stream']->getBuffer());
                    if ($glyph['stream']->isCompressed()) {
                        $glyphStream->compress();
                    }
                    $glyphObjNum  = $this->allocateObject($glyphStream);
                    $charProcs->set($glyph['name'] ?: chr($code), new PdfIndirectReference($glyphObjNum));
                }
                $charProcsObjNum = $this->allocateObject($charProcs);
                $font->getDictionary()->set('CharProcs', new PdfIndirectReference($charProcsObjNum));
            }

            $fontObjNum = $this->allocateObject($font->getDictionary());
            $fontObjNums[$name] = $fontObjNum;
            $this->fontRegistry[$name]['objNum'] = $fontObjNum;
        }
        return $fontObjNums;
    }

    /** @return array{0: int, 1: int[]} [pageTreeObjNum, [pageObjNums]] */
    private function buildPageTree(array $globalFontObjNums): array
    {
        // Allocate the Pages node FIRST so its object number is known before
        // building any page objects (each page needs /Parent → Pages node).
        $pagesDict      = new PdfDictionary();
        $pageTreeObjNum = $this->allocateObject($pagesDict);

        // Build each page
        $pageObjNums = [];
        foreach ($this->pages as $page) {
            $pageObjNums[] = $this->buildPage($page, $globalFontObjNums, $pageTreeObjNum);
        }

        // Now fill in the Pages node (the dict is already registered, mutations
        // are reflected automatically since PHP objects are passed by reference).
        $kids = new PdfArray();
        foreach ($pageObjNums as $n) {
            $kids->add(new PdfIndirectReference($n));
        }

        $w = !empty($this->pages) ? $this->pages[0]->getWidth()  : 595.28;
        $h = !empty($this->pages) ? $this->pages[0]->getHeight() : 841.89;
        $mediaBox = new PdfArray();
        $mediaBox->add(new PdfReal(0.0));
        $mediaBox->add(new PdfReal(0.0));
        $mediaBox->add(new PdfReal($w));
        $mediaBox->add(new PdfReal($h));

        $pagesDict->set('Type',     new PdfName('Pages'));
        $pagesDict->set('Kids',     $kids);
        $pagesDict->set('Count',    new PdfInteger(count($this->pages)));
        $pagesDict->set('MediaBox', $mediaBox);

        return [$pageTreeObjNum, $pageObjNums];
    }

    private function buildPage(PdfPage $page, array $globalFontObjNums, int $pageTreeObjNum): int
    {
        // Merge content streams
        $contentData = '';
        foreach ($page->getContentStreams() as $cs) {
            $contentData .= $cs->getBuffer();
        }

        // Build content stream
        $contentStream = new PdfStream();
        $contentStream->setData($contentData);
        $contentStream->compress();
        $contentObjNum = $this->allocateObject($contentStream);

        // Build resources
        $resources = $page->getResources();

        // Add global fonts to page resources
        foreach ($globalFontObjNums as $name => $num) {
            $resources->addFont($name, new PdfIndirectReference($num));
        }

        // PDF requires stream objects to be indirect — promote any inline streams
        // that may have been added to resource sub-dictionaries (e.g. tiling patterns).
        $this->promoteResourceStreams($resources);

        $resourcesDict   = $resources->toDictionary();
        $resourcesObjNum = $this->allocateObject($resourcesDict);

        // Page MediaBox
        $mediaBox = new PdfArray();
        $mediaBox->add(new PdfReal(0.0));
        $mediaBox->add(new PdfReal(0.0));
        $mediaBox->add(new PdfReal($page->getWidth()));
        $mediaBox->add(new PdfReal($page->getHeight()));

        // Page dictionary
        $pageDict = $page->getDictionary();
        $pageDict->set('Parent', new PdfIndirectReference($pageTreeObjNum));
        $pageDict->set('MediaBox', $mediaBox);
        $pageDict->set('Resources', new PdfIndirectReference($resourcesObjNum));
        $pageDict->set('Contents', new PdfIndirectReference($contentObjNum));

        // Collect all annotations: regular annotations + merged form fields
        $annotsArray = new PdfArray();
        $hasAnnots   = false;

        foreach ($page->getAnnotations() as $annot) {
            $hasAnnots = true;
            $annotObjNum = $this->allocateObject(
                ($annot instanceof \Papier\Annotation\Annotation)
                    ? $annot->getDictionary()
                    : $annot
            );
            $annotsArray->add(new PdfIndirectReference($annotObjNum));
        }

        // Merged form field+widget objects were already allocated in buildAcroForm();
        // include them in the page /Annots array using their pre-allocated object numbers.
        foreach ($page->getFormFields() as $field) {
            $objNum = $field->getAllocatedObjNum();
            if ($objNum !== null) {
                $hasAnnots = true;
                $annotsArray->add(new PdfIndirectReference($objNum));
            }
        }

        if ($hasAnnots) {
            $pageDict->set('Annots', $annotsArray);
        }

        return $this->allocateObject($pageDict);
    }

    /**
     * Promote PdfStream objects stored in resource sub-dictionaries to indirect
     * objects.  PDF §7.3.8 requires streams to be indirect objects; inlining them
     * inside another dictionary causes parsing errors in conformant readers.
     *
     * Also recursively promotes any stream values found inside promoted streams'
     * own dictionaries (e.g. an image's /SMask entry pointing to another stream).
     */
    private function promoteResourceStreams(PdfResources $resources): void
    {
        foreach ([
            [$resources->getPatterns(),  fn($n, $r) => $resources->addPattern($n, $r)],
            [$resources->getXObjects(),  fn($n, $r) => $resources->addXObject($n, $r)],
            [$resources->getShadings(),  fn($n, $r) => $resources->addShading($n, $r)],
        ] as [$dict, $setter]) {
            foreach ($dict->getEntries() as $name => $obj) {
                if ($obj instanceof PdfStream) {
                    // First promote any nested streams inside this stream's dict
                    $this->promoteNestedStreams($obj->getDictionary());
                    $num = $this->allocateObject($obj);
                    $setter($name, new PdfIndirectReference($num));
                }
            }
        }
    }

    /**
     * Replace any PdfStream values inside a dictionary with indirect references.
     * Recurses into nested PdfDictionary values.
     */
    private function promoteNestedStreams(PdfDictionary $dict): void
    {
        foreach ($dict->getEntries() as $key => $value) {
            if ($value instanceof PdfStream) {
                $this->promoteNestedStreams($value->getDictionary());
                $num = $this->allocateObject($value);
                $dict->set($key, new PdfIndirectReference($num));
            } elseif ($value instanceof PdfDictionary) {
                $this->promoteNestedStreams($value);
            }
        }
    }

    private function buildCatalog(
        int     $pageTreeObjNum,
        ?int    $outlinesObjNum,
        ?PdfDictionary $acroFormDict,
        ?int    $xmpObjNum,
        ?int    $ocObjNum,
        ?int    $structObjNum,
    ): int {
        $catalog = new PdfDictionary();
        $catalog->set('Type', new PdfName('Catalog'));
        $catalog->set('Pages', new PdfIndirectReference($pageTreeObjNum));

        if ($outlinesObjNum !== null) {
            $catalog->set('Outlines', new PdfIndirectReference($outlinesObjNum));
            $catalog->set('PageMode', new PdfName('UseOutlines'));
        }

        if ($acroFormDict !== null) {
            $acroFormObjNum = $this->allocateObject($acroFormDict);
            $catalog->set('AcroForm', new PdfIndirectReference($acroFormObjNum));
        }

        if ($xmpObjNum !== null) {
            $catalog->set('Metadata', new PdfIndirectReference($xmpObjNum));
        }

        if ($ocObjNum !== null) {
            $catalog->set('OCProperties', new PdfIndirectReference($ocObjNum));
        }

        if ($structObjNum !== null) {
            $catalog->set('StructTreeRoot', new PdfIndirectReference($structObjNum));
            $catalog->set('MarkInfo', new PdfDictionary());
        }

        if ($this->viewerPrefs !== null) {
            $vpObjNum = $this->allocateObject($this->viewerPrefs);
            $catalog->set('ViewerPreferences', new PdfIndirectReference($vpObjNum));
        }

        // /OpenAction
        if ($this->openAction !== null) {
            $catalog->set('OpenAction', $this->openAction);
        }

        // /PageLabels number tree
        if (!empty($this->pageLabels)) {
            ksort($this->pageLabels);
            $nums = new PdfArray();
            foreach ($this->pageLabels as $pageIndex => $labelDict) {
                $nums->add(new PdfInteger($pageIndex));
                $nums->add($labelDict);
            }
            $plDict = new PdfDictionary();
            $plDict->set('Nums', $nums);
            $plObjNum = $this->allocateObject($plDict);
            $catalog->set('PageLabels', new PdfIndirectReference($plObjNum));
        }

        // /Names  (named dests + embedded files)
        $namesDict = new PdfDictionary();

        if (!empty($this->namedDests)) {
            $destNames = new PdfArray();
            foreach ($this->namedDests as $name => $destArr) {
                $destNames->add(new PdfString($name));
                $destNames->add($destArr);
            }
            $destDict = new PdfDictionary();
            $destDict->set('Names', $destNames);
            $namesDict->set('Dests', $destDict);
        }

        if (!empty($this->embeddedFiles)) {
            $efNames = new PdfArray();
            foreach ($this->embeddedFiles as $filename => $attachment) {
                $efStream = new PdfStream();
                $efStream->setData($attachment['data']);
                $efStream->compress();

                $paramsDict = new PdfDictionary();
                $paramsDict->set('Size', new PdfInteger(strlen($attachment['data'])));
                $paramsDict->set('CreationDate', new PdfString('D:' . gmdate('YmdHis') . 'Z'));
                $paramsDict->set('ModDate',      new PdfString('D:' . gmdate('YmdHis') . 'Z'));

                $efStreamDict = $efStream->getDictionary();
                $efStreamDict->set('Type',    new PdfName('EmbeddedFile'));
                $efStreamDict->set('Subtype', new PdfString($attachment['mime']));
                $efStreamDict->set('Params',  $paramsDict);

                $efObjNum = $this->allocateObject($efStream);

                $filespecDict = new PdfDictionary();
                $filespecDict->set('Type', new PdfName('Filespec'));
                $filespecDict->set('F',    new PdfString($filename));
                $filespecDict->set('UF',   new PdfString($filename));
                $efDict = new PdfDictionary();
                $efDict->set('F', new PdfIndirectReference($efObjNum));
                $filespecDict->set('EF',   $efDict);
                $filespecObjNum = $this->allocateObject($filespecDict);

                $efNames->add(new PdfString($filename));
                $efNames->add(new PdfIndirectReference($filespecObjNum));
            }
            $efNamesDict = new PdfDictionary();
            $efNamesDict->set('Names', $efNames);
            $namesDict->set('EmbeddedFiles', $efNamesDict);
        }

        if (count($namesDict->getEntries()) > 0) {
            $namesObjNum = $this->allocateObject($namesDict);
            $catalog->set('Names', new PdfIndirectReference($namesObjNum));
        }

        $catalog->set('Lang', new PdfString('en-US'));

        return $this->allocateObject($catalog);
    }

    private function buildOutlines(): int
    {
        $items = $this->outline->getRootItems();
        $rootObjNum = $this->nextObjNum;
        $rootDict   = new PdfDictionary();
        $rootDict->set('Type', new PdfName('Outlines'));

        [$firstNum, $lastNum, $count] = $this->buildOutlineItems($items, $rootObjNum);

        if ($firstNum !== null) {
            $rootDict->set('First', new PdfIndirectReference($firstNum));
            $rootDict->set('Last',  new PdfIndirectReference($lastNum));
            $rootDict->set('Count', new PdfInteger($count));
        }

        return $this->allocateObject($rootDict);
    }

    /** @param PdfOutlineItem[] $items */
    private function buildOutlineItems(array $items, int $parentObjNum): array
    {
        if (empty($items)) { return [null, null, 0]; }

        $objNums = [];
        $dicts   = [];

        foreach ($items as $item) {
            $dict = new PdfDictionary();
            $dict->set('Title', PdfString::text($item->getTitle()));
            $dict->set('Parent', new PdfIndirectReference($parentObjNum));

            if (($dest = $item->getDestination()) !== null) {
                $dict->set('Dest', $dest);
            }
            if (($action = $item->getAction()) !== null) {
                $dict->set('A', $action);
            }
            if (($color = $item->getColor()) !== null) {
                $c = new PdfArray();
                $c->add(new PdfReal($color[0]));
                $c->add(new PdfReal($color[1]));
                $c->add(new PdfReal($color[2]));
                $dict->set('C', $c);
            }
            if ($item->getFlags() !== 0) {
                $dict->set('F', new PdfInteger($item->getFlags()));
            }

            $num = $this->nextObjNum++;
            $objNums[] = $num;
            $dicts[]   = $dict;
        }

        // Link siblings
        for ($i = 0; $i < count($items); $i++) {
            if ($i > 0)                   { $dicts[$i]->set('Prev', new PdfIndirectReference($objNums[$i - 1])); }
            if ($i < count($items) - 1)   { $dicts[$i]->set('Next', new PdfIndirectReference($objNums[$i + 1])); }

            // Build children
            $kids = $items[$i]->getChildren();
            if (!empty($kids)) {
                [$firstKid, $lastKid, $kidCount] = $this->buildOutlineItems($kids, $objNums[$i]);
                if ($firstKid !== null) {
                    $dicts[$i]->set('First', new PdfIndirectReference($firstKid));
                    $dicts[$i]->set('Last',  new PdfIndirectReference($lastKid));
                    $dicts[$i]->set('Count', new PdfInteger($kidCount));
                }
            }
        }

        // Register objects
        foreach ($objNums as $i => $num) {
            $this->objects[$num] = $dicts[$i];
        }

        return [$objNums[0], end($objNums), count($items)];
    }

    private function buildAcroForm(): PdfDictionary
    {
        $form   = $this->acroForm;
        $fields = $form->getFields();
        $dict   = $form->getDictionary();

        $fieldRefs = new PdfArray();
        foreach ($fields as $field) {
            $fieldObjNum = $this->buildFormField($field);
            $fieldRefs->add(new PdfIndirectReference($fieldObjNum));
        }
        $dict->set('Fields', $fieldRefs);

        return $dict;
    }

    private function buildFormField(FormField $field): int
    {
        $dict = $field->getDictionary();
        $kids = $field->getKids();

        if (!empty($kids)) {
            // Non-terminal field: has child fields; /FT is inherited, not required here.
            $kidsArr = new PdfArray();
            foreach ($kids as $kid) {
                $kidNum = $this->buildFormField($kid);
                $kidsArr->add(new PdfIndirectReference($kidNum));
            }
            $dict->set('Kids', $kidsArr);
        } else {
            // Terminal (leaf) field: ensure /FT is set.
            if (!$dict->has('FT')) {
                $dict->set('FT', new PdfName($field->getFieldType()));
            }
        }

        $objNum = $this->allocateObject($dict);
        $field->setAllocatedObjNum($objNum);
        return $objNum;
    }

    private function buildStructTree(): int
    {
        $root = $this->structTree;
        $dict = $root->getDictionary();

        $kids = $root->getKids();
        if (!empty($kids)) {
            $kArr = new PdfArray();
            foreach ($kids as $kid) {
                $kidNum = $this->buildStructElement($kid);
                $kArr->add(new PdfIndirectReference($kidNum));
            }
            $dict->set('K', $kArr);
        }

        return $this->allocateObject($dict);
    }

    private function buildStructElement(StructElement $element): int
    {
        $dict = $element->getDictionary();
        $kids = $element->getKids();
        if (!empty($kids)) {
            $kArr = new PdfArray();
            foreach ($kids as $kid) {
                $kidNum = $this->buildStructElement($kid);
                $kArr->add(new PdfIndirectReference($kidNum));
            }
            $dict->set('K', $kArr);
        }
        return $this->allocateObject($dict);
    }

    private function buildXRefTable(): string
    {
        $count = $this->nextObjNum;
        $xref  = "xref\n0 {$count}\n";
        // Free entry for object 0 (exactly 20 bytes: 10+SP+5+SP+1+CR+LF)
        $xref .= "0000000000 65535 f\r\n";

        for ($i = 1; $i < $count; $i++) {
            $offset = $this->offsets[$i] ?? 0;
            $xref  .= sprintf("%010d 00000 n\r\n", $offset);
        }
        return $xref;
    }

    // ── Helper methods for common document assembly ───────────────────────────

    /**
     * Convenience: add a JPEG image to a page and return its resource name.
     *
     * @param PdfPage $page    Target page.
     * @param string  $path    Path to the JPEG file.
     * @param float   $x       X position (lower-left).
     * @param float   $y       Y position (lower-left).
     * @param float   $width   Rendered width; height auto-scaled if 0.
     * @param float   $height  Rendered height.
     * @return string  Resource name of the image (e.g., 'Im1').
     */
    public function addJpegImage(PdfPage $page, string $path, float $x, float $y, float $width = 0, float $height = 0): string
    {
        $img    = JpegImage::fromFile($path);
        $stream = $img->getStream();
        $objNum = $this->allocateObject($stream);

        $imgName = 'Im' . ($this->nextObjNum - 1);
        $page->getResources()->addXObject($imgName, new PdfIndirectReference($objNum));

        // Calculate dimensions
        if ($width === 0 && $height === 0) {
            $width  = (float) $img->getWidth();
            $height = (float) $img->getHeight();
        } elseif ($width === 0) {
            $width = $height * $img->getWidth() / max(1, $img->getHeight());
        } elseif ($height === 0) {
            $height = $width * $img->getHeight() / max(1, $img->getWidth());
        }

        // Add rendering instructions to page content
        $cs = $this->getOrCreateContentStream($page);
        $cs->save()
           ->transform($width, 0, 0, $height, $x, $y)
           ->drawXObject($imgName)
           ->restore();

        return $imgName;
    }

    /**
     * Convenience: add a PNG image to a page.
     */
    public function addPngImage(PdfPage $page, string $path, float $x, float $y, float $width = 0, float $height = 0): string
    {
        $img    = PngImage::fromFile($path);

        // Register soft-mask (alpha channel) if present
        if ($img->hasAlpha() && $img->getSMaskStream() !== null) {
            $sMaskObjNum = $this->allocateObject($img->getSMaskStream());
            $img->getStream()->getDictionary()->set('SMask', new PdfIndirectReference($sMaskObjNum));
        }

        $stream = $img->getStream();
        $objNum = $this->allocateObject($stream);
        $imgName = 'Im' . ($this->nextObjNum - 1);
        $page->getResources()->addXObject($imgName, new PdfIndirectReference($objNum));

        if ($width === 0 && $height === 0) {
            $width  = (float) $img->getWidth();
            $height = (float) $img->getHeight();
        } elseif ($width === 0) {
            $width = $height * $img->getWidth() / max(1, $img->getHeight());
        } elseif ($height === 0) {
            $height = $width * $img->getHeight() / max(1, $img->getWidth());
        }

        $cs = $this->getOrCreateContentStream($page);
        $cs->save()
           ->transform($width, 0, 0, $height, $x, $y)
           ->drawXObject($imgName)
           ->restore();

        return $imgName;
    }

    /**
     * Get or create the primary content stream for a page.
     */
    public function getOrCreateContentStream(PdfPage $page): ContentStream
    {
        $streams = $page->getContentStreams();
        if (!empty($streams)) {
            return end($streams);
        }
        $cs = new ContentStream();
        $page->addContent($cs);
        return $cs;
    }

    /**
     * Add a named extended graphics state (for transparency, etc.) to a page.
     */
    public function addExtGState(PdfPage $page, string $name, ExtGState $gs): static
    {
        $page->getResources()->addExtGState($name, $gs->getDictionary());
        return $this;
    }

    /**
     * Recursively encrypt strings and stream data within an object tree.
     *
     * Per-object key diversification uses $objNum (gen 0).
     * Only PdfString and PdfStream leaf values are encrypted; indirect
     * references, names, integers, and booleans are not.
     */
    private function encryptObjectTree(PdfObject $obj, int $objNum): void
    {
        if ($obj instanceof PdfStream) {
            $encrypted = $this->securityHandler->encryptData($obj->getData(), $objNum, 0);
            // Replace raw data without clearing the stream's /Filter chain
            $obj->setData($encrypted);
            // Recursively encrypt string values inside the stream dictionary
            $this->encryptObjectTree($obj->getDictionary(), $objNum);
        } elseif ($obj instanceof PdfDictionary) {
            foreach ($obj->getEntries() as $key => $value) {
                if ($value instanceof PdfString) {
                    $encrypted = $this->securityHandler->encryptData($value->getValue(), $objNum, 0);
                    $obj->set($key, PdfString::hex($encrypted));
                } elseif ($value instanceof PdfArray || $value instanceof PdfDictionary) {
                    $this->encryptObjectTree($value, $objNum);
                }
                // PdfStream nested here would be an indirect ref in practice, skip
            }
        } elseif ($obj instanceof PdfArray) {
            foreach ($obj->getItems() as $index => $value) {
                if ($value instanceof PdfString) {
                    $encrypted = $this->securityHandler->encryptData($value->getValue(), $objNum, 0);
                    $obj->set($index, PdfString::hex($encrypted));
                } elseif ($value instanceof PdfArray || $value instanceof PdfDictionary) {
                    $this->encryptObjectTree($value, $objNum);
                }
            }
        }
    }
}
