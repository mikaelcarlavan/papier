<?php

namespace Papier\Document;

class AnnotationHighlightMode
{
	/**
	 * No highlighting
	 *
	 * @var string
	 */
	const NONE = 'N';

	/**
	 * Invert the contents of the annotation rectangle
	 *
	 * @var string
	 */
	const INVERT = 'I';

	/**
	 * Invert the annotation’s border
	 *
	 * @var string
	 */
	const OUTLINE = 'O';

	/**
	 * Display the annotation as if it were being pushed
	 * below the surface of the page
	 *
	 * @var string
	 */
	const PUSH = 'P';
}