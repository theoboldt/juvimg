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

namespace App\Service\Optimizer;

use App\Juvimg\OptimizedImage;
use App\Juvimg\ResizedImage;

interface OptimizingProviderInterface
{
    /**
     * Execute image optimization
     *
     * @param ResizedImage $request Job info
     * @return OptimizedImage Result
     */
    public function optimize(ResizedImage $request): OptimizedImage;
}
