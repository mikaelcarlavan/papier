<?php

namespace Papier\Font;

class FontDescriptorFlag
{
	/**
	 * Fixed Pitch
	 *
	 * @var int
	 */
	const FIXED_PITCH = 1;

	/**
	 * Serif
	 *
	 * @var int
	 */
	const SERIF = 2;

	/**
	 * Symbolic
	 *
	 * @var int
	 */
	const SYMBOLIC = 4;

	/**
	 * Script
	 *
	 * @var int
	 */
	const SCRIPT = 8;

	/**
	 * Non-symbolic
	 *
	 * @var int
	 */
	const NON_SYMBOLIC = 32;

	/**
	 * Italic
	 *
	 * @var int
	 */
	const ITALIC = 64;

	/**
	 * AllCap
	 *
	 * @var int
	 */
	const ALL_CAP = 65536;

	/**
	 * SmallCap
	 *
	 * @var int
	 */
	const SMALL_CAP = 131072;

	/**
	 * ForceBold
	 *
	 * @var int
	 */
	const FORCE_BOLD = 262144;
}