<?php
/**
 * Image.class.php 图片处理类
 * ----------------------------------------------------------------
 * OldCMS,site:http://www.oldcms.com
 */
if(!defined('IN_OLDCMS')) { die('Access Denied');
}

class Image
{
    public $error='',$imgObj=null,$imgType='';
    function __construct($imgObj=null)
    {
        if(!empty($imgObj)) {
            $this->imgObj=$imgObj;
            $this->imgType=FileSuffix($imgObj['name']);
        }
    }
    /**
     Upload 上传
     $imgName string 图片名称(上传到的位置)
     */
    public function Upload($imgName='')
    {
        // 禁用上传图片
    }
    
    /**
     Resize 生成缩略图
     */
    public static function Resize($oldImg,$width=200,$height=200,$newImg,$fixed=false)
    {
        if(!file_exists($oldImg)) { return false;
        }
        //生成图片处理对象
        $pathInfo=pathinfo($oldImg);
        $imgType=strtolower($pathInfo['extension']);
        switch($imgType){
        case 'jpg':
        case 'jpeg':
            $im=@imagecreatefromjpeg($oldImg);
            break;
        case 'png':
            $im=@imagecreatefrompng($oldImg);
            break;
        case 'gif':
            $im=@imagecreatefromgif($oldImg);
            break;
        default:
            return false;
          break;
        }
        if($im) {
            $w=imagesx($im);
            $h=imagesy($im);
            //计算新宽,高
            if($w>$width || $h>$height) {
                if(!$fixed) {
                    if($w>$width) {
                        $widthRatio=$width/$w;
                    }else{
                        $widthRatio=1;
                    }
                    if($h>$height) {
                        $heightRatio=$height/$h;
                    }else{
                        $heightRatio=1;
                    }
                    $ratio=$widthRatio<$heightRatio ? $widthRatio : $heightRatio;
                    $newWidth=$w*$ratio;
                    $newHeight=$h*$ratio;
                }else{
                    $newWidth=$width;
                    $newHeight=$height;
                }
            }else{
                return false;
            }
            //开始缩略
            if(function_exists('imagecopyresampled')) {
                $newim=imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($newim, $im, 0, 0, 0, 0, $newWidth, $newHeight, $w, $h); 
            }else{
                $newim=imagecreate($newWidth, $newHeight);
                imagecopyresized($newim, $im, 0, 0, 0, 0, $newWidth, $newHeight, $w, $h); 
            }
            if(file_exists($newImg)) { @unlink($newImg);
            }
            switch($imgType){
            case 'jpg':
            case 'jpeg':
                imagejpeg($newim, $newImg);
                break;
            case 'png':
                imagepng($newim, $newImg);
                break;
            case 'gif':
                imagegif($newim, $newImg);
                break;
            default:
                return false;
                    break;
            }
            imagedestroy($newim);
            return true;
        }else{
            return false;
        }
    }
}
?>
