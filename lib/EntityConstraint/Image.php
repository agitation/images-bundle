<?php

/*
 * @package    agitation/multilang-bundle
 * @link       http://github.com/agitation/multilang-bundle
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

    public $types = ["image/png", "image/jpeg"];

    public function validatedBy()
    {
        return "image";
    }
}
