<?php
namespace app\core;

use DateTime;

class DynamicImgThumbMaker
{
    public const FULL_IMG_UPSIZE_VALUE = 1.02;
    public const FULL_IMG_FONT_FILE = './ArialBold.ttf';
    public const FULL_IMG_TEXT_EXTRA_WIDTH = 12;
    public const FULL_IMG_TEXT_EXTRA_HEIGHT = 8;

    public const JPG_QUALITY = 98;
    public const WEBP_QUALITY = 98;

    private $marketUri;
    private $localPath;
    private $imgStr;
    private $imgResource;
    private $needJpg = false;
    private $needFinalResize = false;

    private $currentSizeRule = null;

    function __construct()
    {
        if (!isset($_SERVER['HTTP_ACCEPT']) || stripos($_SERVER['HTTP_ACCEPT'], 'webp') === false) {
            $this->needJpg = true;
        }

        $this->marketUri = preg_replace('#(^/+|\?.*$)#', '', $_SERVER['REQUEST_URI']); //|\.\./|\.(php|yml)

        $date = new DateTime();
        $date->modify('- 2 month');
        header('Last-Modified: ' . $date->format('D, d M Y H:i:s T'));
        unset($date);
        $this->marketUri = str_replace('thumbs/', '', $this->marketUri);

        $sizes = $_GET['s'] ?? null;

        if ($sizes) {
            if (strpos($sizes, 'x') !== false) {
                $sizes = explode('x', $sizes);
                $width = intval($sizes[0] ?? null);
                $height = intval($sizes[1] ?? null);
                if ($width > 0 && $height > 0) {
                    $this->currentSizeRule['finalWidth'] = $width;
                    $this->currentSizeRule['finalHeight'] = $height;
                    $this->needFinalResize = true;
                }
            }
        }

        $this->localPath = $this->marketUri;

        if (!is_file($this->localPath)) {
            $this->sendAnswerCode(404);
        }
        $this->imgStr = file_get_contents($this->localPath);

        if ($this->needFinalResize === true) {
            $this->resizeImage($this->currentSizeRule['finalWidth'], $this->currentSizeRule['finalHeight']);
        }

        if ($this->needJpg) {
            if (($imgInfo = getimagesizefromstring($this->imgStr)) === false) {
                $this->sendAnswerCode(503, "Not an image");
            }

            if (!$this->needFinalResize) {
                header("Content-Type: {$imgInfo['mime']}");
                header('Content-Length: ' . strlen($this->imgStr));
                echo $this->imgStr;
                die;
            }
            if (!$this->needFinalResize
                || $this->resizeImage(
                    $this->currentSizeRule['finalWidth'],
                    $this->currentSizeRule['finalHeight']
                ) !== true) {
                $this->createImageFromCurrentString($imgInfo[2]);
            }
            header("Content-Type: image/jpeg");

            if (imagejpeg($this->imgResource, null, self::JPG_QUALITY) === false) {
                $this->sendAnswerCode(503, "Img processing error 2");
            }
        } else {
            header("Content-Type: image/webp");
            if ($this->needFinalResize === true) {
                if ($this->resizeImage(
                        $this->currentSizeRule['finalWidth'],
                        $this->currentSizeRule['finalHeight']
                    ) !== true) {
                    $this->createImageFromCurrentString();
                }

                imagewebp($this->imgResource, null, self::WEBP_QUALITY);
                die;
            }
            header('Content-Length: ' . strlen($this->imgStr));
        }
        echo $this->imgStr;
    }

    function sendAnswerCode($code, $message = null)
    {
        switch ($code) {
            case 404:
                header("HTTP/1.0 404 Not Found");
                break;
            case 403:
                header("HTTP/1.0 403 Forbidden");
                break;
            case 503:
                header('HTTP/1.1 503 Service Temporarily Unavailable');
                break;
            default:
                header("HTTP/1.1 $code");
        }
        if ($message !== null) {
            header("X-dbg: $message");
        }
        $img = @file_get_contents("404.jpg");
        if ($img) {
            header("Content-Type: image/jpeg");
            echo $img;
        }
        die();
    }

    function resizeImage($targetWidth, $targetHeight)
    {
        $requestedWidth = $targetWidth;
        $requestedHeight = $targetHeight;

        $imgInfo = getimagesizefromstring($this->imgStr);

        $scale = min($targetWidth / $imgInfo[0], $targetHeight / $imgInfo[1]);
        if ($scale >= 1) {
            if ($imgInfo[2] === IMAGETYPE_WEBP) {
                return false;
            }
            $targetWidth = $imgInfo[0];
            $targetHeight = $imgInfo[1];
        } else {
            $targetWidth = ceil($scale * $imgInfo[0]);
            $targetHeight = ceil($scale * $imgInfo[1]);
        }

        $dstX = 0;
        $dstY = 0;
        if ($requestedWidth > $targetWidth) {
            $dstX = ($requestedWidth - $targetWidth) / 2;
        }
        if ($requestedHeight > $targetHeight) {
            $dstY = ($requestedHeight - $targetHeight) / 2;
        }

        $this->createImageFromCurrentString($imgInfo[2]);
        $dst = imagecreatetruecolor($requestedWidth, $requestedHeight);
        $bg = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $bg);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled(
            $dst,
            $this->imgResource,
            $dstX,
            $dstY,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $imgInfo[0],
            $imgInfo[1]
        );
        $this->imgResource = $dst;
        return true;
    }

    function createImageFromCurrentString($imageType = null)
    {
        if ($imageType === null) {
            $imgInfo = getimagesizefromstring($this->imgStr);
            if (!$imgInfo) {
                $this->sendAnswerCode(503, "Img processing error - 0");
            }
            $imageType = $imgInfo[2];
        }
        if ($imageType === IMAGETYPE_WEBP) {
            //$currentImg = imagecreatefromstring($this->imgStr); // метод пока не работает с webp, поэтому через временный файл
            $tmpMemPath = '/tmp/i' . getmypid();
            file_put_contents($tmpMemPath, $this->imgStr);
            $this->imgResource = imagecreatefromwebp($tmpMemPath);
            @unlink($tmpMemPath);
        } else {
            $this->imgResource = imagecreatefromstring($this->imgStr);
        }

        if ($this->imgResource === false) {
            error_log("Create image from string error - {$this->marketUri}");
            $this->sendAnswerCode(503, "Img processing error");
        }
    }

    function upsizeAndSignImage($signText)
    {
        $imgInfo = getimagesizefromstring($this->imgStr);
        $imgWidth = $imgInfo[0];
        $imgHeight = $imgInfo[1];

        $fontSize = (int)($imgWidth * 0.016);
        if ($fontSize > 30) {
            $fontSize = 30;
        } elseif ($fontSize < 12) {
            $fontSize = 12;
        }

        $textBox = imagettfbbox($fontSize, 0, self::FULL_IMG_FONT_FILE, $signText);
        $textWidth = max($textBox[2] - $textBox[0], $textBox[4] - $textBox[6]);
        if ($textWidth > $imgWidth) {
            if ($textWidth > ($imgWidth * 1.5)) {
                $signText = wordwrap($signText, strlen($signText) / 3, "\n");
            } else {
                $signText = wordwrap($signText, strlen($signText) / 2, "\n");
            }
            $textBox = imagettfbbox($fontSize, 0, self::FULL_IMG_FONT_FILE, $signText);
            $textWidth = max($textBox[2] - $textBox[0], $textBox[4] - $textBox[6]);
        }
        $textHeight = $textBox[1] - $textBox[7];

        $targetWidth = $imgWidth * self::FULL_IMG_UPSIZE_VALUE;
        if ($targetWidth < ($textWidth + self::FULL_IMG_TEXT_EXTRA_WIDTH)) {
            $targetHeight = $imgHeight * (($textWidth + self::FULL_IMG_TEXT_EXTRA_WIDTH) / ($targetWidth / self::FULL_IMG_UPSIZE_VALUE));
            $targetWidth = ($textWidth + self::FULL_IMG_TEXT_EXTRA_WIDTH);
        } else {
            $targetHeight = $imgHeight * self::FULL_IMG_UPSIZE_VALUE;
        }

        $this->createImageFromCurrentString($imgInfo[2]);
        $dst = imagecreatetruecolor($targetWidth, $targetHeight + $textHeight + self::FULL_IMG_TEXT_EXTRA_HEIGHT);

        imagefill($dst, 0, $targetHeight, imagecolorallocate($dst, 255, 255, 255));
        imagecopyresampled($dst, $this->imgResource, 0, 0, 0, 0, $targetWidth, $targetHeight, $imgWidth, $imgHeight);

        imagettftext(
            $dst,
            $fontSize,
            0,
            ($targetWidth - $textWidth) / 2,
            $targetHeight + $textHeight / 2 + self::FULL_IMG_TEXT_EXTRA_HEIGHT,
            imagecolorallocate($dst, 0, 0, 0),
            self::FULL_IMG_FONT_FILE,
            $signText
        );

        $this->imgResource = $dst;
    }
}


