<?php
/**
 * This file is part of the Juvem package.
 *
 * (c) Erik Theoboldt <erik@theoboldt.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Service;

use GuzzleHttp\Client;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

abstract class AbstractTinyPngService
{
    
    /**
     * API key for TinyPNG service
     *
     * @var string
     */
    protected $apiKey;
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
     * @param string $apiKey
     * @param LoggerInterface $logger
     */
    public function __construct(string $apiKey, LoggerInterface $logger)
    {
        $this->apiKey = $apiKey;
        $this->logger = $logger;
    }
    
    /**
     * Configures the Guzzle client for juvimg service
     *
     * @return Client
     */
    protected function client()
    {
        if (!$this->client) {
            $this->client = new Client(
                [
                    'base_uri' => 'https://api.tinify.com',
                    'auth'     => ['api', $this->apiKey],
                ]
            );
        }
        return $this->client;
    }
}