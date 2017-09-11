<?php
declare(strict_types=1);
/*
 * @package    agitation/images-bundle
 * @link       http://github.com/agitation/images-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\ImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends Controller
{
    protected $ttl = 604800; // we set a long (7 day) TTL, therefore a cache-buster with the fingerprint should be used

    public function getAction(Request $request, $type, $id)
    {
        $image = $this->container->get('agit.images.loader')->getImage($type, $id);

        $response = new Response();
        $response->headers->set('Expires', date('D, d M Y H:i:s T', time() + $this->ttl));
        $response->headers->set('Etag', $image->getFingerprint());
        $response->headers->set('Cache-Control', 'public, max-age=' . $this->ttl);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Credentials', 'false');

        if ($request->getMethod() !== 'OPTIONS')
        {
            $etag = $request->headers->get('if-none-match', null, true);

            if ($etag && $etag === $image->getFingerprint())
            {
                $response->setStatusCode(304);
            }
            else
            {
                $content = base64_decode($image->getData());
                $response->setStatusCode(200);
                $response->setContent($content);
                $response->headers->set('Content-Length', strlen($content));
                $response->headers->set('Content-Type', image_type_to_mime_type($image->getType()));
            }
        }

        return $response;
    }
}
