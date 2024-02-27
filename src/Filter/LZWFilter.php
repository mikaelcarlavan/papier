<?php


namespace Papier\Filter;

use Papier\Filter\Base\Filter;
use Papier\Object\DictionaryObject;
use Papier\Validator\StringValidator;

use InvalidArgumentException;

class LZWFilter extends Filter
{
    /**
     * End-of-data marker.
     *
     * @var string
     */
    const EOD_MARKER = 257;

    /**
     * Clear-table marker.
     *
     * @var string
     */
    const CT_MARKER = 256;

    /**
     * Decode stream.
     *
     * @param string $stream
     * @param DictionaryObject|null $param
     * @return string
     */
    public static function decode(string $stream, DictionaryObject $param = null): string
    {
        $out = chr(self::CT_MARKER);
        $i = 1;
        $table = [];
        $characters = unpack("C*", $stream);
        $currentCode = self::EOD_MARKER;
        while ($i <= count($characters)) {
            $sequence = $characters[$i++];

            while (isset($table[$sequence])) {
                $sequence .= $characters[$i++];
            }

            // Output value
            $out .= chr();
            $currentCode++;
            $table[$sequence] = $currentCode;

            /*if (isset($table[$sequence])) {
                $out .= $table[$byteCharacter];
                $newSequence = $byteCharacter .
            }*/
        }

        return '';
    }

    /**
     * Encode value.
     *
     * @param string $value
     * @param DictionaryObject|null $param
     * @return string
     */
    public static function encode(string $value, DictionaryObject $param = null): string
    {
        if (!StringValidator::isValid($value)) {
            throw new InvalidArgumentException("Value is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $out = chr(self::CT_MARKER);
        $i = 1;
        $table = [];
        for ($c = 0; $c < 256; $c++) {
            $character = chr($c);
            $table[$character] = $c;
        }
        $characters = unpack("C*", $value);

        var_dump($characters);

        $currentCode = self::EOD_MARKER;
        $sequence = '';
        while ($i <= count($characters)) {
            $character = $characters[$i++];
            $newSequence = $sequence . $character;

            if (isset($table[$newSequence])) {
                $sequence = $newSequence;
            } else {
                // Output value
                $out .= chr($table[$sequence]);
                $currentCode++;
                $table[$newSequence] = $currentCode;
            }



            /*if (isset($table[$sequence])) {
                $out .= $table[$byteCharacter];
                $newSequence = $byteCharacter .
            }*/
        }

        return $out;
    }
}