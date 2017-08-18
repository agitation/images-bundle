<?php

/*
 * @package    agitation/multilang-bundle
 * @link       http://github.com/agitation/multilang-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\ImagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Agit\BaseBundle\Entity\GeneratedIdentityAwareTrait;
use Agit\ImagesBundle\EntityConstraint\Image;
use Agit\ImagesBundle\Service\ImageProcessor;

/**
 * This is only a trait, and not an entity class, because the entity will need the ManyToOne reference to the parent object.
 * IMPORTANT: You must add the $data and $description properties. See below for examples.
 *
 *
 *
 */
trait ImageTrait
{
    use GeneratedIdentityAwareTrait;

    public static function getEntityClassName()
    {
        return Translate::t("Image");
    }

    /// the following could be used as defaults if PHP would allow overriding them in the class.

    // /**
    //  * @ORM\Column(type="text")
    //  * @Image(minWidth=300, maxWidth=500, minHeight=300, maxHeight=500, types={"image/jpeg"})
    //  */
    // protected $data;
    //
    // /**
    //  * @ORM\Column(type="text")
    //  * @Multilang(maxLength=150)
    //  */
    // protected $description;


    /**
     * @ORM\Column(type="smallint")
     *
     * Do not set this, it will be determined automatically.
     */
    private $type;

    /**
     * @ORM\Column(type="smallint")
     *
     * Do not set this, it will be determined automatically.
     */
    private $width;

    /**
     * @ORM\Column(type="smallint")
     *
     * Do not set this, it will be determined automatically.
     */
    private $height;

    /**
     * @ORM\Column(type="string", length=40)
     *
     * Do not set this, it will be determined automatically.
     */
    private $fingerprint;

    /**
     * Set data
     *
     * @param string $data
     *
     * @return Image
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Image
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get fingerprint
     *
     * @return string
     */
    public function getFingerprint()
    {
        return $this->fingerprint;
    }

    /**
     * @internal This method is public for technical reasons only. Do not call it.
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * IMPORTANT: This needs the HasLifecycleCallbacks annotation on the entity!
     */
    public function _update()
    {
        $data = ImageProcessor::stripImage($this->data);
        $meta = ImageProcessor::getImageMeta($data);

        $this->data = $data;
        $this->fingerprint = $meta["fingerprint"];
        $this->width = $meta["width"];
        $this->height = $meta["height"];
        $this->type = $meta["type"];
    }
}
