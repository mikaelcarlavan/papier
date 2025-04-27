<?php

namespace Papier\Document;

class LineEndingStyle
{
	/**
	 * A square filled with the annotation’s interior color, if any
	 *
	 * @var string
	 */
	const SQUARE = 'Square';

	/**
	 * A circle filled with the annotation’s interior color, if any
	 *
	 * @var string
	 */
	const CIRCLE = 'Circle';

	/**
	 * A diamond filled with the annotation’s interior color, if any
	 *
	 * @var string
	 */
	const DIAMOND = 'Diamond';

	/**
	 * Two short lines meeting in an acute angle to form an open
	 * arrowhead
	 *
	 * @var string
	 */
	const OPEN_ARROW = 'OpenArrow';

	/**
	 * Two short lines meeting in an acute angle as in the OpenArrow style
	 * and connected by a third line to form a triangular closed arrowhead
	 * filled with the annotation’s interior color, if any
	 *
	 * @var string
	 */
	const CLOSED_ARROW = 'ClosedArrow';

	/**
	 * No line ending
	 *
	 * @var string
	 */
	const NONE = 'None';

	/**
	 * A short line at the endpoint perpendicular to the line itself
	 *
	 * @var string
	 */
	const BUTT = 'Butt';

	/**
	 * Two short lines in the reverse direction from OpenArrow
	 *
	 * @var string
	 */
	const R_OPEN_ARROW = 'ROpenArrow';

	/**
	 * A triangular closed arrowhead in the reverse direction
	 * from ClosedArrow
	 *
	 * @var string
	 */
	const R_CLOSED_ARROW = 'RClosedArrow';

	/**
	 * A short line at the endpoint approximately 30 degrees
	 * clockwise from perpendicular to the line itself
	 *
	 * @var string
	 */
	const SLASH = 'Slash';
}