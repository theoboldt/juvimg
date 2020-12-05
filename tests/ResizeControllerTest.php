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


use App\Controller\ResizeController;
use App\Juvimg\OptimizedImage;
use App\Kernel;
use App\Service\Optimizer\TinyPngOptimizeService;
use App\Service\Resizer\ImagineResizeService;
use App\Service\Resizer\OptimizeFailedException;
use App\Service\Resizer\ResizeService;
use Imagine\Exception\RuntimeException;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    public function testResizedInset()
    {
        $client = self::createAuthenticatedClient();

        $client->request('POST', '/resized/10/10/50/inset', [], [], [], ImageTest::provideImageInput());
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $expected = [
            'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAACXBIWXMAAA7EAAAOxAGVKw4bAAABNklEQVQY0wXBy24TMRSA4d8+tsfNDE3DNRILxKqId6jYsEPiCVjwlvACLFkgISFxU8KiVJBpQzNJ5mL78H3m1cUz7XolegslcdjvmVXCogmMKbHeCu2/DlewOCmQB54+CGyD8HeXaHeKWGU5VzRbnJbC0PfMz4R7TYLkmAU4TsrnnyNv37xgGlbYGITHdyZevzxn/XtgtRlo94Xn53d5eL/h+vILHz5usKpgMpzGA+1Nwpieq3bL1bXB6MCn7z1/NkecF6GI8u79V05OE0+WS0JV8W11SS8Vy5mhcQEXg4esdKNy2w38WK1ZNB7VmkUMeBFiXeO6vsCQiF54NK8AyKqcxQnxnpSV21Gww+EGA4hRwCLW4sQx5URlR07ciMlHnGfHlDKTeFIBZxWMAI5f7Z461kxF+A/wVZKDbns1OwAAAABJRU5ErkJggg==',
            'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAACXBIWXMAAA7EAAAOxAGVKw4bAAABNklEQVQY0wXBy24TMRSA4d8+tsfNDE3DNRILxKqId6jYsEPiCVjwlvAC7NggISFxU8KiVJBpQzNJ5mL78H3m1cUz7XolegslcdjvmVXCogmMKbHeCu2/DlewOCmQB54+CGyD8HeXaHeKWGU5VzRbnJbC0B+ZnznuNQmSYxbgOCmff468ffOCaVxhYxAe30m8fnnO+vfAajPQ7gvPz+/y8H7D9eUXPnzcYFXBZDiNB9qbhDE9V+2Wq2uD0YFP33v+bI44L0IR5d37r5ycJp4sl4Sq4tvqkl4qljND4wIuBg9Z6Ublthv4sVqzaDyqNYsY8CLEusZ1fYEhEb3waF4BkFU5ixPiPSkrt6Ngh8MNBhCjgEWsxYljyonKjpy4EZOPOM+OKWUm8aQCzioYARy/2j11rJmK8B/xkpKEkBzgBgAAAABJRU5ErkJggg==',
        ];
        $given    = base64_encode($response->getContent());
        $this->assertContains($given, $expected);
    }

    public function testResizedOutbound()
    {
        $client = self::createAuthenticatedClient();

        $client->request('POST', '/resized/10/10/50/outbound', [], [], [], ImageTest::provideImageInput());
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $expected = [
            'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAACXBIWXMAAA7EAAAOxAGVKw4bAAABNklEQVQY0wXBy24TMRSA4d8+tsfNDE3DNRILxKqId6jYsEPiCVjwlvACLFkgISFxU8KiVJBpQzNJ5mL78H3m1cUz7XolegslcdjvmVXCogmMKbHeCu2/DlewOCmQB54+CGyD8HeXaHeKWGU5VzRbnJbC0PfMz4R7TYLkmAU4TsrnnyNv37xgGlbYGITHdyZevzxn/XtgtRlo94Xn53d5eL/h+vILHz5usKpgMpzGA+1Nwpieq3bL1bXB6MCn7z1/NkecF6GI8u79V05OE0+WS0JV8W11SS8Vy5mhcQEXg4esdKNy2w38WK1ZNB7VmkUMeBFiXeO6vsCQiF54NK8AyKqcxQnxnpSV21Gww+EGA4hRwCLW4sQx5URlR07ciMlHnGfHlDKTeFIBZxWMAI5f7Z461kxF+A/wVZKDbns1OwAAAABJRU5ErkJggg==',
            'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAACXBIWXMAAA7EAAAOxAGVKw4bAAABNklEQVQY0wXBy24TMRSA4d8+tsfNDE3DNRILxKqId6jYsEPiCVjwlvAC7NggISFxU8KiVJBpQzNJ5mL78H3m1cUz7XolegslcdjvmVXCogmMKbHeCu2/DlewOCmQB54+CGyD8HeXaHeKWGU5VzRbnJbC0B+ZnznuNQmSYxbgOCmff468ffOCaVxhYxAe30m8fnnO+vfAajPQ7gvPz+/y8H7D9eUXPnzcYFXBZDiNB9qbhDE9V+2Wq2uD0YFP33v+bI44L0IR5d37r5ycJp4sl4Sq4tvqkl4qljND4wIuBg9Z6Ublthv4sVqzaDyqNYsY8CLEusZ1fYEhEb3waF4BkFU5ixPiPSkrt6Ngh8MNBhCjgEWsxYljyonKjpy4EZOPOM+OKWUm8aQCzioYARy/2j11rJmK8B/xkpKEkBzgBgAAAABJRU5ErkJggg==',
        ];
        $given    = base64_encode($response->getContent());
        $this->assertContains($given, $expected);
    }
    
    /**
     * @return \int[][]
     */
    public function provideInvalidDimensions(): array
    {
        return [
            [0],
            [1001],
        ];
    }

    /**
     * @dataProvider provideInvalidDimensions
     * @param int $width
     * @return void
     */
    public function testValidationWidth(int $width): void
    {
        $client = self::createAuthenticatedClient();

        $client->request('POST', '/resized/' . $width . '/10/50', [], [], [], ImageTest::provideImageInput());
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @dataProvider provideInvalidDimensions
     * @param int $height
     * @return void
     */
    public function testValidationHeight(int $height): void
    {
        $client = self::createAuthenticatedClient();

        $client->request('POST', '/resized/10/' . $height . '/50', [], [], [], ImageTest::provideImageInput());
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @return \int[][]
     */
    public function provideInvalidPercentage(): array
    {
        return [
            [0],
            [101],
        ];
    }

    /**
     * @dataProvider provideInvalidPercentage
     * @param int $percentage
     * @return void
     */
    public function testValidationPercentage(int $percentage): void
    {
        $client = self::createAuthenticatedClient();

        $client->request('POST', '/resized/10/10/' . $percentage, [], [], [], ImageTest::provideImageInput());
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testValidationMode(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $resizer = new ResizeService(new NullLogger(), new ImagineResizeService(new NullLogger()));

        $controller = new ResizeController($resizer);

        $controller->resized(new Request(), 10, 10, 10, 'unknown');
    }

    public function testValidationMimeType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectErrorMessage('An image could not be created from the given input');

        $resizer = new ResizeService(new NullLogger(), new ImagineResizeService(new NullLogger()));

        $controller = new ResizeController($resizer);

        $request = new Request([], [], ['CONTENT_TYPE' => 'unknown']);

        $controller->resized($request, 10, 10, 10, 'inset');
    }
    
    public function testOptimizer(): void
    {
        $optimizer = $this->getMockBuilder(TinyPngOptimizeService::class)
                          ->setConstructorArgs([['test'], new NullLogger()])
                          ->getMock();
        $optimizer->method('optimize')->willReturn(new OptimizedImage('https://www.example.com/test'));

        $resizer = new ResizeService(new NullLogger(), new ImagineResizeService(new NullLogger()));

        $controller = new ResizeController($resizer, $optimizer);

        $request = new Request([], [], [], [], [], [], ImageTest::provideImageInput());

        $response = $controller->resized($request, 10, 10, 10, 'inset');

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    
    public function testOptimizerExceptionIsIgnored(): void
    {
        $optimizer = $this->getMockBuilder(TinyPngOptimizeService::class)
                          ->setConstructorArgs([['test'], new NullLogger()])
                          ->getMock();
        $optimizer->method('optimize')->willThrowException(new OptimizeFailedException());

        $resizer = new ResizeService(new NullLogger(), new ImagineResizeService(new NullLogger()));

        $controller = new ResizeController($resizer, $optimizer);

        $request = new Request([], [], [], [], [], [], ImageTest::provideImageInput());

        $response = $controller->resized($request, 10, 10, 10, 'inset');

        $this->assertInstanceOf(Response::class, $response);
    }
}
