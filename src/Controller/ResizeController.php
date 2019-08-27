<?php
/**
 * This file is part of the JuvImg package.
 *
 * (c) Erik Theoboldt <erik@theoboldt.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Juvimg\ResizeImageRequest;
use App\Service\Resizer\ResizeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ResizeController
{
    
    /**
     * Resizer
     *
     * @var ResizeService
     */
    private $resizer;
    
    /**
     * ResizeController constructor.
     *
     * @param \App\Service\Resizer\ResizeService $resizer
     */
    public function __construct(ResizeService $resizer)
    {
        $this->resizer = $resizer;
    }
    
    /**
     * Do resize
     *
     * @param int $width       Desired image width
     * @param int $height      Desired image height
     * @param int $quality     JPG quality setting
     * @param string|int $mode Boundary mode
     * @param Request $request Request containing source data
     * @return Response Response containing resized image
     */
    public function resized(
        Request $request,
        int $width,
        int $height,
        int $quality = 70,
        $mode = ResizeService::MODE_INSET
    )
    {
        $contentType = $request->getContentType();
        if (!$contentType) {
            list($contentType) = $request->getAcceptableContentTypes();
        }
        
        if ($width > 1000 || $width < 1) {
            throw new BadRequestHttpException('Incorrect width transmitted');
        }
        if ($height > 1000 || $height < 1) {
            throw new BadRequestHttpException('Incorrect height transmitted');
        }
        if ($quality > 100 || $quality < 1) {
            throw new BadRequestHttpException('Incorrect quality transmitted');
        }
        if (!in_array($mode, [ResizeService::MODE_INSET, ResizeService::MODE_OUTBOUND], true)) {
            throw new BadRequestHttpException('Incorrect mode transmitted');
        }
        
        $result = $this->resizer->resize(
            new ResizeImageRequest($request->getContent(true), $contentType, $height, $mode, $width, $quality)
        );
        
        return new Response(
            $result->getData(), Response::HTTP_OK,
            [
                'Content-Type'            => $result->getImageType(true),
                'X-Php-Memory-Peak-Usage' => memory_get_peak_usage(),
                'X-Resize-duration'       => $result->getDuration(),
                'X-Resize-method'         => $result->getResizer(),
            ]
        );
    }
}