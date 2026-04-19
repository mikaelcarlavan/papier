<?php

/**
 * Example 14: Table element
 *
 * Demonstrates the Table and TableCell elements:
 *   - Basic table with header row and striped body
 *   - Column-width control and cell padding
 *   - Per-cell alignment, colour, and font overrides
 *   - Colspan for spanning cells
 *   - Borderless table (list-style)
 *   - Multi-line text wrapping inside cells
 *   - Rowspan for merged cells spanning multiple rows
 *   - Vertical alignment (top / middle / bottom) within cells
 *   - Footer rows with distinct styling
 *   - Table opacity
 *   - Per-cell padding and per-side border overrides
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\{Color, Line, Rectangle, Table, TableCell, Text};
use Papier\Structure\PdfPage;

$doc = PdfDocument::create();
$doc->setTitle('Table Element Demo');

$regular = $doc->addFont('Helvetica');
$bold    = $doc->addFont('Helvetica-Bold');
$italic  = $doc->addFont('Helvetica-Oblique');

// ─────────────────────────────────────────────────────────────────────────────
// Helper: page header
// ─────────────────────────────────────────────────────────────────────────────
$addHeader = static function (string $title) use ($doc, $bold, $regular): PdfPage {
    $page = $doc->addPage();
    $page->add(
        Rectangle::create(0, 800, 595, 42)->fill(Color::rgb(0.12, 0.22, 0.42)),
        Text::write($title)->at(36, 815)->font($bold, 16)->color(Color::white()),
    );
    return $page;
};

// ═════════════════════════════════════════════════════════════════════════════
// Page 1 — Basic sales table with header + striped rows
// ═════════════════════════════════════════════════════════════════════════════
$page1 = $addHeader('Basic Table — Sales Report');

$page1->add(
    Text::write('Quarterly revenue by product category (amounts in USD thousands).')
        ->at(36, 775)->font($regular, 10)->color(Color::gray(0.35)),
);

$sales = Table::create(36, 755)
    ->setColumnWidths(160, 70, 70, 70, 70)
    ->setFont($regular, 10, 'Helvetica')
    ->setHeaderFont($bold, 10, 'Helvetica-Bold')
    ->setHeaderRows(1)
    ->setHeaderBg(Color::rgb(0.12, 0.22, 0.42))
    ->setHeaderTextColor(Color::white())
    ->setAltRowBg(Color::rgb(0.93, 0.95, 0.99))
    ->setBorder(Color::rgb(0.3, 0.35, 0.45), 0.5)
    ->setInnerBorder(Color::rgb(0.7, 0.75, 0.85), 0.3)
    ->setCellPaddingFull(5, 8, 5, 8)
    ->setTextAlign('right');

$sales->addRow([
    TableCell::make('Category')->align('left'),
    'Q1', 'Q2', 'Q3', 'Q4',
]);

$data = [
    ['Software Licenses',  '1 240', '1 380', '1 510', '1 870'],
    ['Professional Services', '680',  '720',  '840',  '990'],
    ['Hardware',           '340',  '290',  '410',  '530'],
    ['Support & Maintenance', '510', '510',  '510',  '510'],
    ['Training',           '120',  '145',  '165',  '200'],
];
foreach ($data as $row) {
    $sales->addRow($row);
}

// Total row with bold font and a top-border accent
$sales->addRow([
    TableCell::make('Total')->align('left')->font($bold, 10, 'Helvetica-Bold'),
    TableCell::make('2 890')->font($bold, 10, 'Helvetica-Bold'),
    TableCell::make('3 045')->font($bold, 10, 'Helvetica-Bold'),
    TableCell::make('3 435')->font($bold, 10, 'Helvetica-Bold'),
    TableCell::make('4 100')->font($bold, 10, 'Helvetica-Bold')
        ->bg(Color::rgb(0.92, 0.96, 0.87)),
]);

$page1->add($sales);

$page1->add(
    Text::write('↑  Alternating row colours, right-aligned numbers, bold totals row.')
        ->at(36, 370)->font($italic, 9)->color(Color::gray(0.5)),
);

// ═════════════════════════════════════════════════════════════════════════════
// Page 2 — Colspan and per-cell overrides
// ═════════════════════════════════════════════════════════════════════════════
$page2 = $addHeader('Colspan & Per-Cell Overrides');

$page2->add(
    Text::write('Employee directory — grouped by department using colspan header cells.')
        ->at(36, 775)->font($regular, 10)->color(Color::gray(0.35)),
);

$dir = Table::create(36, 752)
    ->setColumnWidths(130, 110, 90, 80, 80)
    ->setFont($regular, 10, 'Helvetica')
    ->setHeaderFont($bold, 10, 'Helvetica-Bold')
    ->setHeaderRows(1)
    ->setHeaderBg(Color::rgb(0.15, 0.35, 0.15))
    ->setHeaderTextColor(Color::white())
    ->setRowBg(Color::white())
    ->setAltRowBg(Color::rgb(0.94, 0.98, 0.94))
    ->setBorder(Color::rgb(0.3, 0.5, 0.3), 0.5)
    ->setInnerBorder(Color::rgb(0.7, 0.85, 0.7), 0.3)
    ->setCellPaddingFull(5, 8, 5, 8);

// Main column headers
$dir->addRow(['Name', 'Role', 'Department', 'Start', 'Salary']);

// Section divider: colspan spanning all 5 columns
$dir->addRow([
    TableCell::make('Engineering')
        ->colspan(5)
        ->align('center')
        ->bg(Color::rgb(0.78, 0.9, 0.78))
        ->font($bold, 10, 'Helvetica-Bold'),
]);

$dir->addRow(['Alice Chen',     'Lead Engineer',    'Backend',  '2019-03', '$145k']);
$dir->addRow(['Bob Martinez',   'Senior Engineer',  'Frontend', '2020-07', '$135k']);
$dir->addRow(['Carol Liu',      'Engineer II',      'DevOps',   '2021-11', '$120k']);

$dir->addRow([
    TableCell::make('Product & Design')
        ->colspan(5)
        ->align('center')
        ->bg(Color::rgb(0.78, 0.9, 0.78))
        ->font($bold, 10, 'Helvetica-Bold'),
]);

$dir->addRow(['David Park',     'Product Manager',  'Product',  '2018-06', '$155k']);
$dir->addRow(['Eva Rossi',      'UX Designer',      'Design',   '2022-02', '$110k']);

$dir->addRow([
    TableCell::make('Sales & Marketing')
        ->colspan(5)
        ->align('center')
        ->bg(Color::rgb(0.78, 0.9, 0.78))
        ->font($bold, 10, 'Helvetica-Bold'),
]);

$dir->addRow(['Frank Nguyen',   'Account Executive','Enterprise','2020-09','$125k']);
$dir->addRow(['Grace Okoye',    'Marketing Lead',   'Marketing', '2021-04','$115k']);

$page2->add($dir);

$page2->add(
    Text::write('↑  Section rows use colspan(5) with a tinted background and bold text.')
        ->at(36, 380)->font($italic, 9)->color(Color::gray(0.5)),
);

// ═════════════════════════════════════════════════════════════════════════════
// Page 3 — Borderless list table + multi-line cell content
// ═════════════════════════════════════════════════════════════════════════════
$page3 = $addHeader('Borderless List + Multi-Line Cell Text');

$page3->add(
    Text::write('Borderless table (no outer or inner borders) styled as a feature list.')
        ->at(36, 775)->font($regular, 10)->color(Color::gray(0.35)),
    Text::write('Multi-line descriptions are word-wrapped automatically within the cell width.')
        ->at(36, 761)->font($regular, 10)->color(Color::gray(0.35)),
);

$features = Table::create(36, 738)
    ->setColumnWidths(30, 130, 275)
    ->setFont($regular, 10, 'Helvetica')
    ->setHeaderFont($bold, 11, 'Helvetica-Bold')
    ->setHeaderRows(1)
    ->setHeaderBg(Color::rgb(0.96, 0.96, 0.96))
    ->setAltRowBg(Color::rgb(0.98, 0.98, 0.98))
    ->noBorder()
    ->noInnerBorder()
    ->setCellPaddingFull(6, 10, 6, 10);

$features->addRow([
    TableCell::make('#')->align('center'),
    'Feature',
    'Description',
]);

$featureData = [
    ['1', 'Automatic word wrap',
     'Long text in any cell is automatically broken into multiple lines that fit within the column width. The row height expands to fit all the content.'],
    ['2', 'Colspan support',
     'A single cell can span two or more columns using TableCell::make(…)->colspan(N). Useful for section headers, totals rows, and merged summary cells.'],
    ['3', 'Per-cell styling',
     'Each cell can override the table defaults for background colour, text colour, font (name and size), and text alignment independently.'],
    ['4', 'Striped rows',
     'Alternating row background colours are applied automatically when setAltRowBg() is called. Odd data rows get the alternate colour; even rows use the default.'],
    ['5', 'Header rows',
     'One or more leading rows can be designated as headers via setHeaderRows(). They receive a dedicated font, background, and text colour separate from body rows.'],
    ['6', 'Border control',
     'Outer border and inner grid lines are independently configurable: colour, width, and on/off. Call noBorder() and/or noInnerBorder() for a minimal look.'],
    ['7', 'Fixed or auto height',
     'Row height can be set to a fixed value with setRowHeight() or left at 0 (default) for automatic sizing based on wrapped content and cell padding.'],
];

foreach ($featureData as [$num, $name, $desc]) {
    $features->addRow([
        TableCell::make($num)
            ->align('center')
            ->font($bold, 10, 'Helvetica-Bold')
            ->color(Color::rgb(0.2, 0.4, 0.7)),
        TableCell::make($name)->font($bold, 10, 'Helvetica-Bold'),
        TableCell::make($desc),
    ]);
}

$page3->add($features);

// ═════════════════════════════════════════════════════════════════════════════
// Page 4 — Mixed layout: two side-by-side tables + custom cell colours
// ═════════════════════════════════════════════════════════════════════════════
$page4 = $addHeader('Custom Cell Colours & Side-by-Side Tables');

$page4->add(
    Text::write('Two independent tables placed side by side, each with individual cell colour overrides.')
        ->at(36, 775)->font($regular, 10)->color(Color::gray(0.35)),
);

// ── Left table: status dashboard ─────────────────────────────────────────────
$status = Table::create(36, 752)
    ->setColumnWidths(120, 80, 60)
    ->setFont($regular, 10, 'Helvetica')
    ->setHeaderFont($bold, 10, 'Helvetica-Bold')
    ->setHeaderRows(1)
    ->setHeaderBg(Color::rgb(0.1, 0.1, 0.1))
    ->setHeaderTextColor(Color::white())
    ->setBorder(Color::gray(0.3), 0.5)
    ->setInnerBorder(Color::gray(0.75), 0.3)
    ->setCellPaddingFull(5, 8, 5, 8);

$status->addRow(['Service', 'Uptime', 'Status']);

$services = [
    ['API Gateway',    '99.98 %', 'OK',      [0.85, 0.95, 0.82]],
    ['Auth Service',   '99.91 %', 'OK',      [0.85, 0.95, 0.82]],
    ['Database',       '99.87 %', 'OK',      [0.85, 0.95, 0.82]],
    ['Cache Layer',    '97.40 %', 'DEGRADED',[0.99, 0.96, 0.78]],
    ['Billing API',    '98.55 %', 'OK',      [0.85, 0.95, 0.82]],
    ['CDN Edge',       '95.10 %', 'INCIDENT',[0.98, 0.84, 0.84]],
    ['Notifications',  '99.99 %', 'OK',      [0.85, 0.95, 0.82]],
];

foreach ($services as [$svc, $uptime, $state, [$r, $g, $b]]) {
    $statusColor = match ($state) {
        'OK'       => Color::rgb(0.1, 0.55, 0.1),
        'DEGRADED' => Color::rgb(0.7, 0.5, 0.0),
        default    => Color::rgb(0.75, 0.1, 0.1),
    };
    $status->addRow([
        $svc,
        TableCell::make($uptime)->align('right'),
        TableCell::make($state)
            ->align('center')
            ->bg(Color::rgb($r, $g, $b))
            ->color($statusColor)
            ->font($bold, 9, 'Helvetica-Bold'),
    ]);
}

$page4->add($status);

// ── Right table: progress tracker ────────────────────────────────────────────
$progress = Table::create(310, 752)
    ->setColumnWidths(130, 50, 50)
    ->setFont($regular, 10, 'Helvetica')
    ->setHeaderFont($bold, 10, 'Helvetica-Bold')
    ->setHeaderRows(1)
    ->setHeaderBg(Color::rgb(0.35, 0.1, 0.5))
    ->setHeaderTextColor(Color::white())
    ->setAltRowBg(Color::rgb(0.97, 0.94, 0.99))
    ->setBorder(Color::rgb(0.5, 0.3, 0.6), 0.5)
    ->setInnerBorder(Color::rgb(0.8, 0.7, 0.9), 0.3)
    ->setCellPaddingFull(5, 8, 5, 8);

$progress->addRow([
    TableCell::make('Sprint Task')->align('left'),
    TableCell::make('Est.')->align('center'),
    TableCell::make('Done')->align('center'),
]);

$tasks = [
    ['Redesign login flow',    '8',  '8',  true],
    ['API rate limiting',      '5',  '5',  true],
    ['Dashboard widgets',      '13', '10', false],
    ['Export to CSV',          '3',  '3',  true],
    ['Mobile responsive',      '8',  '4',  false],
    ['Unit test coverage',     '5',  '5',  true],
    ['Performance profiling',  '3',  '1',  false],
];

foreach ($tasks as [$task, $est, $done, $complete]) {
    $bg    = $complete ? Color::rgb(0.90, 0.98, 0.90) : null;
    $doneC = $complete
        ? TableCell::make($done)->align('center')->bg(Color::rgb(0.82, 0.95, 0.82))->color(Color::rgb(0.1, 0.45, 0.1))
        : TableCell::make($done)->align('center')->bg(Color::rgb(0.99, 0.93, 0.85))->color(Color::rgb(0.65, 0.35, 0.0));
    $row = [
        $bg ? TableCell::make($task)->bg($bg) : $task,
        TableCell::make($est)->align('center'),
        $doneC,
    ];
    $progress->addRow($row);
}

$page4->add($progress);

$page4->add(
    Text::write('Left: per-cell background + text colour to indicate service health.')
        ->at(36, 425)->font($italic, 9)->color(Color::gray(0.5)),
    Text::write('Right: row backgrounds and cell overrides for a sprint tracker.')
        ->at(310, 425)->font($italic, 9)->color(Color::gray(0.5)),
);

// ═════════════════════════════════════════════════════════════════════════════
// Page 5 — Rowspan + vertical alignment
// ═════════════════════════════════════════════════════════════════════════════
$page5 = $addHeader('Rowspan & Vertical Alignment');

$page5->add(
    Text::write('Weekly schedule using rowspan to merge time-slot cells across rows.')
        ->at(36, 775)->font($regular, 10)->color(Color::gray(0.35)),
    Text::write('Each merged cell uses a different vertical alignment (top / middle / bottom).')
        ->at(36, 761)->font($regular, 10)->color(Color::gray(0.35)),
);

$schedule = Table::create(36, 738)
    ->setColumnWidths(70, 120, 120, 120)
    ->setFont($regular, 10, 'Helvetica')
    ->setHeaderFont($bold, 10, 'Helvetica-Bold')
    ->setHeaderRows(1)
    ->setHeaderBg(Color::rgb(0.20, 0.20, 0.45))
    ->setHeaderTextColor(Color::white())
    ->setRowBg(Color::white())
    ->setAltRowBg(Color::rgb(0.96, 0.96, 1.00))
    ->setBorder(Color::rgb(0.4, 0.4, 0.6), 0.5)
    ->setInnerBorder(Color::rgb(0.75, 0.75, 0.90), 0.3)
    ->setCellPaddingFull(6, 8, 6, 8)
    ->setMinRowHeight(32);

$schedule->addRow(['Time', 'Monday', 'Tuesday', 'Wednesday']);

// Row 0: 09:00 spans 2 rows; "Team Stand-up" spans 1 row; "Lab" spans 3 rows
$schedule->addRow([
    TableCell::make('09:00')->align('center')->font($bold, 9, 'Helvetica-Bold'),
    TableCell::make('Team Stand-up')->align('center')->valign('middle')
        ->bg(Color::rgb(0.85, 0.92, 1.00)),
    TableCell::make('Research Review')->align('center')->valign('middle')
        ->bg(Color::rgb(0.92, 0.85, 1.00)),
    TableCell::make("Lab / Experiments\n(all morning)")->rowspan(3)
        ->align('center')->valign('middle')
        ->bg(Color::rgb(0.85, 1.00, 0.90))
        ->font($bold, 10, 'Helvetica-Bold'),
]);

// Row 1: time cell spans from row 0 col 0 — need a new time cell
$schedule->addRow([
    TableCell::make('09:30')->align('center')->font($bold, 9, 'Helvetica-Bold'),
    TableCell::make('Sprint Planning')->align('center')->valign('bottom')
        ->bg(Color::rgb(1.00, 0.93, 0.85)),
    // col 2 free
    // col 3 occupied by rowspan
]);

// Row 2: 10:00
$schedule->addRow([
    TableCell::make('10:00')->align('center')->font($bold, 9, 'Helvetica-Bold'),
    TableCell::make('1:1 Meetings')->align('center')->valign('top')
        ->bg(Color::rgb(0.85, 0.92, 1.00)),
    TableCell::make('Code Review')->align('center')->valign('top')
        ->bg(Color::rgb(0.92, 0.85, 1.00)),
    // col 3 occupied
]);

// Row 3: 10:30 — Lab rowspan ends, back to 4-column
$schedule->addRow([
    TableCell::make('10:30')->align('center')->font($bold, 9, 'Helvetica-Bold'),
    TableCell::make('Documentation')->align('center')->valign('middle')
        ->bg(Color::rgb(1.00, 0.98, 0.85)),
    TableCell::make('Deep Work')->align('center')->valign('middle')
        ->bg(Color::rgb(1.00, 0.98, 0.85)),
    TableCell::make('Deep Work')->align('center')->valign('middle')
        ->bg(Color::rgb(1.00, 0.98, 0.85)),
]);

$page5->add($schedule);

$page5->add(
    Text::write('↑  "Lab / Experiments" spans 3 rows (rowspan=3). Vertical alignment: middle/bottom/top labels shown per cell.')
        ->at(36, 480)->font($italic, 9)->color(Color::gray(0.5)),
);

// ═════════════════════════════════════════════════════════════════════════════
// Page 6 — Footer rows + opacity + per-cell border suppression
// ═════════════════════════════════════════════════════════════════════════════
$page6 = $addHeader('Footer Rows, Opacity & Per-Cell Border Overrides');

$page6->add(
    Text::write('Budget table with a styled footer totals row. The ghost table behind uses opacity(0.25).')
        ->at(36, 775)->font($regular, 10)->color(Color::gray(0.35)),
);

// ── Ghost / watermark table at 25 % opacity ───────────────────────────────
$ghost = Table::create(36, 752)
    ->setColumnWidths(160, 80, 80, 80)
    ->setFont($regular, 10, 'Helvetica')
    ->setHeaderFont($bold, 10, 'Helvetica-Bold')
    ->setHeaderRows(1)
    ->setHeaderBg(Color::rgb(0.60, 0.10, 0.10))
    ->setHeaderTextColor(Color::white())
    ->setAltRowBg(Color::rgb(0.98, 0.90, 0.90))
    ->setBorder(Color::rgb(0.70, 0.20, 0.20), 0.5)
    ->setInnerBorder(Color::rgb(0.85, 0.60, 0.60), 0.3)
    ->setCellPaddingFull(5, 8, 5, 8)
    ->setTextAlign('right')
    ->opacity(0.25);

$ghost->addRow([TableCell::make('DRAFT — DO NOT DISTRIBUTE')->colspan(4)->align('center')]);
$ghost->addRow([TableCell::make('Department')->align('left'), 'Budget', 'Spent', 'Remaining']);
$ghost->addRow(['Engineering', '450 000', '312 000', '138 000']);
$ghost->addRow(['Marketing',   '180 000', '155 000',  '25 000']);
$ghost->addRow(['Operations',  '220 000', '198 000',  '22 000']);

$page6->add($ghost);

// ── Real budget table (footer row) ────────────────────────────────────────
$budget = Table::create(36, 600)
    ->setColumnWidths(160, 80, 80, 80)
    ->setFont($regular, 10, 'Helvetica')
    ->setHeaderFont($bold, 10, 'Helvetica-Bold')
    ->setHeaderRows(1)
    ->setHeaderBg(Color::rgb(0.12, 0.35, 0.18))
    ->setHeaderTextColor(Color::white())
    ->setAltRowBg(Color::rgb(0.93, 0.98, 0.93))
    ->setBorder(Color::rgb(0.25, 0.50, 0.30), 0.5)
    ->setInnerBorder(Color::rgb(0.70, 0.88, 0.72), 0.3)
    ->setCellPaddingFull(5, 8, 5, 8)
    ->setTextAlign('right')
    ->setFooterRows(1)
    ->setFooterFont($bold, 10, 'Helvetica-Bold')
    ->setFooterBg(Color::rgb(0.20, 0.42, 0.25))
    ->setFooterTextColor(Color::white());

$budget->addRow([TableCell::make('Department')->align('left'), 'Budget', 'Spent', 'Remaining']);

$depts = [
    ['Engineering', '450 000', '312 000', '138 000'],
    ['Marketing',   '180 000', '155 000',  '25 000'],
    ['Operations',  '220 000', '198 000',  '22 000'],
    ['HR',          ' 90 000',  '87 000',   '3 000'],
    ['Finance',     '130 000', '101 000',  '29 000'],
];
foreach ($depts as $row) {
    $budget->addRow($row);
}

// Footer row — no left/right inner borders on the label cell to give a
// "summary bar" feel
$budget->addRow([
    TableCell::make('Total')
        ->align('left')
        ->borderSides(null, false, null, false),
    TableCell::make('1 070 000')->borderSides(null, false, null, false),
    TableCell::make('  853 000')->borderSides(null, false, null, false),
    TableCell::make('  217 000')->borderSides(null, false, null, false),
]);

$page6->add($budget);

$page6->add(
    Text::write('↑  Footer row has distinct bg/font via setFooterRows(). Inner vertical borders suppressed with borderSides().')
        ->at(36, 330)->font($italic, 9)->color(Color::gray(0.5)),
    Text::write('The watermark-style DRAFT table above uses opacity(0.25) for transparency.')
        ->at(36, 316)->font($italic, 9)->color(Color::gray(0.5)),
);

// ─────────────────────────────────────────────────────────────────────────────
$doc->save(__DIR__ . '/output/14_table.pdf');
echo "Created: 14_table.pdf\n";
echo "  Page 1: Basic sales table with header + striped rows\n";
echo "  Page 2: Employee directory with colspan section dividers\n";
echo "  Page 3: Borderless feature list with multi-line cell text\n";
echo "  Page 4: Service-status dashboard and sprint tracker side by side\n";
echo "  Page 5: Rowspan + vertical alignment (schedule grid)\n";
echo "  Page 6: Footer rows, opacity watermark, per-cell border suppression\n";
