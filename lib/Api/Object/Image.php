<?php

/*
 * @package    agitation/multilang-bundle
 * @link       http://github.com/agitation/multilang-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\ImagesBundle\Api\Object;

use Agit\ApiBundle\Annotation\Object;
use Agit\ApiBundle\Annotation\Property;
use Agit\ApiBundle\Api\Object\AbstractEntityObject;
use Agit\ApiBundle\Api\Object\IdTrait;
use Agit\MultilangBundle\Api\Annotation\MultilangStringType;

/**
 * @Object\Object(namespace="admin.v1")
 */
class Image extends AbstractEntityObject
{
    use IdTrait;

    /**
     * @Property\Name("Image data")
     * @Property\StringType
     */
    public $data;

    /**
     * @Property\Name("Description")
     * @MultilangStringType(maxLength=150)
     */
    public $description;

    /**
     * @Property\Name("Type")
     * @Property\StringType(readonly=true)
     */
    public $type;

    /**
     * @Property\Name("Width")
     * @Property\IntegerType(readonly=true)
     */
    public $width;

    /**
     * @Property\Name("Height")
     * @Property\IntegerType(readonly=true)
     */
    public $height;

    /**
     * @Property\Name("Fingerprint")
     * @Property\StringType(readonly=true)
     */
    public $fingerprint;

    public function fill($image)
    {
        parent::fill($image);
        $this->type = image_type_to_mime_type($image->getType());
    }
}
