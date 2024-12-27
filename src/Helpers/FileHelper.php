<?php

namespace Papier\Helpers;

use InvalidArgumentException;
use Papier\Validator\IntegerValidator;
use Papier\Validator\NumberValidator;

class FileHelper
{
    /**
     * Resource.
     *
     * @var ?resource
     */
    protected mixed $stream;

	/**
	 * Offset.
	 *
	 * @var int
	 */
	protected int $offset = 0;

    /**
     * Instance of the object.
     *
     * @var ?FileHelper
     */
    private static ?FileHelper $instance = null;

	/**
	 * Endianness of the file.
	 *
	 * @var int
	 */
	protected int $endianness = self::BIG_ENDIAN;

	/**
	 * Unsigned-byte type
	 *
	 * @var int
	 */
	const UNSIGNED_BYTE_TYPE = 0;

	/**
	 * Unsigned-integer type
	 *
	 * @var int
	 */
	const UNSIGNED_INTEGER_TYPE = 1;

	/**
	 * Unsigned-short integer type
	 *
	 * @var int
	 */
	const UNSIGNED_SHORT_INTEGER_TYPE = 2;

	/**
	 * Unsigned-long integer type
	 *
	 * @var int
	 */
	const UNSIGNED_LONG_INTEGER_TYPE = 3;

	/**
	 * Little-endian
	 *
	 * @var int
	 */
	const LITTLE_ENDIAN = 0;

	/**
	 * Big-endian
	 *
	 * @var int
	 */
	const BIG_ENDIAN = 0;

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
     * @return self
     */
    public function open(string $file, string $mode = 'r'): self
    {
        $stream = fopen($file, $mode);
        if (!$stream) {
            throw new InvalidArgumentException("File is not valid. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->stream = $stream;
		$this->offset = 0;
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
    public function read(int $length = 1): false|string
	{
		if (!IntegerValidator::isValid($length) || $length < 1) {
			throw new InvalidArgumentException("File is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

        try {
			$stream = $this->getStream();
			if (is_resource($stream)) {
				$this->offset += $length;
				return fread($stream, $length);
			} else {
				return false;
			}
        } catch (\Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

	/**
	 * Set stream's offset
	 *
	 * @param int $offset
	 * @return self
	 */
	public function setOffset(int $offset = 0): self
	{
		if (!IntegerValidator::isValid($offset) || $offset < 0) {
			throw new InvalidArgumentException("Offset is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		try {
			$stream = $this->getStream();
			if (is_resource($stream)) {
				fseek($stream, $offset);
				$this->offset = $offset;
			} else {
				throw new InvalidArgumentException("Stream is not valid. See ".__CLASS__." class's documentation for possible values.");
			}
		} catch (\Exception $e) {
			throw new InvalidArgumentException($e->getMessage());
		}

		return $this;
	}

	/**
	 * Get stream's offset
	 *
	 * @return int
	 */
	public function getOffset(): int
	{
		return $this->offset;
	}


	/**
	 * Set little-endian's use
	 *
	 * @return static
	 */
	public function setLittleEndian(): static
	{
		$this->endianness = self::LITTLE_ENDIAN;
		return $this;
	}

	/**
	 * Set big-endian'use
	 *
	 * @return static
	 */
	public function setBigEndian(): static
	{
		$this->endianness = self::BIG_ENDIAN;
		return $this;
	}

	/**
	 * Returns if little-endian is ued.
	 *
	 * @return bool
	 */
	public function isLittleEndian(): bool
	{
		return $this->endianness == self::LITTLE_ENDIAN;
	}

	/**
	 * Returns if big-endian is ued.
	 *
	 * @return bool
	 */
	public function isBigEndian(): bool
	{
		return $this->endianness == self::BIG_ENDIAN;
	}

	/**
	 * Return format for pack and unpack functions
	 *
	 * @param int $type
	 * @return string
	 */
	public function format(int $type): string
	{
		$formats = array();

		if ($this->isBigEndian()) {
			$formats = [
				self::UNSIGNED_BYTE_TYPE => 'C',
				self::UNSIGNED_SHORT_INTEGER_TYPE => 'n',
				self::UNSIGNED_INTEGER_TYPE => 'N',
				self::UNSIGNED_LONG_INTEGER_TYPE => 'J'
			];
		} elseif ($this->isLittleEndian()) {
			$formats = [
				self::UNSIGNED_BYTE_TYPE => 'C',
				self::UNSIGNED_SHORT_INTEGER_TYPE => 'v',
				self::UNSIGNED_INTEGER_TYPE => 'V',
				self::UNSIGNED_LONG_INTEGER_TYPE => 'P'
			];
		} else {
			throw new InvalidArgumentException("Incorrect endianness. See ".__CLASS__." class's documentation for possible values.");
		}

		if (!isset($formats[$type])) {
			throw new InvalidArgumentException("Incorrect type. See ".__CLASS__." class's documentation for possible values.");
		}

		return $formats[$type];
	}

	/**
	 * Return length for pack and unpack functions
	 *
	 * @param int $type
	 * @return int
	 */
	public function length(int $type): int
	{
		$lengths = array(
			self::UNSIGNED_BYTE_TYPE => 1,
			self::UNSIGNED_SHORT_INTEGER_TYPE => 2,
			self::UNSIGNED_INTEGER_TYPE => 4,
			self::UNSIGNED_LONG_INTEGER_TYPE => 8,
		);


		if (!isset($lengths[$type])) {
			throw new InvalidArgumentException("Incorrect type. See ".__CLASS__." class's documentation for possible values.");
		}

		return $lengths[$type];
	}

	/**
	 * Unpack byte from stream
	 *
	 * @return int
	 */
    public function unpackUnsignedByte(): int
	{
		return $this->unpack(self::UNSIGNED_BYTE_TYPE);
	}

	/**
	 * Unpack byte from stream
	 *
	 * @return int
	 */
	public function unpackByte(): int
	{
		$max = 2 ** 8;
		$mid = $max / 2;
		$value = $this->unpack(self::UNSIGNED_BYTE_TYPE);
		return $value >= $mid - 1 ? $value - $max : $value;
	}

	/**
	 * Unpack 16-bit unsigned integer from stream
	 *
	 * @return int
	 */
	public function unpackUnsignedShortInteger(): int
	{
		return $this->unpack(self::UNSIGNED_SHORT_INTEGER_TYPE);
	}

	/**
	 * Unpack 16-bit signed integer from stream
	 *
	 * @return int
	 */
	public function unpackShortInteger(): int
	{
		$max = 2 ** 16;
		$mid = $max / 2;
		$value = $this->unpack(self::UNSIGNED_SHORT_INTEGER_TYPE);
		return $value >= $mid - 1 ? $value - $max : $value;
	}

	/**
	 * Unpack integer from stream
	 *
	 * @return int
	 */
	public function unpackInteger(): int
	{
		$max = 2 ** 32;
		$mid = $max / 2;
		$value = $this->unpack(self::UNSIGNED_INTEGER_TYPE);
		return $value >= $mid - 1 ? $value - $max : $value;
	}

	/**
	 * Unpack signed-integer from stream
	 *
	 * @return int
	 */
	public function unpackUnsignedInteger(): int
	{
		return $this->unpack(self::UNSIGNED_INTEGER_TYPE);
	}


	/**
	 * Unpack 64-bit unsigned integer from stream
	 *
	 * @return int
	 */
	public function unpackUnsignedLongInteger(): int
	{
		return $this->unpack(self::UNSIGNED_LONG_INTEGER_TYPE);
	}

	/**
	 * Unpack string from stream
	 *
	 * @param int $n
	 * @return string
	 */
	public function unpackString(int $n): string
	{
		$str = '';
		for ($i = 0; $i < $n; $i++) {
			$byte = $this->unpackByte();
			$str .= chr($byte);
		}
		return $str;
	}

	/**
	 * Unpack data from stream
	 *
	 * @param int $type
	 * @return int
	 */
	public function unpack(int $type = self::UNSIGNED_BYTE_TYPE): int
	{
		$length = $this->length($type);
		$format = $this->format($type);

		$chunk = $this->read($length);

		if ($chunk !== false) {
			/** @var array<int>|false $values */
			$values = unpack($format, $chunk);
			if (is_array($values)) {
				/** @var int|null $value */
				$value = array_shift($values);
				if (!is_null($value)) {
					return $value;
				}
			}
		}

		throw new InvalidArgumentException("Incorrect unpacked value. See ".__CLASS__." class's documentation for possible values.");
	}
}