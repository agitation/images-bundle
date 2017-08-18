<?php

/*
 * @package    agitation/images-bundle
 * @link       http://github.com/agitation/images-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\ImagesBundle\Api\Annotation;

use Agit\ApiBundle\Annotation\Property\ObjectType;

/**
 * @Annotation
 */
class ImageType extends ObjectType
{
    protected $class = "Image";

    protected $minHeight = null;

    protected $maxHeight = null;

    protected $minWidth = null;

    protected $maxWidth = null;

    protected $types = ["image/png", "image/jpeg"];

    protected $_isObjectType = true;

    protected function checkValue($value)
    {
        parent::checkValue($value);

        static::$_validator->validate(
            "image",
            $value->data,
            $this->minWidth,
            $this->maxWidth,
            $this->minHeight,
            $this->maxHeight,
            $this->types
        );
    }
}
