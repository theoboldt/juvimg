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


class ResizedImage extends Image
{
    /**
     * Time spent for resizing
     *
     * @var double
     */
    private $duration = 0;
    
    /**
     * Responsible resizer
     *
     * @var string
     */
    private $resizer;
    
    /**
     * ResizedImage constructor.
     *
     * @param $data
     * @param string|null $mimeType
     * @param float $duration
     * @param string $resizer
     */
    public function __construct($data, ?string $mimeType, float $duration, string $resizer)
    {
        $this->duration = $duration;
        $this->resizer  = $resizer;
        parent::__construct($data, $mimeType);
    }
    
    /**
     * Get time spent for resize
     *
     * @return float
     */
    public function getDuration(): float
    {
        return $this->duration;
    }
    
    /**
     * @return string
     */
    public function getResizer(): string
    {
        return $this->resizer;
    }
}