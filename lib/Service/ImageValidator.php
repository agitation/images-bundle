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

    public function validate($value, $minWidth = null, $maxWidth = null, $minHeight = null, $maxHeight = null, $types = [])
    {
        $meta = ImageProcessor::getImageMeta($value);

        try
        {
            $this->integerValidator->validate($meta['width'], $minWidth, $maxWidth);
            $this->integerValidator->validate($meta['height'], $minHeight, $maxHeight);
        }
        catch (Exception $e)
        {
            throw new BadImageException(sprintf(
                Translate::t('The image is expected to have a width of between %d and %d pixels and a height of between %d and %d pixels. But the image is actually %d pixels wide and %d pixels high.'),
                $minWidth,
                $maxWidth,
                $minHeight,
                $maxHeight,
                $meta['width'],
                $meta['height']
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
