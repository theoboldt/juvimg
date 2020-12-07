<?php
/**
 * This file is part of the Juvem package.
 *
 * (c) Erik Theoboldt <erik@theoboldt.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace App\Service\Resizer;

use App\Juvimg\ResizedImage;
use App\Juvimg\ResizeImageRequest;
use App\Service\AbstractTinyPngService;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

class TinyPngResizeService extends AbstractTinyPngService implements ResizingProviderInterface
{
    /**
     * @param string|null $apiKeys
     * @param LoggerInterface|null $logger
     * @return TinyPngResizeService|null
     */
    public static function create(?string $apiKeys = null, ?LoggerInterface $logger = null): ?TinyPngResizeService
    {
        if ($apiKeys) {
            return new self(explode(',', $apiKeys), $logger);
        } else {
            return null;
        }
    }
    
    /**
     * Execute image resize
     *
     * @param ResizeImageRequest $request Job info
     * @return ResizedImage Result
     */
    public function resize(ResizeImageRequest $request): ResizedImage
    {
        $startTime = microtime(true);
        $this->logger->info(
            'Started image shrinking using tinypng, peak memory now {memory}',
            ['memory' => memory_get_peak_usage()]
        );
        
        $scaleUrl = $this->tryShrinkImage($request);
        
        switch ($request->getTargetMode()) {
            case ResizeService::MODE_INSET:
                $method = 'fit';
                break;
            case ResizeService::MODE_OUTBOUND:
                $method = 'cover';
                break;
            default:
                throw new \RuntimeException('Unavailable resize mode requested');
        }
        
        $action = [
            'resize' => [
                'method' => $method,
                'width'  => $request->getTargetWidth(),
                'height' => $request->getTargetHeight(),
            ],
        ];
        
        $this->logger->info(
            'Started image resizing using tinypng, peak memory now {memory}',
            ['memory' => memory_get_peak_usage()]
        );
        $response = $this->client()->post(
            $scaleUrl,
            ['json' => $action]
        );
        $duration = microtime(true) - $startTime;
        $request->closeHandle();
        
        $this->logger->info(
            'Finished image resizing using tinypng, peak memory now {memory}',
            ['memory' => memory_get_peak_usage()]
        );
        
        return new ResizedImage($response->getBody(), $request->getMimeType(), $duration, self::class);
    }

    /**
     * Try to shrink image and provide url of generated file
     *
     * @param ResizeImageRequest $request
     * @return string
     */
    private function tryShrinkImage(ResizeImageRequest $request): string
    {
        try {
            $response = $this->client()->post(
                '/shrink',
                ['body' => (string)$request]
            );
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $body = $e->getResponse()->getBody();
                if ($body->getSize() < 1024) {
                    $this->logger->warning('Failed to shrink image: ' . $body->getContents());
                } else {
                    $this->logger->warning('Failed to shrink image');
                }
            }
            throw new ResizeFailedException('Failed to shrink image', $e->getCode(), $e);
        }
        $result = json_decode($response->getBody(), true);
        $this->logger->info(
            'Finished image shrinking using tinypng, peak memory now {memory}',
            ['memory' => memory_get_peak_usage()]
        );

        if (is_array($result) && isset($result['output']['url'])) {
            return $result['output']['url'];
        } else {
            $this->logger->warning('Image shrink result misses url');
            throw new ResizeFailedException('Failed to shrink image');
        }
    }
}
