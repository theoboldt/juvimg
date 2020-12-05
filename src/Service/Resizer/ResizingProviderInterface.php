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

interface ResizingProviderInterface
{
    /**
     * Execute image resize
     *
     * @param ResizeImageRequest $request Job info
     * @return ResizedImage Result
     */
    public function resize(ResizeImageRequest $request): ResizedImage;
}
