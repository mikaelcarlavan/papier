<?php

namespace Papier\Type;

use Papier\Graphics\ShadingType;


class FreeFormTriangleMeshShadingDictionaryType extends MeshShadingDictionaryType
{
    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $this->setEntry('ShadingType', ShadingType::FREE_FORM_TRIANGLE_MESH);
        return parent::format();
    }
}