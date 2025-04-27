<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Font\TrueType\TrueTypeFontCharacterToGlyphIndexMappingTable;
use Papier\Object\ArrayObject;
use Papier\Object\BaseObject;
use Papier\Object\DictionaryObject;
use Papier\Object\StreamObject;
use Papier\Type\Base\BooleanType;
use Papier\Type\Base\DictionaryType;
use Papier\Type\Base\IntegerType;
use Papier\Type\Base\StreamType;
use Papier\Validator\ArrayValidator;
use Papier\Validator\StringValidator;
use RuntimeException;
use InvalidArgumentException;

class CMapStreamType extends StreamType
{

	/**
	 * Unicode map
	 *
	 * @var array
	 */
	protected array $unicodeMap;

	/**
	 * Set CMap name
	 *
	 * @param  string  $CMapName
	 * @return CMapStreamType
	 */
	public function setCMapName(string $CMapName): CMapStreamType
	{
		$value = Factory::create('Papier\Type\Base\NameType', $CMapName);

		$this->setEntry('CMapName', $value);
		return $this;
	}


	/**
	 * Set entries of the character collection for the CIDFont
	 * or CIDFonts associated with the CMap
	 *
	 * @param DictionaryType $CIDSystemInfo
	 * @return CMapStreamType
	 */
	public function setCIDSystemInfo(DictionaryType $CIDSystemInfo): CMapStreamType
	{
		$this->setEntry('CIDSystemInfo', $CIDSystemInfo);
		return $this;
	}

	/**
	 * Set writing mode for any CIDFont with
	 * which this CMap is combined.
	 *
	 * @param  int  $wMode
	 * @return CMapStreamType
	 */
	public function setWMode(int $wMode): CMapStreamType
	{
		$value = Factory::create('Papier\Type\Base\IntegerType', $wMode);

		$this->setEntry('WMode', $value);
		return $this;
	}

	/**
	 * Set name of a predefined CMap, or a stream containing a
	 * CMap.
	 *
	 * @param  mixed  $useCMap
	 * @return CMapStreamType
	 */
	public function setUseCMap(mixed $useCMap): CMapStreamType
	{
		if (!StringValidator::isValid($useCMap) && !$useCMap instanceof StreamObject) {
			throw new InvalidArgumentException("useCMap is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = $useCMap instanceof StreamObject ? $useCMap : Factory::create('Papier\Type\Base\NameType', $useCMap);

		$this->setEntry('UseCMap', $value);
		return $this;
	}

	/**
	 * Set unicode map.
	 *
	 * @param array $unicodeMap
	 * @return CMapStreamType
	 */
	public function setUnicodeMap(array $unicodeMap): CMapStreamType
	{
		if (!ArrayValidator::isValid($unicodeMap)) {
			throw new InvalidArgumentException("Unicode map is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->unicodeMap = $unicodeMap;
		return $this;
	}

	/**
	 * Get unicode map.
	 *
	 * @return array
	 */
	public function getUnicodeMap(): array
	{
		return $this->unicodeMap;
	}
	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		$type = Factory::create('Papier\Type\Base\NameType', 'CMap');
		$this->setEntry('Type', $type);

		if (!$this->hasEntry('CMapName')) {
			throw new RuntimeException("CMap name is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		$unicodeMap = $this->getUnicodeMap();
		$characters = '';
		$numberOfCharacters = 0;
		foreach ($unicodeMap as $code => $character) {
			$numberOfCharacters++;
			$characters .= sprintf("<%02X> <%04X>\n", $code, $character);
		}

		$content = "/CIDInit /ProcSet findresource begin\n";
		$content .= "12 dict begin\n";
		$content .= "begincmap\n";
		$content .= "/CIDSystemInfo\n";
		$content .= "<</Registry (Adobe)\n";
		$content .= "/Ordering (UCS)\n";
		$content .= "/Supplement 0\n";
		$content .= ">> def\n";
		$content .= "/CMapName /Adobe-Identity-UCS def\n";
		$content .= "/CMapType 2 def\n";
		$content .= "1 begincodespacerange\n";
		$content .= "<00> <FF>\n";
		$content .= "endcodespacerange\n";
		if ($numberOfCharacters > 0) {
			$content .= "$numberOfCharacters beginbfchar\n";
			$content .= $characters;
			$content .= "endbfchar\n";
		}
		$content .= "endcmap\n";
		$content .= "CMapName currentdict /CMap defineresource pop\n";
		$content .= "end\n";
		$content .= "end";

		$this->setContent($content);

		return parent::format();
	}
}