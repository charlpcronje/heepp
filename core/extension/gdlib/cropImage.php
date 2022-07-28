<?php
namespace core\extension\gdlib;

class CropImage extends \core\extension\Extension {
    private $data;
    private $type;
    private $extension;
    private $srcFile = null;
    private $dstFile = null;
    private $msg;

    function __construct($src, $data, $file) {
        set_time_limit(100);
        $this->data = $data;
        $this->srcFile = $src;
        $this->dstFile = $file;
        $this->setFile();
        $this->crop();
    }

    private function setFile() {
        $type = exif_imagetype($this->srcFile);

        if ($type) {
            $extension = image_type_to_extension($type);
            $this->type = $type;
            $this->extension = $extension;
        }
    }

    private function crop() {
        if (!empty($this->srcFile) && !empty($this->dstFile) && !empty($this->data)) {
            switch ($this->type) {
                case IMAGETYPE_GIF:
                    $src_img = imagecreatefromgif($this->srcFile);
                    break;

                case IMAGETYPE_JPEG:
                    $src_img = imagecreatefromjpeg($this->srcFile);
                    break;

                case IMAGETYPE_PNG:
                    $src_img = imagecreatefrompng($this->srcFile);
                    break;
            }

            if (!$src_img) {
                $this->msg = "Failed to read the image file";
                return;
            }

            $dst_img = imagecreatetruecolor(220, 220);
            $result = imagecopyresampled($dst_img, $src_img, 0, 0, $this->data->x1, $this->data->y1, 220, 220, $this->data->width, $this->data->height);

            if ($result) {
                switch ($this->type) {
                    case IMAGETYPE_GIF:
                        $result = imagegif($dst_img, $this->dstFile);
                        break;

                    case IMAGETYPE_JPEG:
                        $result = imagejpeg($dst_img, $this->dstFile);
                        break;

                    case IMAGETYPE_PNG:
                        $result = imagepng($dst_img, $this->dstFile);
                        break;
                }

                if (!$result) {
                    $this->msg = "Failed to save the cropped image file";
                }
            } else {
                $this->msg = "Failed to crop the image file";
            }

            imagedestroy($src_img);
            imagedestroy($dst_img);
        }
    }
}
