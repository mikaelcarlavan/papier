<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Encryption\StandardSecurityHandler;
use Papier\AcroForm\{AcroForm, TextField};
use Papier\Structure\{PdfOutline, PdfOutlineItem};
use Papier\Elements\Text;
use Papier\Objects\PdfString;
use Papier\Parser\PdfParser;

final class ReadParityTest extends TestCase
{
    private function buildDoc(): PdfDocument
    {
        $doc  = PdfDocument::create();
        $doc->setTitle('Round Trip')->setAuthor('Papier')->setSubject('Testing');
        $font = $doc->addFont('Helvetica');
        $page = $doc->addPage();
        $page->add(Text::write('Hello Parser')->at(72, 750)->font($font, 18));

        // Attachment
        $doc->attachFile('data.txt', "embedded-payload-123", 'text/plain');

        // Outline
        $outline = new PdfOutline();
        $ch  = new PdfOutlineItem('Chapter 1');
        $sub = new PdfOutlineItem('Section 1.1');
        $ch->addChild($sub);
        $outline->addItem($ch);
        $doc->setOutline($outline);

        // Form field
        $form = new AcroForm();
        $tf = new TextField('person.name', 'person.name');
        $tf->setRect(200, 700, 400, 718)
           ->setDefaultAppearance($font, 12)
           ->setValue(PdfString::text('Alice'));
        $form->addField($tf);
        $page->addFormField($tf);
        $doc->setAcroForm($form);

        return $doc;
    }

    public function testReadsMetadataAttachmentsOutlinesFormsXmp(): void
    {
        $pdf    = $this->buildDoc()->toString();
        $parser = new PdfParser($pdf);
        $parser->parse();

        $meta = $parser->getMetadata();
        $this->assertSame('Round Trip', $meta['title']);
        $this->assertSame('Papier', $meta['author']);

        $atts = $parser->getAttachments();
        $this->assertCount(1, $atts);
        $this->assertSame('data.txt', $atts[0]['name']);
        $this->assertSame('embedded-payload-123', $atts[0]['data']);

        $outlines = $parser->getOutlines();
        $this->assertCount(1, $outlines);
        $this->assertSame('Chapter 1', $outlines[0]['title']);
        $this->assertCount(1, $outlines[0]['children']);
        $this->assertSame('Section 1.1', $outlines[0]['children'][0]['title']);

        $fields = $parser->getFormFields();
        $this->assertNotEmpty($fields);
        $names = array_column($fields, 'name');
        $this->assertContains('person.name', $names);
        $field = $fields[array_search('person.name', $names, true)];
        $this->assertSame('Tx', $field['type']);
        $this->assertSame('Alice', $field['value']);

        $xmp = $parser->getXmpMetadata();
        $this->assertNotNull($xmp);
        $this->assertStringContainsString('xpacket', $xmp);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('algorithms')]
    public function testDecryptionRoundTrip(int $algorithm): void
    {
        $pdf = $this->buildDoc()
            ->encrypt('secret', 'owner', StandardSecurityHandler::PERM_ALL, $algorithm)
            ->toString();

        // Reading with the user password.
        $parser = (new PdfParser($pdf))->setPassword('secret');
        $parser->parse();
        $this->assertTrue($parser->isEncrypted());
        $this->assertSame('Round Trip', $parser->getTitle());
        $this->assertStringContainsString('Hello Parser', $parser->extractText());
        $this->assertSame('embedded-payload-123', $parser->getAttachments()[0]['data']);

        // Reading with the owner password.
        $owner = (new PdfParser($pdf))->setPassword('owner');
        $owner->parse();
        $this->assertSame('Round Trip', $owner->getTitle());
    }

    public function testWrongPasswordThrows(): void
    {
        $pdf = $this->buildDoc()
            ->encrypt('secret', 'owner', StandardSecurityHandler::PERM_ALL, StandardSecurityHandler::AES_128)
            ->toString();

        $this->expectException(\RuntimeException::class);
        (new PdfParser($pdf))->setPassword('wrong')->parse();
    }

    public static function algorithms(): array
    {
        return [
            'RC4-40'  => [StandardSecurityHandler::RC4_40],
            'RC4-128' => [StandardSecurityHandler::RC4_128],
            'AES-128' => [StandardSecurityHandler::AES_128],
            'AES-256' => [StandardSecurityHandler::AES_256],
        ];
    }
}
