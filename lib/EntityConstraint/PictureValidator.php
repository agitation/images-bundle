<?php
declare(strict_types=1);

/*
 * @package    agitation/images-bundle
 * @link       http://github.com/agitation/images-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\ImagesBundle\EntityConstraint;

use Agit\ValidationBundle\ValidationService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ImageValidator extends ConstraintValidator
{
    private $validator;

    public function __construct(ValidationService $validator)
    {
        $this->validator = $validator;
    }

    public function validate($value, Constraint $constraint)
    {
        return $this->validator->isValid(
            'image',
            $value,
            $constraint->minWidth,
            $constraint->maxWidth,
            $constraint->minHeight,
            $constraint->maxHeight,
            $constraint->types
        );
    }
}
