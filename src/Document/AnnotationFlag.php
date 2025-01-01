<?php

namespace Papier\Document;

class AnnotationFlag
{
	/**
	 * Invisible
	 *
	 * @var int
	 */
	const INVISIBLE = 1;

	/**
	 * Hidden
	 *
	 * @var int
	 */
	const HIDDEN = 2;

	/**
	 * Print
	 *
	 * @var int
	 */
	const PRINT = 4;

	/**
	 * NoZoom
	 *
	 * @var int
	 */
	const NO_ZOOM = 8;

	/**
	 * NoRotate
	 *
	 * @var int
	 */
	const NO_ROTATE = 16;

	/**
	 * NoView
	 *
	 * @var int
	 */
	const NO_VIEW = 32;

	/**
	 * ReadOnly
	 *
	 * @var int
	 */
	const READ_ONLY = 64;

	/**
	 * Locked
	 *
	 * @var int
	 */
	const LOCKED = 128;

	/**
	 * ToggleNoView
	 *
	 * @var int
	 */
	const TOGGLE_NO_VIEW = 256;

	/**
	 * LockedContents
	 *
	 * @var int
	 */
	const LOCKED_CONTENTS = 512;

}