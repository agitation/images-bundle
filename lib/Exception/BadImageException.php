<?php
declare(strict_types=1);
/*
 * @package    agitation/images-bundle
 * @link       http://github.com/agitation/images-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\ImagesBundle\Exception;

use Agit\BaseBundle\Exception\PublicException;

class BadImageException extends PublicException
{
    protected $statusCode = 400;
}
