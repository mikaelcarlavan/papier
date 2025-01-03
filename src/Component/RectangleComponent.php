<?php

namespace Papier\Component;

use Papier\Component\Base\BaseComponent;
use Papier\Helpers\MetricHelper;
use Papier\Papier;

class RectangleComponent extends BaseComponent
{
    use Color;
    use LineWidth;
    use Position;
	use Size;


	/**
	 * Format component's content.
	 *
	 * @return RectangleComponent
	 */
    function format(): RectangleComponent
    {
        $contents = $this->getContents();
        $contents->save();

        $this->applyColors($contents);

        $x = $this->getX();
        $y = $this->getY();
        $width = $this->getWidth();
        $height = $this->getHeight();

        $mmToUserUnit = Papier::MM_TO_USER_UNIT;

        $lineWidth = $this->getLineWidth();

        $contents->setLineWidth(MetricHelper::toUserUnit($lineWidth));
        
        $contents->appendRectangle(MetricHelper::toUserUnit($x), MetricHelper::toUserUnit($y), MetricHelper::toUserUnit($width), MetricHelper::toUserUnit($height));

        $strokingColors = $this->getStrokingColor();
        $nonStrokingColors = $this->getNonStrokingColor();

        if ($strokingColors && $nonStrokingColors) {
            $contents->fillAndStroke();
        } else if ($strokingColors) {
            $contents->stroke();
        } else {
            $contents->fill();
        }

        $contents->restore();
        
        return $this;
    }
}