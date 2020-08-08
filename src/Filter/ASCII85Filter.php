<?php

namespace Papier\Filter;

use Papier\Filter\Base\Filter;
use Papier\Validator\StringValidator;

use RuntimeException;
use InvalidArgumentException;

class ASCII85Filter extends Filter
{    
    /**
     * End-of-data marker.
     *
     * @var string
     */
    const EOD_MARKER = "~>"; 

    /**
     * Decode stream.
     *  
     * @param  string  $stream
     * @param  array  $param
     * @return string
     */
    public static function decode($stream, $param = array())
    {
        $stream = trim($stream);
        $marker = substr($stream, -strlen(self::EOD_MARKER));

        if ($marker != self::EOD_MARKER) {
            throw new InvalidArgumentException("Stream is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = substr($stream, 0, -strlen(self::EOD_MARKER));

        $n = strlen($value) % 5;
        $pad = 5 - $n;
        if ($n != 0) {
            $value .= str_repeat(chr(117), $pad); // chr(117) is 'u'
        }

        // Fill 'z' markers
        $value = str_replace('z', str_repeat(chr(33), 5), $value);
        $groups = str_split($value, 5);

        $result = '';
        if (is_array($groups) && count($groups) > 0) {
            foreach ($groups as $i => $group) {

                $data = (85 ** 4) * (ord($group[0]) - 33) + (85 ** 3)  * (ord($group[1]) - 33) + (85 ** 2) * (ord($group[2]) - 33) +  (85) * (ord($group[3]) - 33) + (ord($group[4]) - 33);

                if ($data == 0 && $i != count($groups) - 1) {
                    $result .= str_repeat(chr(0), 4);
                } else {
                    for ($k = 3; $k >= 0; $k--) {
                        $c = intval(floor($data / (256 ** $k)));
                        $data = $data - $c * (256 ** $k);
    
                        $result .= chr($c);
                    }
                }
            }
        } else {
            throw new RuntimeException("Value is empty. See ".__CLASS__." class's documentation for possible values.");         
        }

        $result = $pad > 0 ? substr($result, 0, -$pad) : $result;

        return $result;
    }

    /**
     * Encode value.
     *  
     * @param  string  $value
     * @param  array  $param
     * @return string
     */
    public static function encode($value, $param = array())
    {
        // Clean white-spaces
        $value = preg_replace('/\s+/', '', $value);

        if (!StringValidator::isValid($value)) {
            throw new InvalidArgumentException("Value is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $n = strlen($value) % 4;
        $pad = 4 - $n;
        if ($n != 0) {
            $value .= str_repeat(chr(0), $pad);
        }

        $groups = str_split($value, 4);

        $result = '';
        if (is_array($groups) && count($groups) > 0) {
            foreach ($groups as $i => $group) {

                $data = (256 ** 3) * ord($group[0]) + (256 ** 2)  * ord($group[1]) + 256 * ord($group[2]) +  ord($group[3]);

                if ($data == 0 && $i != count($groups) - 1) {
                    $result .= 'z';
                } else {
                    for ($k = 4; $k >= 0; $k--) {
                        $c = intval(floor($data / (85 ** $k)));
                        $data = $data - $c * (85 ** $k);
    
                        $result .= chr($c + 33);
                    }
                }
            }
        } else {
            throw new RuntimeException("Value is empty. See ".__CLASS__." class's documentation for possible values.");         
        }

        $result = $pad > 0 ? substr($result, 0, -$pad) : $result;
        return $result . self::EOD_MARKER;
    }
}