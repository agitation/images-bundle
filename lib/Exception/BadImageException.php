<?php

/*
 * @package    agitation/api-bundle
 * @link       http://github.com/agitation/api-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\ImagesBundle\Exception;

use Agit\BaseBundle\Exception\PublicException;

class BadImageException extends PublicException
{
    protected $statusCode = 400;
}
