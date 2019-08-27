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
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ManipulatorInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class ImagineResizeService implements ResizingProviderInterface
{
    /**
     * Logger
     *
     * @var AbstractLogger
     */
    private $logger;
    
    /**
     * ResizeController constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * Execute image resize
     *
     * @param ResizeImageRequest $request Job info
     * @return ResizedImage Result
     */
    public function resize(ResizeImageRequest $request): ResizedImage
    {
        $this->logger->info(
            'Start image resizing, peak memory until so far {memory}', ['memory' => memory_get_peak_usage()]
        );
        $data = $request->getData();
        
        $startTime = microtime(true);
        $imagine   = new Imagine();
        $size      = new Box($request->getTargetWidth(), $request->getTargetHeight());
        $image     = $imagine->read($data);
        
        switch ($request->getTargetMode()) {
            case ResizeService::MODE_INSET:
                $method = ManipulatorInterface::THUMBNAIL_INSET;
                break;
            case ResizeService::MODE_OUTBOUND:
                $method = ManipulatorInterface::THUMBNAIL_OUTBOUND;
                break;
            default:
                throw new \RuntimeException('Unavailable resize mode requested');
        }
        $data = $image->thumbnail($size, $method)
                      ->get(
                          $request->getImageType(false),
                          [
                              'jpeg_quality'          => $request->getTargetQuality(),
                              'png_compression_level' => 9,
                          ]
                      );
        $request->closeHandle();
        
        $duration = microtime(true) - $startTime;
        
        $this->logger->info('Finished image resizing, peak memory now {memory}', ['memory' => memory_get_peak_usage()]);
        
        return new ResizedImage($data, $request->getMimeType(), $duration, self::class);
    }
}