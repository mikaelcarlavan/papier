<?php

namespace Papier\Document;

class AnnotationIT
{
	/**
	 * Annotation is intended to function as a plain
	 * free-text annotation
	 *
	 * @var string
	 */
	const FREE_TEXT = 'FreeText';

	/**
	 * Annotation is intended to function as a
	 * callout
	 *
	 * @var string
	 */
	const FREE_TEXT_CALLOUT = 'FreeTextCallout';

	/**
	 * Annotation is intended to function as a click-
	 * to-type or typewriter object and no callout line is
	 * drawn.
	 *
	 * @var string
	 */
	const FREE_TEXT_TYPE_WRITER = 'FreeTextTypeWriter';
}