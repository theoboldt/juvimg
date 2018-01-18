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

class ResizeImage
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
     * Binary image data, contains original data before resize and resized image after resizing
     *
     * @var string
     */
    private $data;

    /**
     * Is set to true if image is resized
     *
     * @var bool
     */
    private $resized = false;

    /**
     * Time spent for resizing
     *
     * @var double
     */
    private $duration = 0;

    /**
     * ResizeImage constructor.
     *
     * @param int    $targetWidth
     * @param int    $targetHeight
     * @param int    $targetQuality
     * @param string $targetMode
     * @param string $data
     */
    public function __construct($targetWidth, $targetHeight, $targetMode, $data, $targetQuality = 70)
    {
        $this->targetWidth   = $targetWidth;
        $this->targetHeight  = $targetHeight;
        $this->targetQuality = $targetQuality;
        $this->targetMode    = $targetMode;
        $this->data          = $data;
    }

    /**
     * Perform image resize
     *
     * @return void
     */
    private function doResize()
    {
        $startTime = microtime(true);
        $imagine = new Imagine();
        $size    = new Box($this->targetWidth, $this->targetHeight);
        $image   = $imagine->load($this->data);

        $this->data    = $image->thumbnail($size, $this->targetMode)
                               ->get(
                                   $this->getImageType(false),
                                   [
                                       'jpeg_quality'          => $this->targetQuality,
                                       'png_compression_level' => 9
                                   ]
                               );
        $this->resized = true;
        $this->duration = microtime(true)-$startTime;
    }

    /**
     * Access binary string of resized image
     *
     * @return string
     */
    public function data()
    {
        if (!$this->resized) {
            $this->doResize();
        }
        return $this->data;
    }

    /**
     * Provide resulting image
     *
     * @return string
     */
    public function __toString()
    {
        return $this->data();
    }

    /**
     * Get the type of image
     *
     * @param bool $asMimeType Set to true if the mime type should be returned and not jpg or png
     * @return string          Image type as mime type or png or jpg
     */
    public function getImageType($asMimeType = false)
    {
        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($this->data);

        if ($asMimeType) {
            return $mimeType;
        }

        return ($mimeType == 'image/jpeg') ? 'jpg' : 'png';
    }

    /**
     * Get time spent for resize
     *
     * @return float
     */
    public function getDuration(): float {
        return $this->duration;
    }

}