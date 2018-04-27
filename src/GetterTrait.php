<?php

namespace PrePic;

trait GetterTrait
{

    /**
     * Get the new image URL.
     *
     * This also may return the original image URL or false if the original image URL is not valid.
     *
     * @return bool|string
     */
    public function getUrl()
    {
        return $this->image_url;
    }

    /**
     * Get the new image width.
     *
     * @return int|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get the new image height.
     *
     * @return int|null
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get the cropping positions.
     *
     * @return array
     */
    public function getCropPositions()
    {
        return $this->crop;
    }

    /**
     * Get the crop.
     *
     * @return array|bool
     */
    public function getCrop()
    {
        if (empty($this->crop)) {
            return false;
        }

        // Return in the correct order and with 0 and 1 indexes required for
        // 'image_resize_dimensions' from wp-includes/media.php.
        return [
            $this->crop['x'],
            $this->crop['y'],
        ];
    }

    /**
     * Get cropping position on x-axis
     *
     * @return bool|mixed
     */
    public function getCropPositionX()
    {
        if (empty($this->crop['x'])) {
            return false;
        }

        return $this->crop['x'];
    }

    /**
     * Get cropping position on y-axis
     *
     * @return bool|mixed
     */
    public function getCropPositionY()
    {
        if (empty($this->crop['y'])) {
            return false;
        }

        return $this->crop['y'];
    }

}
