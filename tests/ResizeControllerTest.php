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

class ResizeControllerTest extends WebTestCase
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

    public function testResized()
    {
        $client = self::createAuthenticatedClient();

        $client->request('POST', '/resized/10/10/50', [], [], [], ImageTest::provideImageInput());
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());


        $expected = [
            'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAACXBIWXMAAA7EAAAOxAGVKw4bAAABNklEQVQY0wXBy24TMRSA4d8+tsfNDE3DNRILxKqId6jYsEPiCVjwlvACLFkgISFxU8KiVJBpQzNJ5mL78H3m1cUz7XolegslcdjvmVXCogmMKbHeCu2/DlewOCmQB54+CGyD8HeXaHeKWGU5VzRbnJbC0PfMz4R7TYLkmAU4TsrnnyNv37xgGlbYGITHdyZevzxn/XtgtRlo94Xn53d5eL/h+vILHz5usKpgMpzGA+1Nwpieq3bL1bXB6MCn7z1/NkecF6GI8u79V05OE0+WS0JV8W11SS8Vy5mhcQEXg4esdKNy2w38WK1ZNB7VmkUMeBFiXeO6vsCQiF54NK8AyKqcxQnxnpSV21Gww+EGA4hRwCLW4sQx5URlR07ciMlHnGfHlDKTeFIBZxWMAI5f7Z461kxF+A/wVZKDbns1OwAAAABJRU5ErkJggg==',
            'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAACXBIWXMAAA7EAAAOxAGVKw4bAAABNklEQVQY0wXBy24TMRSA4d8+tsfNDE3DNRILxKqId6jYsEPiCVjwlvAC7NggISFxU8KiVJBpQzNJ5mL78H3m1cUz7XolegslcdjvmVXCogmMKbHeCu2/DlewOCmQB54+CGyD8HeXaHeKWGU5VzRbnJbC0B+ZnznuNQmSYxbgOCmff468ffOCaVxhYxAe30m8fnnO+vfAajPQ7gvPz+/y8H7D9eUXPnzcYFXBZDiNB9qbhDE9V+2Wq2uD0YFP33v+bI44L0IR5d37r5ycJp4sl4Sq4tvqkl4qljND4wIuBg9Z6Ublthv4sVqzaDyqNYsY8CLEusZ1fYEhEb3waF4BkFU5ixPiPSkrt6Ngh8MNBhCjgEWsxYljyonKjpy4EZOPOM+OKWUm8aQCzioYARy/2j11rJmK8B/xkpKEkBzgBgAAAABJRU5ErkJggg=='
        ];
        $given    = base64_encode($response->getContent());
        $this->assertContains($given, $expected);
    }
}
