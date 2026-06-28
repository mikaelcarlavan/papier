<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\AcroForm\{AcroForm, FormFiller, TextField, CheckBoxField};
use Papier\Parser\PdfParser;

final class FormFillerTest extends TestCase
{
    private function blankForm(): string
    {
        $doc  = PdfDocument::create();
        $doc->setTitle('Blank Form');
        $font = $doc->addFont('Helvetica');
        $page = $doc->addPage();

        $form = new AcroForm();

        $name = new TextField('fullname', 'fullname');
        $name->setRect(200, 700, 420, 718)->setDefaultAppearance($font, 12);
        $form->addField($name);
        $page->addFormField($name);

        $email = new TextField('email', 'email');
        $email->setRect(200, 670, 420, 688)->setDefaultAppearance($font, 12);
        $form->addField($email);
        $page->addFormField($email);

        $subscribe = new CheckBoxField('subscribe', 'subscribe');
        $subscribe->setRect(200, 640, 214, 654);
        $form->addField($subscribe);
        $page->addFormField($subscribe);

        $doc->setAcroForm($form);
        return $doc->toString();
    }

    public function testListsFieldNames(): void
    {
        $filler = new FormFiller($this->blankForm());
        $names  = $filler->getFieldNames();
        sort($names);
        $this->assertSame(['email', 'fullname', 'subscribe'], $names);
    }

    public function testFillsValuesAndIsIncremental(): void
    {
        $original = $this->blankForm();
        $filler = new FormFiller($original);
        $filler->setText('fullname', 'Alice Example')
               ->setText('email', 'alice@example.com')
               ->setCheckbox('subscribe', true);
        $filled = $filler->save();

        // Incremental: original preserved, a new revision appended.
        $this->assertStringStartsWith($original, $filled);
        $this->assertSame(2, substr_count($filled, '%%EOF'));

        // Values are readable through the parser.
        $parser = new PdfParser($filled);
        $parser->parse();
        $byName = [];
        foreach ($parser->getFormFields() as $f) {
            $byName[$f['name']] = $f['value'];
        }
        $this->assertSame('Alice Example', $byName['fullname']);
        $this->assertSame('alice@example.com', $byName['email']);
        $this->assertSame('Yes', $byName['subscribe']);
    }

    public function testTextFieldGetsAppearanceStream(): void
    {
        $filler = new FormFiller($this->blankForm());
        $filler->setText('fullname', 'Visible Value');
        $filled = $filler->save();

        // An appearance Form XObject was generated containing the value.
        $this->assertStringContainsString('/Subtype /Form', $filled);
        $this->assertStringContainsString('(Visible Value) Tj', $filled);
        $this->assertStringContainsString('/Tx BMC', $filled);
    }

    public function testUnknownFieldIgnored(): void
    {
        $filler = new FormFiller($this->blankForm());
        $filler->setText('does_not_exist', 'x');
        // Saving must not error; no edit applied.
        $filled = $filler->save();
        $this->assertNotSame('', $filled);
    }
}
