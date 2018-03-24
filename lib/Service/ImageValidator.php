<?php
declare(strict_types=1);

/*
 * @package    agitation/images-bundle
 * @link       http://github.com/agitation/images-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\ImagesBundle\Service;

use Agit\ImagesBundle\Exception\BadImageException;
use Agit\IntlBundle\Tool\Translate;
use Agit\ValidationBundle\Validator\AbstractValidator;
use Agit\ValidationBundle\Validator\IntegerValidator;
use Agit\ValidationBundle\Validator\SelectionValidator;
use Exception;

class ImageValidator extends AbstractValidator
{
    /**
     * @var IntegerValidator
     */
    private $integerValidator;

    /**
     * @var SelectionValidator
     */
    private $selectionValidator;

    public function __construct(IntegerValidator $integerValidator, SelectionValidator $selectionValidator)
    {
        $this->integerValidator = $integerValidator;
        $this->selectionValidator = $selectionValidator;
    }

    public function validate($value, $minWidth = null, $maxWidth = null, $minHeight = null, $maxHeight = null, $types = [], $maxSize = 500000)
    {
        if (!is_string($value))
        {
            throw new BadImageException('String expected!');
        }

        if (strlen($value) > $maxSize)
        {
            throw new BadImageException(sprintf(Translate::t('The image must not be larger than %s bytes.'), $maxSize));
        }

        try
        {
            $meta = ImageProcessor::getImageMeta($value);
            $this->integerValidator->validate($meta['width'], $minWidth, $maxWidth);
            $this->integerValidator->validate($meta['height'], $minHeight, $maxHeight);
        }
        catch (Exception $e)
        {
            throw new BadImageException(sprintf(
                Translate::t('The image must have a width of between %s and %s pixels and a height of between %s and %s pixels.'),
                $minWidth,
                $maxWidth,
                $minHeight,
                $maxHeight
            ));
        }

        try
        {
            $this->selectionValidator->validate(image_type_to_mime_type($meta['type']), $types);
        }
        catch (Exception $e)
        {
            throw new BadImageException(sprintf(
                Translate::t('The image is expected to be of one of the following types: %s.'),
                implode(', ', $types)
            ));
        }
    }
}
