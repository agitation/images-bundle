<?php
declare(strict_types=1);

/*
 * @package    agitation/images-bundle
 * @link       http://github.com/agitation/images-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\ImagesBundle\Api\Annotation;

/**
 * @Annotation
 */
class PicturesType extends PictureType
{
    protected $minCount = null;

    protected $maxCount = null;

    protected $_isListType = true;

    public function check($value)
    {
        $this->init($value);

        if ($this->mustCheck())
        {
            static::$_validator->validate('array', $value, $this->minCount, $this->maxCount);

            foreach ($value as $val)
            {
                $this->checkValue($val);
            }
        }
    }
}
