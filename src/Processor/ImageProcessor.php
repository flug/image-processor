<?php


namespace Clooder\Processor;


use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;
use Symfony\Component\Filesystem\Filesystem;

class ImageProcessor
{
    private $imageManager;
    private $width;
    private $height;
    private $quality;
    private $pathToImage;
    private $directory;
    private $fs;

    private $filename;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
        $this->fs = new Filesystem();
    }

    public function process()
    {
        $this->moveOriginal();
        if (!$this->fs->exists($this->directory . '/' . $this->width)) {
            $this->fs->mkdir($this->directory . '/' . $this->width);
        }
        $this->imageManager->make($this->pathToImage)->resize($this->width, $this->height, function (Constraint $constraint) {
            $constraint->aspectRatio();
        })
            ->save($this->directory . '/' . $this->width . "/" . $this->filename, $this->quality);
    }

    private function moveOriginal()
    {
        if (!$this->fs->exists($this->pathToImage)) {
            throw new \RuntimeException(sprintf('this %s not exists', $this->pathToImage));
        }
        $originalsDirectory = $this->directory . '/originals';
        if ($this->fs->exists($originalsDirectory . "/" . $this->filename)) {
            return;
        }
        $this->fs->mkdir($originalsDirectory);
        $this->fs->copy($this->pathToImage, $originalsDirectory . "/" . $this->filename);
    }

    public function setResize($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    public function setQuality($quality)
    {
        $this->quality = $quality;
        return $this;
    }

    public function setImage($pathToImage)
    {
        $this->pathToImage = $pathToImage;
        $this->filename = current(array_reverse(explode('/', $this->pathToImage)));
        return $this;
    }

    public function setOutputDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

}
