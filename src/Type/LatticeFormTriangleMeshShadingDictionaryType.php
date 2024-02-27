<?php

namespace Papier\Type;

use Papier\Graphics\ShadingType;

use Papier\Factory\Factory;

use Papier\Validator\IntegerValidator;

use InvalidArgumentException;
use RuntimeException;

class LatticeFormTriangleMeshShadingDictionaryType extends MeshShadingDictionaryType
{
     /**
     * Set the number of vertices in each row of the lattice.
     *  
     * @param  int  $vertices
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return LatticeFormTriangleMeshShadingDictionaryType
     */
    public function setVerticesPerRow(int $vertices): LatticeFormTriangleMeshShadingDictionaryType
    {
        if (!IntegerValidator::isValid($vertices, 2)) {
            throw new InvalidArgumentException("VerticesPerRow is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\IntegerType', $vertices);

        $this->setEntry('VerticesPerRow', $value);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        if (!$this->hasEntry('VerticesPerRow')) {
            throw new RuntimeException("VerticesPerRow is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('ShadingType', ShadingType::LATTICE_FORM_TRIANGLE_MESH);
        return parent::format();
    }
}