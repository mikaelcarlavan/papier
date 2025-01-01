<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\File\CrossReference\CrossReferenceEntry;

class PageLabelsNumberTreeDictionaryType extends NumberTreeDictionaryType
{
	/**
	 * Add new label.
	 *
	 * @param int $page Page index of the first page in a labelling range
	 * @param string $style Numbering style that shall be used for the numeric portion of each
	 * page label
	 * @param string|null $prefix Label prefix for page labels in this range.
	 * @param int $start Value of the numeric portion for the first page label in the range.
	 * @return PageLabelDictionaryType
	 */
	public function addLabel(int $page, string $style, string $prefix = null, int $start = 0): PageLabelDictionaryType
	{
		$pageLabel = Factory::create('Papier\Type\PageLabelDictionaryType');
		$pageLabel->setS($style);
		if (!is_null($prefix)) {
			$pageLabel->setP($prefix);
		}
		if ($start > 0) {
			$pageLabel->setSt($start);
		}
		$object = new PageLabelDictionaryType();
		$this->addNum($page, $pageLabel);

		return $object;
	}
}