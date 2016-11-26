<?php
namespace AppBundle\Event;

use AppBundle\Entity\Image;
use Symfony\Component\EventDispatcher\Event;

class ImageUploadedEvent extends Event
{
    const IMAGE_UPLOADED = 'imageUploaded';

    protected $image;

    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    /**
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }
}
