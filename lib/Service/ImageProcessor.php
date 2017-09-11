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
use Exception;
use Imagick;

class ImageProcessor
{
    // we store the data of already calculated images so we don’t have to process them multiple times
    private static $cache = [];

    /**
     * @param $image Base64 encoded image data
     */
    public static function getImageMeta($image)
    {
        if (! is_string($image))
        {
            throw new BadImageException(Translate::t('The value is expected to be a base64-encoded image blob.'));
        }

        $fp = self::createFingerprint($image);

        if (! isset(self::$cache[$fp]))
        {
            try
            {
                $blob = base64_decode($image);
                $imgdata = @getimagesizefromstring($blob);
            }
            catch (Exception $e)
            {
                throw new BadImageException(Translate::t('The value is expected to be a base64-encoded image blob.'));
            }

            if (! $imgdata)
            {
                throw new BadImageException(Translate::t('The value is expected to be a base64-encoded image blob.'));
            }

            self::$cache[$fp] = [
                'fingerprint' => $fp,
                'width' => $imgdata[0],
                'height' => $imgdata[1],
                'type' => $imgdata[2],
                'blob' => $blob
            ];
        }

        return self::$cache[$fp];
    }

    public static function stripImage($image)
    {
        $meta = self::getImageMeta($image);

        $imagick = new Imagick();
        $imagick->readImageBlob($meta['blob']);
        $imagick->stripImage();

        $newBlob = $imagick->getImageBlob();
        $newImage = base64_encode($newBlob);
        $fp = self::createFingerprint($newImage);

        self::$cache[$fp] = [
            'fingerprint' => $fp,
            'blob' => $newBlob
        ] + $meta;

        return $newImage;
    }

    public static function createFingerprint($image)
    {
        // MD5 seems to be fast while reasonably collision-safe
        return md5($image);
    }
}
