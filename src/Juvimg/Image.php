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

class Image
{
    /**
     * mimeType
     *
     * @var string|null
     */
    protected $mimeType;
    
    /**
     * Binary image data, contains original data before resize and resized image after resizing
     *
     * @var resource|string
     */
    protected $data;
    
    /**
     * Image constructor.
     *
     * @param resource|string $data
     * @param string|null $mimeType
     */
    public function __construct($data, ?string $mimeType = null)
    {
        $this->data     = $data;
        $this->mimeType = $mimeType;
    }
    
    /**
     * Get mime type
     *
     * @return string
     */
    public function getMimeType(): string
    {
        if (!$this->mimeType) {
            $finfo          = new \finfo(FILEINFO_MIME_TYPE);
            $this->mimeType = $finfo->buffer((string)$this->data);
            
        }
        return $this->mimeType;
    }
    
    /**
     * Get the type of image
     *
     * @param bool $asMimeType Set to true if the mime type should be returned and not jpg or png
     * @return string          Image type as mime type or png or jpg
     */
    public function getImageType($asMimeType = false)
    {
        $mimeType = $this->getMimeType();
       
        if ($asMimeType) {
            return $mimeType;
        }
        
        return ($mimeType == 'image/jpeg') ? 'jpg' : 'png';
    }
    
    /**
     * Access binary data of resized image
     *
     * @return resource|string
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * Close handle if open
     *
     * @return void
     */
    public function closeHandle(): void
    {
        if (is_resource($this->data)) {
            fclose($this->data);
        }
    }
    
    /**
     * Provide file content
     *
     * @return string
     */
    public function __toString()
    {
        if (!$this->data) {
            return '';
        }
        if (is_resource($this->data)) {
            $contents = '';
            while (!feof($this->data)) {
                $contents .= fread($this->data, 8192);
            }
            return $contents;
        } else {
            return $this->data;
        }
    }
    
    /**
     * Ensure handle is closed
     */
    public function __destruct()
    {
        $this->closeHandle();
    }
    
}
