<?php


namespace Clooder\Profile;


use Clooder\Processor\ImageProcessor;

class Image
{
    private $profiles;

    private $imageProcessor;

    public function __construct(array $profiles, ImageProcessor $imageProcessor)
    {
        $this->profiles = $profiles;
        $this->imageProcessor = $imageProcessor;
    }

    /**
     * @param $profile
     * @return ImageProcessor[]
     */
    public function get($profile)
    {
        if (!array_key_exists($profile, $this->profiles)) {
            throw new \InvalidArgumentException(sprintf('Profile %s not exists', $profile));
        }

        $specs = [];

        foreach ($this->profiles[$profile] as $spec) {

            $specs[] = clone $this->imageProcessor
                ->setResize($spec['width'], $spec['height'])
                ->setQuality($spec['quality']);

        }
        return $specs;
    }
}
