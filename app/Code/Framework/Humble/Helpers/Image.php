<?php
namespace Code\Framework\Humble\Helpers;
class Image extends Helper
{
    private $image          = null;
    private $imageHeight    = 0;
    private $imageWidth     = 0;
    private $extension      = '';

    public function __construct() {
        parent::__construct();
    }

    //--------------------------------------------------------------------------
    //
    //--------------------------------------------------------------------------
    public function getClassName()
    {
        return __CLASS__;
    }

    //--------------------------------------------------------------------------------------------------
    // Resizes to a new height
    //--------------------------------------------------------------------------------------------------
    public function resize($newH=1248) {
        if ($this->image) {
            $newH = (is_numeric($newH)) ? $newH : 1248;
            if ($this->imageHeight && $newH) {
                if ($this->imageHeight > $newH) {
                    $ratio 				= $newH / $this->imageHeight;
                    $newW 				= floor($this->imageWidth * $ratio);
                    $tmp_img			= imagecreatetruecolor($newW,$newH);
                    imagecopyresampled($tmp_img,$this->image,0,0,0,0,$newW,$newH,$this->imageWidth,$this->imageHeight);
                    $this->image 		= $tmp_img;
                    $this->imageWidth 	= imageSX($this->image);
                    $this->imageHeight 	= imageSY($this->image);
                }
            }
        }
    }

    //--------------------------------------------------------------------------------------------------
    // flip on horizontal axis
    //--------------------------------------------------------------------------------------------------
    public function crop($startX,$startY,$endX,$endY) {
        if ($this->image) {
            $canvas             = imagecreatetruecolor($endX-$startX, $endY-$startY);
            imagecopy($canvas,$this->image,0,0,$startX,$startY,$endX,$endY);
            $this->image        = $canvas;
            $this->imageWidth 	= imageSX($this->image);
            $this->imageHeight 	= imageSY($this->image);
        }
    }

    //--------------------------------------------------------------------------------------------------
    // flip on horizontal axis
    //--------------------------------------------------------------------------------------------------
    public function flip() {
        if ($this->image) {

        }
    }

    //--------------------------------------------------------------------------------------------------
    // flip on vertical axis
    //--------------------------------------------------------------------------------------------------
    public function mirror() {
        if ($this->image) {

        }
    }

    //--------------------------------------------------------------------------------------------------
    //
    //--------------------------------------------------------------------------------------------------
    public function rotate($degrees) {
        if ($this->image) {
            $this->image	= imagerotate($this->image, $degrees, 0);
        }
    }

    //--------------------------------------------------------------------------------------------------
    //
    //--------------------------------------------------------------------------------------------------
    public function generateThumbnail($output,$type='jpg') {
        if ($this->image) {
            $newH = 250;
            $ratio 				= $newH / $this->imageHeight;
            $newW 				= $this->imageWidth * $ratio;
            $tmp_img			= ImageCreateTrueColor($newW,$newH);
            imagecopyresampled($tmp_img,$this->image,0,0,0,0,$newW,$newH,$this->imageWidth,$this->imageHeight);
            switch ($type) {
                case "jpg"  :   imagejpeg($tmp_img,$output);
                                break;
                case "jpeg" :   imagejpeg($tmp_img,$output);
                                break;
                case "png"  :   imagepng($tmp_img,$output);
                                break;
                case "gif"  :   imagegif($tmp_img,$output);
                                break;
                default     :   break;
            }
        }
    }

    //--------------------------------------------------------------------------------------------------
    //
    //--------------------------------------------------------------------------------------------------
    public function writePNG($file=false) {
        $didit = false;
        if ($this->image && $file) {
            $didit = imagepng($this->image,$file);
        }
        return $didit;
    }

    //--------------------------------------------------------------------------------------------------
    //
    //--------------------------------------------------------------------------------------------------
    public function writeJPG($file=false) {
        $didit = false;
        if ($this->image && $file) {
            $didit = imagejpeg($this->image,$file);
        }
        return $didit;
    }

    //--------------------------------------------------------------------------------------------------
    //
    //--------------------------------------------------------------------------------------------------
    public function writeGIF($file=false) {
        $didit = false;
        if ($this->image && $file) {
            $didit = imagegif($this->image,$file);
        }
        return $didit;
    }

    //--------------------------------------------------------------------------------------------------
    //
    //--------------------------------------------------------------------------------------------------
    public function write($file=false) {
        //based on file extension, write out image
    }

    //--------------------------------------------------------------------------------------------------
    // Getters/Setters
    //--------------------------------------------------------------------------------------------------
    public function setImage($arg=false) {
        if ($arg) {
            $this->setLocation($arg);
            $this->image        = imagecreatefromstring($imageSource = file_get_contents($arg));
            $this->extension    = substr($arg,strlen($arg)-strrpos($arg,'.'));
            $this->imageWidth 	= imageSX($this->image);
            $this->imageHeight 	= imageSY($this->image);
            return $imageSource;
        }

    }

    /* --------------------------------- */
    public function fetch($arg=false) {
        if ($arg) {
            return $this->setImage($arg);
        }
    }

    /* --------------------------------- */
    public function setSource($arg=false) {
        $this->image = null;
        if ($arg) {
            $this->image        = @imagecreatefromstring($arg);
            if ($this->image) {
                $this->imageWidth 	= imageSX($this->image);
                $this->imageHeight 	= imageSY($this->image);
            }
        }
        return ($this->image !== null);
    }

    public function getImage()          {   return $this->image;                }
    public function getImageHeight()    {   return $this->imageHeight;          }
    public function getImageWidth()     {   return $this->imageWidth;           }

}
?>