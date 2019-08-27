<?php
/**
 * This file is part of the Juvem package.
 *
 * (c) Erik Theoboldt <erik@theoboldt.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Juvimg;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ResizeImageRequest extends Image
{
    /**
     * @var int
     */
    private $targetWidth;
    
    /**
     * @var int
     */
    private $targetHeight;
    
    /**
     * @var int
     */
    private $targetQuality;
    
    /**
     * @var string
     */
    private $targetMode;
    
    /**
     * ResizeImageRequest constructor.
     *
     * @param resource $data
     * @param null|string $mimeType
     * @param int $targetHeight
     * @param string $targetMode
     * @param int $targetWidth
     * @param int $targetQuality
     */
    public function __construct($data, ?string $mimeType, $targetHeight, $targetMode, $targetWidth, $targetQuality = 70)
    {
        $this->targetWidth   = $targetWidth;
        $this->targetHeight  = $targetHeight;
        $this->targetQuality = $targetQuality;
        $this->targetMode    = $targetMode;
        parent::__construct($data, $mimeType);
    }
    
    /**
     * @return int
     */
    public function getTargetWidth(): int
    {
        return $this->targetWidth;
    }
    
    /**
     * @return int
     */
    public function getTargetHeight(): int
    {
        return $this->targetHeight;
    }
    
    /**
     * @return int
     */
    public function getTargetQuality(): int
    {
        return $this->targetQuality;
    }
    
    /**
     * @return string
     */
    public function getTargetMode(): string
    {
        return $this->targetMode;
    }
    
}