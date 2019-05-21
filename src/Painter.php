<?php

declare(strict_types=1);

namespace BobRoss;

use BobRoss\Handlers\PersistenceHandler;
use BobRoss\Utils\UploadHandler;
use BobRoss\ValueObject\Image;

class Painter
{
    /** @var \BobRoss\Utils\UploadHandler */
    private $uploadHandler;
    /** @var \BobRoss\Handlers\PersistenceHandler */
    private $persistenceHandler;

    public function __construct(PersistenceHandler $persistenceHandler)
    {
        $this->persistenceHandler = $persistenceHandler;
    }

    /**
     * Load an image from a form file input
     */
    public function loadFromFileGlobal(string $name) : void
    {
        $this->uploadHandler = new UploadHandler($_FILES[$name]);
    }

    /**
     * Load an image from a system path
     */
    public function loadFromFileSystem(string $path) : void
    {
        $this->uploadHandler = new UploadHandler($path);
    }

    /**
     * Load an image from a given uri
     */
    public function loadFromUri(string $uri, ?string $name = null) : bool
    {
        if (filter_var($uri, FILTER_VALIDATE_URL)) {
            $image     = getimagesize($uri);
            $extension = null;
            switch ($image['mime']) {
                case 'image/gif':
                    $extension = '.gif';
                    break;
                case 'image/png':
                    $extension = '.png';
                    break;
                case 'image/bmp':
                    $extension = '.bmp';
                    break;
                case 'image/jpeg':
                    $extension = '.jpg';
                    break;
                default:
                    return false;
            }

            if ($name === null) {
                $name = uniqid('img-') . $extension;
            } else {
                $name .= '-' . uniqid("", true) . $extension;
            }
            $tmp_path = sys_get_temp_dir() . '/';
            file_put_contents($tmp_path . $name, file_get_contents($uri));
            $this->loadFromFileSystem($tmp_path . $name);
        }

        return false;
    }

    public function convertToJpeg() : void
    {
        $this->uploadHandler->image_convert = 'jpeg';
    }

    public function setJpegQuality(int $quality) : void
    {
        $this->convertToJpeg();
        $this->uploadHandler->jpeg_quality = $quality;
    }

    /**
     * Possibles values are : ''; 'png'; 'jpeg'; 'gif'; 'bmp'
     */
    public function setImageConvert(string $format) : void
    {
        $this->uploadHandler->image_convert = $format;
    }

    /**
     * @param string $url URL of the image
     * @return bool
     */
    public static function checkMinImageSize(string $url, int $width = 100, int $height = 100) : bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $image = getimagesize($url);

        return ($image[0] > $width && $image[1] > $height);
    }

    public function delete(string $path, string $filename) : bool
    {
        return $this->persistenceHandler->deleteFile(rtrim($path, '/') . '/' . $filename);
    }

    /**
     * @param string $dir File system path to save the image to
     */
    public function save(string $dir = 'i') : Image
    {
        $tmp_path = sys_get_temp_dir() . '/';
        $this->uploadHandler->process($tmp_path);

        $image = $this->persistenceHandler->persistFile(
            $tmp_path . $this->uploadHandler->file_dst_name,
            $dir,
            $this->uploadHandler->file_dst_name
        );
        unlink($tmp_path . $this->uploadHandler->file_dst_name);

        return $image;
    }

    /**
     * Save the current image with fixed width
     *
     * @param int    $width the width of the new image
     * @param string $dir   File system path to save the image to
     */
    public function saveFixedWidth(int $width, string $dir = 'i') : Image
    {
        $this->uploadHandler->image_resize  = true;
        $this->uploadHandler->image_ratio_y = true;
        $this->uploadHandler->image_x       = $width;

        $tmp_path = sys_get_temp_dir() . '/';
        $this->uploadHandler->process($tmp_path);

        $image = $this->persistenceHandler->persistFile(
            $tmp_path . $this->uploadHandler->file_dst_name,
            $dir,
            $this->uploadHandler->file_dst_name
        );
        unlink($tmp_path . $this->uploadHandler->file_dst_name);

        return $image;
    }

    /**
     * Save the current image with fixed height
     *
     * @param int    $height the height of the new image
     * @param string $dir    File system path to save the image to
     */
    public function saveFixedHeight(int $height, string $dir = 'i') : Image
    {
        $this->uploadHandler->image_resize  = true;
        $this->uploadHandler->image_ratio_x = true;
        $this->uploadHandler->image_y       = $height;

        $tmp_path = sys_get_temp_dir() . '/';
        $this->uploadHandler->process($tmp_path);

        $image = $this->persistenceHandler->persistFile(
            $tmp_path . $this->uploadHandler->file_dst_name,
            $dir,
            $this->uploadHandler->file_dst_name
        );
        unlink($tmp_path . $this->uploadHandler->file_dst_name);

        return $image;
    }

    /**
     * Save the current image with max width and height keeping ratio
     *
     * @param int    $width  max width of the image
     * @param int    $height max height of the image
     * @param string $dir    File system path to save the image to
     */
    public function saveMaxWidthHeight(int $width, int $height, string $dir = 'i') : Image
    {
        $this->uploadHandler->image_resize = true;
        $this->uploadHandler->image_ratio  = true;
        $this->uploadHandler->image_x      = $width;
        $this->uploadHandler->image_y      = $height;

        $tmp_path = sys_get_temp_dir() . '/';
        $this->uploadHandler->process($tmp_path);

        $image = $this->persistenceHandler->persistFile(
            $tmp_path . $this->uploadHandler->file_dst_name,
            $dir,
            $this->uploadHandler->file_dst_name
        );
        unlink($tmp_path . $this->uploadHandler->file_dst_name);

        return $image;
    }

    /**
     * Save the current image with fixed width and height cropping the exceeding.
     *
     * @param int    $width  width of the thumbnail
     * @param int    $height height of the thumbnail
     * @param string $dir    File system path to save the image to
     */
    public function saveThumb(int $width, int $height, string $dir = 'i') : Image
    {
        $this->uploadHandler->image_resize     = true;
        $this->uploadHandler->image_ratio_crop = true;
        $this->uploadHandler->image_x          = $width;
        $this->uploadHandler->image_y          = $height;

        $tmp_path = sys_get_temp_dir() . '/';
        $this->uploadHandler->process($tmp_path);
        $image = $this->persistenceHandler->persistFile(
            $tmp_path . $this->uploadHandler->file_dst_name,
            $dir,
            $this->uploadHandler->file_dst_name
        );
        unlink($tmp_path . $this->uploadHandler->file_dst_name);

        return $image;
    }

    /**
     * Checks whether the $_FILE is an image
     *
     * @param string $name Name of the $_FILES[] field to be checked
     */
    public static function isImage(string $name) : bool
    {
        if (isset($_FILES[$name])) {
            $tempFile = $_FILES[$name]['tmp_name'];
            if (!empty($tempFile) && file_exists($tempFile)) {
                $image = getimagesize($tempFile);
                switch ($image['mime']) {
                    case 'image/gif':
                    case 'image/png':
                    case 'image/bmp':
                    case 'image/tiff':
                    case 'image/jpeg':
                        return true;
                }
            }
        }

        if (file_exists($name)) {
            $image = getimagesize($name);
            switch ($image['mime']) {
                case 'image/gif':
                case 'image/png':
                case 'image/bmp':
                case 'image/tiff':
                case 'image/jpeg':
                    return true;
            }
        }

        return false;
    }
}
