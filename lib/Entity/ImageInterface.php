<?php

/*
 * @package    tixys/common-bundle
 * @author     Alexander Günsche
 * @copyright  (C) 2017 AGITsol GmbH
 */

namespace Agit\ImagesBundle\Entity;

interface ImageInterface
{
    /**
     * @param string $data base64 image data
     *
     * @return ImageInterface
     */
    public function setData($data);

    /**
     * @param string $description
     *
     * @return ImageInterface
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
     * @return integer
     */
    public function getType();

    /**
     * @return integer
     */
    public function getHeight();

    /**
     * @return integer
     */
    public function getWidth();

    /**
     * @return integer
     */
    public function getFingerprint();
}
