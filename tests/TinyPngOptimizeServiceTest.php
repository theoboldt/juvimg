<?php
/**
 * This file is part of the Juvem package.
 *
 * (c) Erik Theoboldt <erik@theoboldt.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Tests;


use App\Service\Optimizer\TinyPngOptimizeService;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use ReflectionClass;

class TinyPngOptimizeServiceTest extends TestCase
{
    public function testExplodeKeys(): void
    {
        $service = TinyPngOptimizeService::create('first,second', new NullLogger());

        $reflection = new ReflectionClass($service);
        $property   = $reflection->getProperty('apiKeys');
        $property->setAccessible(true);
        $given = $property->getValue($service);

        $this->assertEquals(['first', 'second'], $given);
    }

}
