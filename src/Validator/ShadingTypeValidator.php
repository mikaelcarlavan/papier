<?php

namespace Papier\Validator;

use Papier\Graphics\ShadingType;
use Papier\Validator\IntegerValidator;

class ShadingTypeValidator extends IntegerValidator
{
    /**
     * Shading types.
     *
     * @var array
     */
    const SHADING_TYPES = array(
        ShadingType::FUNCTION_BASED,
        ShadingType::AXIAL,
        ShadingType::RADIAL,
        ShadingType::FREE_FORM_TRIANGLE_MESH,
        ShadingType::LATTICE_FORM_TRIANGLE_MESH,
        ShadingType::COONS_PATCH_MESH,
        ShadingType::TENSOR_PRODUCT_PATCH_MESH,
    );


     /**
     * Test if given parameter is a valid shading type.
     * 
     * @param  int  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = parent::isValid($value) && in_array($value, self::SHADING_TYPES);
        return $isValid;
    }
}