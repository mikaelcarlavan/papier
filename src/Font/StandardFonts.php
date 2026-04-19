<?php

declare(strict_types=1);

namespace Papier\Font;

/**
 * The 14 standard PDF fonts (ISO 32000-1 §9.6.2.2).
 *
 * Conforming readers must support these fonts without embedding.
 * Glyph widths are provided for layout calculations.
 */
final class StandardFonts
{
    /**
     * Standard font names indexed by canonical name.
     * Values are [PostScript name, subfamily flags].
     */
    public const FONTS = [
        'Courier'               => ['Courier',               0],
        'Courier-Bold'          => ['Courier-Bold',          0],
        'Courier-Oblique'       => ['Courier-Oblique',       0],
        'Courier-BoldOblique'   => ['Courier-BoldOblique',   0],
        'Helvetica'             => ['Helvetica',             32],
        'Helvetica-Bold'        => ['Helvetica-Bold',        32],
        'Helvetica-Oblique'     => ['Helvetica-Oblique',     32],
        'Helvetica-BoldOblique' => ['Helvetica-BoldOblique', 32],
        'Times-Roman'           => ['Times-Roman',           6],
        'Times-Bold'            => ['Times-Bold',            6],
        'Times-Italic'          => ['Times-Italic',          6],
        'Times-BoldItalic'      => ['Times-BoldItalic',      6],
        'Symbol'                => ['Symbol',                4],
        'ZapfDingbats'          => ['ZapfDingbats',          4],
    ];

    /**
     * Character widths for Helvetica (AFM data, glyph widths in 1/1000 unit).
     * Keys are character codes 32–255 (WinAnsiEncoding).
     *
     * @var array<int, int>
     */
    private static array $helveticaWidths = [
        32=>278,33=>278,34=>355,35=>556,36=>556,37=>889,38=>667,39=>222,
        40=>333,41=>333,42=>389,43=>584,44=>278,45=>333,46=>278,47=>278,
        48=>556,49=>556,50=>556,51=>556,52=>556,53=>556,54=>556,55=>556,
        56=>556,57=>556,58=>278,59=>278,60=>584,61=>584,62=>584,63=>556,
        64=>1015,65=>667,66=>667,67=>722,68=>722,69=>667,70=>611,71=>778,
        72=>722,73=>278,74=>500,75=>667,76=>556,77=>833,78=>722,79=>778,
        80=>667,81=>778,82=>722,83=>667,84=>611,85=>722,86=>667,87=>944,
        88=>667,89=>667,90=>611,91=>278,92=>278,93=>278,94=>469,95=>556,
        96=>222,97=>556,98=>556,99=>500,100=>556,101=>556,102=>278,103=>556,
        104=>556,105=>222,106=>222,107=>500,108=>222,109=>833,110=>556,
        111=>556,112=>556,113=>556,114=>333,115=>500,116=>278,117=>556,
        118=>500,119=>722,120=>500,121=>500,122=>500,123=>334,124=>260,
        125=>334,126=>584,160=>278,161=>333,162=>556,163=>556,164=>556,
        165=>556,166=>260,167=>556,168=>333,169=>737,170=>370,171=>556,
        172=>584,174=>737,175=>333,176=>400,177=>584,178=>333,179=>333,
        180=>333,181=>556,182=>537,183=>278,184=>333,185=>333,186=>365,
        187=>556,188=>834,189=>834,190=>834,191=>611,192=>667,193=>667,
        194=>667,195=>667,196=>667,197=>667,198=>1000,199=>722,200=>667,
        201=>667,202=>667,203=>667,204=>278,205=>278,206=>278,207=>278,
        208=>722,209=>722,210=>778,211=>778,212=>778,213=>778,214=>778,
        215=>584,216=>778,217=>722,218=>722,219=>722,220=>722,221=>667,
        222=>667,223=>611,224=>556,225=>556,226=>556,227=>556,228=>556,
        229=>556,230=>889,231=>500,232=>556,233=>556,234=>556,235=>556,
        236=>278,237=>278,238=>278,239=>278,240=>556,241=>556,242=>556,
        243=>556,244=>556,245=>556,246=>556,247=>584,248=>611,249=>556,
        250=>556,251=>556,252=>556,253=>500,254=>556,255=>500,
    ];

    private static array $timesWidths = [
        32=>250,33=>333,34=>408,35=>500,36=>500,37=>833,38=>778,39=>180,
        40=>333,41=>333,42=>500,43=>564,44=>250,45=>333,46=>250,47=>278,
        48=>500,49=>500,50=>500,51=>500,52=>500,53=>500,54=>500,55=>500,
        56=>500,57=>500,58=>278,59=>278,60=>564,61=>564,62=>564,63=>444,
        64=>921,65=>722,66=>667,67=>667,68=>722,69=>611,70=>556,71=>722,
        72=>722,73=>333,74=>389,75=>722,76=>611,77=>889,78=>722,79=>722,
        80=>556,81=>722,82=>667,83=>556,84=>611,85=>722,86=>722,87=>944,
        88=>722,89=>722,90=>611,91=>333,92=>278,93=>333,94=>469,95=>500,
        96=>333,97=>444,98=>500,99=>444,100=>500,101=>444,102=>333,103=>500,
        104=>500,105=>278,106=>278,107=>500,108=>278,109=>778,110=>500,
        111=>500,112=>500,113=>500,114=>333,115=>389,116=>278,117=>500,
        118=>500,119=>722,120=>500,121=>500,122=>444,123=>480,124=>200,
        125=>480,126=>541,
    ];

    private static array $courierWidths = []; // Courier is monospaced: all glyphs are 600

    /**
     * Return the glyph width for a character in the given font (in 1/1000 units).
     */
    public static function getGlyphWidth(string $fontName, int $charCode): int
    {
        return match (true) {
            str_contains($fontName, 'Courier') => 600,
            str_starts_with($fontName, 'Helvetica') => self::$helveticaWidths[$charCode] ?? 278,
            str_starts_with($fontName, 'Times') => self::$timesWidths[$charCode] ?? 500,
            default => 500,
        };
    }

    /**
     * Calculate the width of a string in a given font at the specified size.
     */
    public static function stringWidth(string $text, string $fontName, float $fontSize): float
    {
        $width = 0;
        $len   = strlen($text);
        for ($i = 0; $i < $len; $i++) {
            $width += self::getGlyphWidth($fontName, ord($text[$i]));
        }
        return $width * $fontSize / 1000;
    }

    public static function isStandard(string $name): bool
    {
        return isset(self::FONTS[$name]);
    }
}
