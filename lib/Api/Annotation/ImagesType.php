<?php

/*
 * @package    agitation/multilang-bundle
 * @link       http://github.com/agitation/multilang-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\ImagesBundle\Api\Annotation;

use Agit\ApiBundle\Annotation\Property\ObjectListType;

/**
 * @Annotation
 */
class ImagesType extends ImageType
{
    protected $minCount = null;

    protected $maxCount = null;

    protected $_isListType = true;

    public function check($value)
    {
        $this->init($value);

        if ($this->mustCheck()) {
            static::$_validator->validate('array', $value, $this->minCount, $this->maxCount);

            foreach ($value as $val) {
                $this->checkValue($val);
            }
        }
    }
}