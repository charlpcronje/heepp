<?php
namespace core\element\base;
use core\Element;
use core\extension\gdlib\SimpleImage;

class image extends Element {
    public $src;
    public $width;
    public $height;
    public $imagecolor;
    public $attr = array();
    public $backgroundImage;

    public function setSrc($src) {
        $this->src = $src;
        return $this;
    }

    public function setHeight($height) {
        $this->height = $height;
        return $this;
    }

    public function setWidth($width) {
        $this->width = $width;
        return $this;
    }

    private function hex2rgb($hex,$alpha = false) {
        $hex = str_replace('#','',$hex) ;
        if (strlen($hex) == 6) {
            $rgb['r'] = hexdec(substr($hex, 0, 2)) ;
            $rgb['g'] = hexdec(substr($hex, 2, 2)) ;
            $rgb['b'] = hexdec(substr($hex, 4, 2)) ;
        } else if (strlen($hex) == 3) {
            $rgb['r'] = hexdec(str_repeat(substr ($hex, 0, 1), 2)) ;
            $rgb['g'] = hexdec(str_repeat(substr ($hex, 1, 1), 2)) ;
            $rgb['b'] = hexdec(str_repeat(substr ($hex, 2, 1), 2)) ;
        } else {
            $rgb['r'] = '0' ;
            $rgb['g'] = '0' ;
            $rgb['b'] = '0' ;
        }
        if ($alpha) {
            $rgb['a'] = $alpha ;
        }
        return $rgb ;
    }

    public function render() {
        // Check if the image comes from another project
        if (!file_exists(realpath($this->src)) && strpos($this->src,'..'.DS) !== false) {
            $imgParts         = explode('..'.DS,$this->src);
            $beginWithProject = $imgParts[1];
            $projectsPath     = $imgParts[0];
            $project          = explode(DS,$beginWithProject)[0];
            if (in_array($project, unserialize(PROJECT_LIST))) {
                $this->src = str_replace(PROJECT.DS,'',$projectsPath).$beginWithProject;
            }
        }
        $filename = basename($this->src);
        $this->element = 'img';
        $this->src = urlToPath($this->src);
        if (file_exists($this->src) && !is_dir($this->src)) {
            $path = str_replace($filename,'',$this->src);
            //Resize Image
            if (!empty($this->width) && !empty($this->height)) {
                if (!file_exists($path.$this->width.'_'.$this->height.'_'.$filename)) {
                    $image = new SimpleImage($this->src);
                    $image->resize($this->width, $this->height);
                    $image->save($path.$this->width.'_'.$this->height.'_'.$filename);
                }
                $this->attr['src'] = pathToUrl($path.$this->width.'_'.$this->height.'_'.$filename);
            } elseif (!empty($this->width)) {
                if (!file_exists($path.$this->width.'__'.$filename)) {
                    $image = new SimpleImage($this->src);
                    $image->resizeToWidth($this->width);
                    $image->save($path.$this->width.'_'.$this->height.'_'.$filename);
                }
                $this->attr['src'] = pathToUrl($path.$this->width.'_'.$this->height.'_'.$filename);
            } elseif (!empty($this->height)) {
                if (!file_exists($path.'__'.$this->height.$filename)) {
                    $image = new SimpleImage($this->src);
                    $image->resizeToHeight($this->height);
                    $image->save($path.'__'.$this->height.$filename);
                }
                $this->attr['src'] = pathToUrl($path.'__'.$this->height.$filename);
            }

            $filename = basename($this->attr['src']);
            $path = str_replace($filename,'',$this->attr['src']);
            if (!empty($this->imagecolor)) {
                $rgb = $this->hex2rgb($this->imagecolor,true);
                $im = imagecreatefrompng($this->attr['src']);
                imagealphablending($im, false);
                imagesavealpha($im,true);
                //imagefilter($im,IMG_FILTER_COLORIZE,0,0,0);
                imagefilter($im,IMG_FILTER_COLORIZE,$rgb['r'],$rgb['g'],$rgb['b']);
                imagepng($im,$path.$this->imagecolor.$filename);
                $this->attr['src'] = $path.$this->imagecolor.$filename;
            }

            //Add Background to image
            if (!empty($this->backgroundImage)) {
                if (file_exists($this->backgroundImage)) {
                    $imageSize = getimagesize($this->attr['src']);
                    //Get Image and Background widths and heights and get the center position of the background image
                    $imageWidth = $imageSize[0];
                    $imageHeight = $imageSize[1];
                    $backgroundSize = getimagesize($this->backgroundImage);
                    $backgroundWidth = $backgroundSize[0];
                    $backgroundHeight = $backgroundSize[1];
                    $marginLeft = round(($backgroundWidth - $imageWidth) / 2,0);
                    $marginTop = round(($backgroundHeight - $imageHeight) / 2,0);

                    //New Object for Image
                    $image = imagecreatetruecolor($imageWidth, $imageHeight);
                    imagealphablending($image, false);
                    $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
                    imagefill($image, 0, 0, $transparent);
                    imagesavealpha($image, true);
                    imagealphablending($image, true);

                    // copy the source to the new resource
                    $source = imagecreatefrompng($this->attr['src']);
                    imagecopyresampled($image, $source, 0, 0, 0, 0, $imageWidth, $imageHeight, $imageWidth, $imageHeight);

                    //Create new blank Image
                    $img = imagecreatetruecolor($backgroundWidth, $backgroundHeight);
                    imagealphablending($img, false);
                    $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
                    imagefill($img, 0, 0, $transparent);
                    imagesavealpha($img, true);
                    imagealphablending($img, true);

                    //Get Background Image
                    $background = imagecreatefrompng($this->backgroundImage);

                    //Merge Images
                    imagecopyresampled($img, $background, 0, 0, 0, 0, $backgroundWidth, $backgroundHeight, $backgroundWidth, $backgroundHeight);
                    imagecopyresampled($img, $image, $marginLeft, $marginTop, 0, 0, $imageWidth, $imageHeight, $imageWidth, $imageHeight);
                    imagepng($img,$this->attr['src']);
                } else {
                    $this->setError('Background Image Not Found');
                }
            }
        }

        parent::render();
        return $this->html;
    }
}
