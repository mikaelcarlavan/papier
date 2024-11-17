<?php

namespace Papier\Helpers;

use InvalidArgumentException;

class FileHelper
{
    /**
     * Resource.
     *
     * @var mixed
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
     * @return mixed
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
        fclose($this->getStream());
        $this->stream = null;
    }

	/**
	 * Read bytes from file
	 *
	 * @param int $length
	 * @return mixed
	 */
    public function read(int $length): mixed
	{
        try {
            return fread($this->getStream(), $length);
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
            $chunk = fread($this->getStream(), 4);
            $values = unpack("N", $chunk);
            return array_shift($values);
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
            $chunk = fread($this->getStream(), 1);
            $values = unpack("C", $chunk);
            return array_shift($values);
        } catch (\Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}