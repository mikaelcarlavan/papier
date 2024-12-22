<?php

namespace Papier\Helpers;

use InvalidArgumentException;
use Papier\Validator\IntegerValidator;

class FileHelper
{
    /**
     * Resource.
     *
     * @var ?resource
     */
    protected mixed $stream;

    /**
     * Instance of the object.
     *
     * @var ?FileHelper
     */
    protected static ?FileHelper $instance = null;


    /**
     * Get instance of helper.
     *
     * @return FileHelper
     */
    public static function getInstance(): FileHelper
    {
        if (is_null(self::$instance)) {
            self::$instance = new FileHelper();
        }

        return self::$instance;
    }

    /**
     * Get stream.
     *
     * @return resource|null
     */
    public function getStream(): mixed
    {
        return $this->stream;
    }

    /**
     * Open file for reading or writing.
     *
     * @param string $file
     * @param string $mode
     * @return FileHelper
     */
    public function open(string $file, string $mode = 'r'): FileHelper
    {
        $stream = fopen($file, $mode);
        if (!$stream) {
            throw new InvalidArgumentException("File is not a valid. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->stream = $stream;
        return $this;
    }

    /**
     * Close file.
     *
     * @return void
     */
    public function close()
    {
		$stream = $this->getStream();
		if (is_resource($stream)) {
			fclose($stream);
		}

        $this->stream = null;
    }

	/**
	 * Read bytes from file
	 *
	 * @param int $length
	 * @return false|string
	 */
    public function read(int $length): false|string
	{
		if (!IntegerValidator::isValid($length) || $length < 1) {
			throw new InvalidArgumentException("File is not a valid. See ".__CLASS__." class's documentation for possible values.");
		}

        try {
			$stream = $this->getStream();
			if (is_resource($stream)) {
				return fread($stream, $length);
			} else {
				return false;
			}
        } catch (\Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

	/**
	 * Unpack integer from stream
	 *
	 * @return int
	 */
    public function unpackInteger(): int
	{
        try {
			$stream = $this->getStream();
			if (is_resource($stream)) {
				$chunk = fread($stream, 4);
				if ($chunk !== false) {
					/** @var array<int>|false $values */
					$values = unpack("N", $chunk);
					if (is_array($values)) {
						/** @var int|null $value */
						$value = array_shift($values);
						if (!is_null($value)) {
							return $value;
						}
					}
				}
			}

			throw new InvalidArgumentException("Incorrect unpacked value. See ".__CLASS__." class's documentation for possible values.");
		} catch (\Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

	/**
	 * Unpack byte from stream
	 *
	 * @return mixed
	 */
    public function unpackByte(): mixed
	{
        try {
			$stream = $this->getStream();
			if (is_resource($stream)) {
				$chunk = fread($stream, 1);
				if ($chunk !== false) {
					/** @var array<int>|false $values */
					$values = unpack("C", $chunk);
					if (is_array($values)) {
						/** @var mixed $value */
						$value = array_shift($values);
						if (!is_null($value)) {
							return $value;
						}
					}
				}
			}

			throw new InvalidArgumentException("Incorrect unpacked byte. See ".__CLASS__." class's documentation for possible values.");
        } catch (\Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}