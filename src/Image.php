<?php

namespace magein;

use magein\trans\Resources;

/**
 * Class Image
 * @package magein
 */
class Image
{
    /**
     * 图片资源
     * @var resource
     */
    protected $resource = null;
	/**
	 * 图片宽度
	 * @var $width
	 */
	protected $width    = 750;
	/**
	 * 图片高度
	 * @var $height
	 */
	protected $height   = 1334;
	/**
	 * 图片颜色 默认黑色
	 * @var $color
	 */
	protected $color    = [0, 0, 0];
	/**
	 * 图片alpha
	 * @var $alpha
	 */
	protected $alpha    = 127;

	/**
	 * Image constructor.
	 * @param null $image
	 * @param null $width
	 * @param null $height
	 * @param null $color
	 * @param null $alpha
	 */
    public function __construct($image = null, $width = null, $height = null, $color = null, $alpha = null)
    {
	    isset($width)  && $this->width  = $width;
	    isset($height) && $this->height = $height;
	    isset($color)  && $this->color  = $color;
	    isset($alpha)  && $this->alpha  = $alpha;

        $this->init($image);
    }

    /**
     * @param $image
     */
    public function init($image)
    {
        if ($image !== null && !is_resource($image)) {
            $image = Resources::instance()->get($image);
        } else {
	        $image = Resources::instance()->create($this->width, $this->height);
        }

        $this->resource = $image;
    }

    /**
     * @return resource
     */
    public function get()
    {
        return $this->resource;
    }

    /**
     * @param bool $download 是否下载 默认否
     * @param null $extend
     */
    public function output($extend = null, $download = false)
    {
	    if ($download) {
		    header('Content-type', 'charset=utf-8;application/x-' . $extend);
		    header('Content-Disposition:attachment');
	    }

        switch ($extend) {
            case 'png':
                header('Content-type:image/png');
                imagepng($this->resource);
                break;
            case 'gif':
                header('Content-type:image/gif');
                imagegif($this->resource);
                break;
            default:
                header('Content-type:image/jpeg');
                imagejpeg($this->resource);
                break;
        }

        imagedestroy($this->resource);

        exit();
    }

    /**
     * @param $filename
     * @param null $extend
     * @param int $quality
     * @return bool
     */
    public function save($filename, $extend = null, $quality = 100)
    {
        switch ($extend) {
            case 'png':
                imagepng($this->resource, $filename, $quality);
                break;
            case 'gif':
                imagegif($this->resource, $filename);
                break;
            default:
                imagejpeg($this->resource, $filename, $quality);
                break;
        }

        imagedestroy($this->resource);

        if (is_file($filename)) {
            return true;
        }

        return false;
    }

    /**
     * 略缩图
     * @param int $width
     * @param null $height
     * @return resource
     */
    public function thumb($width = 100, $height = null)
    {
        $resource_width = imagesx($this->resource);
        $resource_height = imagesy($this->resource);

        //算出另一个边的长度，得到缩放后的图片宽度和高度
        if ($resource_width > $resource_height) {
            $image_width = $width;
            $image_height = $height ? $height : $resource_height * ($width / $resource_width);
        } else {
            $image_height = $width;
            $image_width = $height ? $height : $resource_width * ($width / $resource_height);
        }

        // 缩放后的大小
        $resource = imagecreatetruecolor($image_width, $image_height);

        //目标资源，源，目标资源的开始坐标x,y, 源资源的开始坐标x,y,目标资源的宽高w,h,源资源的宽高w,h
        imagecopyresampled($resource, $this->resource, 0, 0, 0, 0, $image_width, $image_height, $resource_width, $resource_height);

        return $this->resource = $resource;
    }

    /**
     * 裁剪
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @return resource
     */
    public function cut($x = 0, $y = 0, $width = 100, $height = 100)
    {
        $canvas = imagecrop($this->resource, ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height]);

        return $this->resource = $canvas;
    }

    /**
     * 圆形处理
     * @return resource
     */
    public function circle()
    {
        // 获取图片的大小
        $width = imagesx($this->resource);
        $height = imagesy($this->resource);
        $width = min($width, $height);
        $height = $width;

        // 创建一个全透明的背景图
        $resource = Resources::instance()->create($width, $height);

        // 将源图片的中的每一个像素取出来填充到创建的图片中（在圆半径内）
        $r = $width / 2; //圆半径
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgbColor = imagecolorat($this->resource, $x, $y);
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    imagesetpixel($resource, $x, $y, $rgbColor);
                }
            }
        }

        $this->resource = $resource;

        return $this->resource;
    }

    /**
     * 添加水印
     * @param $image
     * @param string $x
     * @param string $y
     * @return resource
     */
    public function water($image, $x = '100%', $y = '100%')
    {
        if (!is_resource($image)) {
            $image = Resources::instance()->get($image);
        }

        $resourceWidth = imagesx($this->resource);
        $resourceHeight = imagesy($this->resource);

        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        if (!is_int($x) && !is_int($y)) {

            $x = intval($x);
            $y = intval($y);

            $x = $x > 100 ? 100 : $x;
            $y = $y > 100 ? 100 : $x;

            $x = $resourceWidth * ($x / 100) - $imageWidth;
            $y = $resourceHeight * ($y / 100) - $imageHeight;
        }

        imagecopy($this->resource, $image, $x, $y, 0, 0, $imageWidth, $imageHeight);

        return $this->resource;
    }

	/**
	 * 合成图片
	 * @param null $image 需要合成到底图的图片
	 * @param null $extend 后缀名或网络类型标识
	 * @param int $width 目标图片宽度 (合成到底图上时, 根据目标宽高, 可能会对原图拉伸或压缩)
	 * @param int $height 目标图片高度
	 * @param int $top 坐标y
	 * @param int $left 坐标x
	 * @param null $originWidth 原始图片宽度 (指需要合成到底图的图片) 默认跟随目标宽高
	 * @param null $originHeight 原始图片高度
	 * @return resource 底图
	 * @author fisher
	 * @date 2018/10/26 上午10:38
	 */
	public function mergeImage($image = null, $extend = null, $width = 0, $height = 0, $top = 0, $left = 0, $originWidth = null, $originHeight = null)
	{
		if ( ! is_resource($image)) {
			$image = Resources::instance()->get($image);
		}

		$originHeight = isset($originHeight) ? $originHeight : $height;
		$originWidth  = isset($originWidth) ? $originWidth : $width;

		imagecopyresized($this->resource, $image, $left, $top, 0, 0, $width, $height, $originWidth, $originHeight);

		return $this->resource;
	}

	/**
	 * 合成文字 - 在底图基础上添加文字
	 * @param $text //文字内容
	 * @param null $font //字体文件地址 绝对路径
	 * @param int $size 文字大小
	 * @param int $top 坐标y
	 * @param int $left 坐标x
	 * @param array $color 颜色值 rgb数组
	 * @param int $rotation 旋转角度 默认0不旋转
	 * @return resource 底图
	 * @author fisher
	 * @date 2018/10/26 上午10:38
	 */
	public function mergeText($text, $font = null, $size = 16, $top = 0, $left = 0, $color = [], $rotation = 0)
	{
		$color = empty($color) ? $this->color : $color;
		$color = imagecolorallocate($this->resource, $color['0'], $color['1'], $color['2']);
		imagefttext($this->resource, $size, $rotation, $left, $top, $color, $font, $text);

		return $this->resource;
	}

	/**
	 * 输出字符串 默认base64
	 * @param string $extend
	 * @param string $encode
	 * @return string
	 * @author fisher
	 * @date 2018/10/26 上午11:43
	 */
	public function toString($extend = 'png', $encode = 'base64')
	{
		ob_start();

		switch ($extend) {
			case 'jpg':
			case 'jpeg':
				imagejpeg($this->resource);

				break;
			case 'gif':
				imagegif($this->resource);

				break;
			default:
				imagepng($this->resource);

				break;
		}

		$resource = ob_get_clean();

		if ($encode === 'base64') {
			$prefix   = 'data:image/' . $extend . ';base64,';
			$resource = $prefix . base64_encode($resource);
		}

		imagedestroy($this->resource);

		return $resource;
	}
}
