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


use App\Kernel;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    /**
     * {@inheritDoc}
     */
    protected static function getKernelClass()
    {
        return Kernel::class;
    }

    /**
     * Creates a authenticated KernelBrowser.
     *
     * @param array $options An array of options to pass to the createKernel method
     * @param array $server  An array of server parameters
     *
     * @return KernelBrowser A KernelBrowser instance
     */
    protected static function createAuthenticatedClient(array $options = [], array $server = [])
    {
        return static::createClient(
            $options,
            array_merge(
                $server,
                [
                    'PHP_AUTH_USER' => 'user',
                    'PHP_AUTH_PW'   => 'z15f05804d98d016925ff428fc86d73a',
                ]
            )
        );
    }

    public function testEnsureAuthenticationRequired(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $response = $client->getResponse();

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEmpty($response->getContent());
    }


    public function testHealthCheck()
    {
        $client = self::createAuthenticatedClient();

        $client->request('GET', '/');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }
}
