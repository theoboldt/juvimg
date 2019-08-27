<?php
/**
 * This file is part of the Juvem package.
 *
 * (c) Erik Theoboldt <erik@theoboldt.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Resizer;


use App\Juvimg\ResizedImage;
use App\Juvimg\ResizeImageRequest;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class ResizeService implements ResizingProviderInterface
{
    const MODE_INSET = 'inset';
    
    const MODE_OUTBOUND = 'outbound';
    
    /**
     * Logger
     *
     * @var AbstractLogger
     */
    private $logger;
    
    /**
     * imagine
     *
     * @var ImagineResizeService
     */
    private $imagine;
    
    /**
     * tinyPng
     *
     * @var TinyPngResizeService|null
     */
    private $tinyPng = null;
    
    /**
     * ResizeController constructor.
     *
     * @param LoggerInterface $logger
     * @param ImagineResizeService $imagine
     * @param TinyPngResizeService|null $tinyPng
     */
    public function __construct(
        LoggerInterface $logger,
        ImagineResizeService $imagine,
        ?TinyPngResizeService $tinyPng = null
    )
    {
        $this->logger  = $logger;
        $this->imagine = $imagine;
        $this->tinyPng = $tinyPng;
    }
    
    /**
     * Execute image resize
     *
     * @param ResizeImageRequest $request Job info
     * @return ResizedImage Result
     */
    public function resize(ResizeImageRequest $request): ResizedImage
    {
        if ($this->tinyPng) {
            try {
                return $this->tinyPng->resize($request);
            } catch (ResizeFailedException $e) {
                $this->logger->notice('Failed resizing using tinypng, trying imagine');
            }
        }
        return $this->imagine->resize($request);
    }
    
}