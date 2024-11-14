<?php


namespace Papier\Filter;

use Papier\Filter\Base\Filter;
use Papier\Object\DictionaryObject;
use Papier\Validator\StringValidator;

use InvalidArgumentException;
use Exception;

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
     * @throws Exception
     */
    public static function decode(string $stream, DictionaryObject $param = null): string
    {
        // Convert compressed bytes back to binary
        $binaryString = '';
        for ($i = 0; $i < strlen($stream); $i++) {
            $binaryString .= str_pad(decbin(ord($stream[$i])), 8, "0", STR_PAD_LEFT);
        }

        // Build the table
        $table = [];
        for ($c = 0; $c < 256; $c++) {
            $table[$c] = chr($c);
        }

        $currentCode = 258;
        $bitWidth = 9; // Starting bit width
        $out = "";
        $sequence = "";

        $i = 0;
        while ($i + $bitWidth <= strlen($binaryString)) {
            // Adjust bit width based on dictionary size before reading the next code
            if ($currentCode > pow(2, $bitWidth)) {
                $bitWidth++;
            }

            // Read `bitWidth` bits to get the next code
            $code = bindec(substr($binaryString, $i, $bitWidth));
            $i += $bitWidth;

            if ($code == self::CT_MARKER) {
                // Build the table
                $table = [];
                for ($c = 0; $c < 256; $c++) {
                    $table[$c] = chr($c);
                }
            } else if ($code == self::EOD_MARKER) {
                break;
            } else {
                // Get the corresponding string for the code
                if (isset($table[$code])) {
                    $entry = $table[$code];
                } elseif ($code == $currentCode) {
                    // Special case for the "w + w[0]" situation
                    $entry = $sequence . $sequence[0];
                } else {
                    throw new Exception("Invalid LZW code encountered.");
                }

                // Append the entry to the result
                $out .= $entry;

                // Add w + entry[0] to the dictionary
                if ($sequence !== "") {
                    $table[$currentCode++] = $sequence . $entry[0];
                }

                $sequence = $entry;
            }

        }


        return $out;
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

        // Build the table
        $table = [];
        for ($c = 0; $c < 256; $c++) {
            $table[chr($c)] = $c;
        }

        $currentCode = 258;
        $sequence = "";
        $codes = [];
        $codes[] = self::CT_MARKER;

        // Process the input string character by character.
        for ($i = 0; $i < strlen($value); $i++) {
            $character = $value[$i];
            $newSequence = $sequence . $character;

            // If wc exists in the dictionary, set w to wc.
            if (isset($table[$newSequence])) {
                $sequence = $newSequence;
            } else {
                // Output the code for w and add wc to the dictionary.
                $codes[] = $table[$sequence];
                $table[$newSequence] = $currentCode++;
                $sequence = $character;
            }
        }

        // Output the code for final sequence.
        if ($sequence !== "") {
            $codes[] = $table[$sequence];
        }

        $codes[] = self::EOD_MARKER;

        // Determine the initial bit width, typically starting at 9 bits
        $bitWidth = ceil(log($currentCode, 2));
        $binaryString = '';

        foreach ($codes as $code) {
            // Convert each code to binary with padded width
            $binaryString .= str_pad(decbin($code), $bitWidth, "0", STR_PAD_LEFT);

            // Increase bit width if dictionary size exceeds current width capacity
            if ($currentCode > pow(2, $bitWidth)) {
                $bitWidth++;
            }
        }

        // Pad the binary string to make it a multiple of 8 bits (1 byte)
        $binaryString = str_pad($binaryString, ceil(strlen($binaryString) / 8) * 8, "0", STR_PAD_RIGHT);

        // Pack the binary string into bytes
        $out = '';
        for ($i = 0; $i < strlen($binaryString); $i += 8) {
            $byte = substr($binaryString, $i, 8);
            $out .= pack('C', bindec($byte)); // Convert each 8-bit segment to a byte
        }

        return $out;
    }
}