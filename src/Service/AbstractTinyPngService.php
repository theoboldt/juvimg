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


namespace App\Service;

use GuzzleHttp\Client;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

abstract class AbstractTinyPngService
{
    
    /**
     * API key for TinyPNG service
     *
     * @var array|string[]
     */
    protected $apiKeys = [];
    /**
     * Logger
     *
     * @var AbstractLogger
     */
    protected $logger;
    
    /**
     * Cached HTTP client
     *
     * @var Client
     */
    protected $client;
    
    /**
     * TinyPngResizeService constructor.
     *
     * @param array|string[] $apiKeys
     * @param LoggerInterface $logger
     */
    public function __construct(array $apiKeys, LoggerInterface $logger)
    {
        $this->apiKeys = $apiKeys;
        $this->logger  = $logger;
    }
    
    /**
     * Configures the Guzzle client for juvimg service
     *
     * @return Client
     */
    protected function client(): Client
    {
        $index  = \array_rand($this->apiKeys);
        $apiKey = $this->apiKeys[$index];
        if (!$this->client) {
            $this->client = new Client(
                [
                    'base_uri' => 'https://api.tinify.com',
                    'auth'     => ['api', $apiKey],
                ]
            );
        }
        return $this->client;
    }
}