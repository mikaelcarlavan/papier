<?php

namespace Papier\Validator;

use Papier\Graphics\ColourComponents;
use Papier\Multimedia\MediaClip;
use Papier\Validator\Base\Validator;

class MediaClipValidator implements Validator
{
	/**
	 * Media clip values
	 *
	 * @var array<string>
	 */
	const MEDIA_CLIP_VALUES = array(
		MediaClip::MEDIA_CLIP_DATA,
		MediaClip::MEDIA_CLIP_SECTION,
	);


	/**
	 * Test if given parameter is a valid media clip
	 *
	 * @param  string  $value
	 * @return bool
	 */
	public static function isValid($value): bool
	{
		return StringValidator::isValid($value) && in_array($value, self::MEDIA_CLIP_VALUES);
	}
}