<?php
declare(strict_types=1);

/*
 * @package    agitation/images-bundle
 * @link       http://github.com/agitation/images-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\ImagesBundle\Entity;

interface PictureInterface
{
    /**
     * @param string $data base64 image data
     *
     * @return PictureInterface
     */
    public function setData($data);

    /**
     * @param string $description
     *
     * @return PictureInterface
     */
    public function setDescription($description);

    /**
     * @return string base64 image data
     */
    public function getData();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return int
     */
    public function getType();

    /**
     * @return int
     */
    public function getHeight();

    /**
     * @return int
     */
    public function getWidth();

    /**
     * @return int
     */
    public function getFingerprint();
}
