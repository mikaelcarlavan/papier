<?php

namespace Papier\Type;

use Papier\Graphics\ShadingType;

class TensorProductPatchMeshShadingDictionaryType extends MeshShadingDictionaryType
{
    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $this->setEntry('ShadingType', ShadingType::TENSOR_PRODUCT_PATCH_MESH);

        return parent::format();
    }
}