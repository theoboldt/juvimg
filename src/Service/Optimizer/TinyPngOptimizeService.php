<?php
/**
 * This file is part of the Juvem package.
 *
 * (c) Erik Theoboldt <erik@theoboldt.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Optimizer;


use App\Juvimg\OptimizedImage;
use App\Juvimg\ResizedImage;
use App\Service\AbstractTinyPngService;
use App\Service\Resizer\OptimizeFailedException;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

class TinyPngOptimizeService extends AbstractTinyPngService implements OptimizingProviderInterface
{
    
    /**
     * @param string|null $apiKeys
     * @param LoggerInterface|null $logger
     * @return TinyPngOptimizeService|null
     */
    public static function create(?string $apiKeys = null, ?LoggerInterface $logger = null): ?TinyPngOptimizeService
    {
        if ($apiKeys) {
            return new self(explode(',', $apiKeys), $logger);
        } else {
            return null;
        }
    }
    
    /**
     * Execute image optimization
     *
     * @param ResizedImage $request Job info
     * @return OptimizedImage Result
     */
    public function optimize(ResizedImage $request): OptimizedImage
    {
        $this->logger->info('Started image optimization using tinypng');
        try {
            $response = $this->client()->post('/shrink', ['body' => (string)$request]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $body = $e->getResponse()->getBody();
                if ($body->getSize() < 1024) {
                    $this->logger->warning('Failed to shrink image: ' . $body->getContents());
                } else {
                    $this->logger->warning('Failed to shrink image');
                }
            }
            throw new OptimizeFailedException('Failed to shrink image', $e->getCode(), $e);
        }
        $result = json_decode($response->getBody(), true);
        $this->logger->info('Finished image optimization using tinypng');
        
        if (is_array($result) && isset($result['output']['url'])) {
            $scaleUrl = $result['output']['url'];
            return new OptimizedImage($scaleUrl);
        } else {
            $this->logger->warning('Image shrink result misses url');
            throw new OptimizeFailedException('Failed to shrink image');
        }
    }
}
