<?php

namespace Papier\Document;

class NumberingStyle
{
	/**
	 * Decimal arabic numerals
	 *
	 * @var string
	 */
	const DECIMAL_ARABIC = 'D';

	/**
	 * Uppercase roman numerals
	 *
	 * @var string
	 */
	const UPPERCASE_ROMAN = 'R';

	/**
	 * Lowercase roman numerals
	 *
	 * @var string
	 */
	const LOWERCASE_ROMAN = 'r';

	/**
	 * Uppercase letters (A to Z for the first 26 pages, AA to ZZ for the next 26, and
	 * so on)
	 *
	 * @var string
	 */
	const UPPERCASE_LETTERS = 'A';

	/**
	 * Lowercase letters (a to z for the first 26 pages, aa to zz for the next 26, and so
	 * on)
	 *
	 * @var string
	 */
	const LOWERCASE_LETTERS = 'a';
}