<?php
/**
 * This file is part of the JuvImg package.
 *
 * (c) Erik Theoboldt <erik@theoboldt.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace App\Controller;

use App\Juvimg\Image;
use App\Juvimg\OptimizedImage;
use App\Juvimg\ResizedImage;
use App\Juvimg\ResizeImageRequest;
use App\Service\Optimizer\OptimizeFailedException;
use App\Service\Optimizer\TinyPngOptimizeService;
use App\Service\Resizer\ResizeService;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * Optimizer
     *
     * @var TinyPngOptimizeService|null
     */
    private $optimizer;
    
    /**
     * ResizeController constructor.
     *
     * @param ResizeService $resizer
     * @param null|TinyPngOptimizeService $optimizer
     */
    public function __construct(ResizeService $resizer, ?TinyPngOptimizeService $optimizer = null)
    {
        $this->resizer   = $resizer;
        $this->optimizer = $optimizer;
    }

    /**
     * Throw exception if input configuration is invalid
     *
     * @param int        $width   Desired image width
     * @param int        $height  Desired image height
     * @param int        $quality JPG quality setting
     * @param string|int $mode    Boundary mode
     * @throws BadRequestHttpException
     */
    private static function validateSettings(
        int $width,
        int $height,
        int $quality = 70,
        string $mode = ResizeService::MODE_INSET
    ): void {
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
    }
    
    /**
     * Create response from resized image
     *
     * @param ResizedImage $result
     * @return Response
     */
    private static function createResponse(ResizedImage $result): Response
    {
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
        string $mode = ResizeService::MODE_INSET
    ): Response
    {
        self::validateSettings($width, $height, $quality, $mode);

        $contentType = $request->getContentType();
        if (!$contentType) {
            $contentType = $request->getAcceptableContentTypes();
            if (!count($contentType)) {
                $contentType = null;
            }
        }
        
        $result = $this->resizer->resize(
            new ResizeImageRequest($request->getContent(true), $contentType, $height, $mode, $width, $quality)
        );
        
        if ($this->optimizer) {
            try {
                $optimized = $this->optimizer->optimize($result);
                return new RedirectResponse($optimized->getUrl());
            } catch (OptimizeFailedException $e) {
                //intentionally left empty
            }
        }
    
        return self::createResponse($result);
    }
}
