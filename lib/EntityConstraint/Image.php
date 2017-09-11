<?php
declare(strict_types=1);
/*
 * @package    agitation/images-bundle
 * @link       http://github.com/agitation/images-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\ImagesBundle\EntityConstraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Image extends Constraint
{
    public $minWidth = null;

    public $maxWidth = null;

    public $minHeight = null;

    public $maxHeight = null;

    public $types = ['image/png', 'image/jpeg'];

    public function validatedBy()
    {
        return 'image';
    }
}
