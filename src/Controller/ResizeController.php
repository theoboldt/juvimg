<?php
/**
 * This file is part of the JuvImg package.
 *
 * (c) Erik Theoboldt <erik@theoboldt.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Juvimg\ResizeImage;
use Imagine\Image\ImageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ResizeController {

    /**
     * Main event
     *
     * @param int $width
     * @param int $height
     * @param int $quality
     * @param string $mode
     * @param Request $request
     * @return Response
     */
    public function resized(
        Request $request, int $width, int $height, int $quality = 70, $mode = ImageInterface::THUMBNAIL_INSET
    ) {
        if ($width > 1000 || $width < 1) {
            throw new BadRequestHttpException('Incorrect width transmitted');
        }
        if ($height > 1000 || $height < 1) {
            throw new BadRequestHttpException('Incorrect height transmitted');
        }
        if ($quality > 100 || $quality < 1) {
            throw new BadRequestHttpException('Incorrect quality transmitted');
        }
        if (!in_array($mode, [ImageInterface::THUMBNAIL_INSET, ImageInterface::THUMBNAIL_OUTBOUND], true)) {
            throw new BadRequestHttpException('Incorrect mode transmitted');
        }

        $image = new ResizeImage($width, $height, $mode, $request->getContent(), $quality);

        return new Response(
            $image->data(), Response::HTTP_OK, ['Content-Type', $image->getImageType(true)]
        );
    }
}