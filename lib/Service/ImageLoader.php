<?php

/*
 * @package    agitation/images-bundle
 * @link       http://github.com/agitation/images-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\ImagesBundle\Service;

use Agit\BaseBundle\Exception\InternalErrorException;
use Agit\BaseBundle\Service\UrlService;
use Agit\ImagesBundle\Entity\ImageInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

/**
 * NOTE: This service is, by itself, useless, because it doesn’t know of any types.
 * To make sensible use of it, you must override this service and register it as agit.images.loader
 * to allow the images controller to load image entities by type and ID. In the new class, you must
 * only override the $types property with a map of [type => entity class/interface].
 */
class ImageLoader
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var UrlService
     */
    protected $urlService;

    /**
     * @var array of type => entity class/interface
     */
    protected $types = [];

    public function __construct(EntityManager $entityManager, RouterInterface $router, UrlService $urlService)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->urlService = $urlService;
    }

    public function setTypes(array $types)
    {
        $this->types = $types;
    }

    public function getImage($type, $id)
    {
        if (! isset($this->types[$type])) {
            throw new BadRequestHttpException("Unknown image type.");
        }

        $image = $this->entityManager->find($this->types[$type], $id);

        if (! $image) {
            throw new NotFoundHttpException("Image not found.");
        }

        return $image;
    }

    public function createImageUrl(ImageInterface $image)
    {
        $type = null;

        foreach ($this->types as $t => $iface) {
            $name = $this->entityManager->getClassMetadata($iface)->getName();

            if ($image instanceof $name) {
                $type = $t;
                break;
            }
        }

        if (! $type) {
            throw new InternalErrorException(sprintf("The type alias for images of class %s could not be determined.", get_class($image)));
        }

        $path = $this->router->generate("image", [
            "type"      => $type,
            "id"        => $image->getId(),
            "extension" => image_type_to_extension($image->getType(), false)
        ]);

        return $this->urlService->createAppUrl($path, ["fp" => $image->getFingerprint()]);
    }
}
