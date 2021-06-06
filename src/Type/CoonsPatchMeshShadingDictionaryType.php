<?php

namespace Papier\Type;

use Papier\Graphics\ShadingType;

class CoonsPatchMeshShadingDictionaryType extends MeshShadingDictionaryType
{
    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $this->setEntry('ShadingType', ShadingType::COONS_PATCH_MESH);
        return parent::format();
    }
}