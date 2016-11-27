<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\Image;
use AppBundle\Service\FilesystemManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use League\Flysystem\Filesystem;
use Symfony\Bridge\Twig\Extension\HttpFoundationExtension;

class ImageSubscriber implements EventSubscriber
{
    protected $httpFoundationExtension;
    protected $filesystemManager;

    public function __construct(HttpFoundationExtension $httpFoundationExtension, FilesystemManager $filesystemManager)
    {
        $this->httpFoundationExtension = $httpFoundationExtension;
        $this->filesystemManager = $filesystemManager;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postRemove,
            Events::postLoad
        ];
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Image && $entity->getImagePath() !== null) {
            $this->setAbsoluteUrl($entity);
        }
    }

    protected function setAbsoluteUrl(Image $image)
    {
        $path = '/uploads/' . $image->getFilesystem() . '/' . $image->getImagePath();
        $url = $this->httpFoundationExtension->generateAbsoluteUrl($path);
        $image->setUrl($url);
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Image && $entity->getImageFile() !== null) {
            $this->upload($entity);
            $this->setAbsoluteUrl($entity);
        }
    }

    protected function upload(Image $entity)
    {
        /** @var Filesystem $filesystem */
        list($filesystem, $adapterName) = $this->filesystemManager->getFilesystem();
        $filename = hash('sha512', uniqid(mt_rand(), true));
        while ($filesystem->has($entity->getUploadDir() . '/' . $filename . '.' . $entity->getImageFile()->guessExtension())) {
            $filename = hash('sha512', uniqid(mt_rand(), true));
        }

        $entity->setFilesystem($adapterName);
        $entity->setOriginalName($entity->getImageFile()->getClientOriginalName());
        $entity->setImageName($filename . '.' . $entity->getImageFile()->guessExtension());
        $entity->setSize($entity->getImageFile()->getSize());
        $entity->setMimeType($entity->getImageFile()->getMimeType());

        $stream = fopen($entity->getImageFile()->getRealPath(), 'rb+');
        $filesystem->writeStream($entity->getAbsoluteImagePath(), $stream);
        fclose($stream);

        $entity->setImageFile(null);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Image && $entity->getImageFile() !== null) {
            $this->upload($entity);
            $this->setAbsoluteUrl($entity);
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Image) {
            $path = $entity->getAbsoluteImagePath();
            $filesystem = $this->filesystemManager->getFilesystemByName($entity->getFilesystem());
            if ($filesystem->has($path)) {
                /** @var Filesystem $filesystem */
                $filesystem->delete($entity->getAbsoluteImagePath());
            }
        }
    }
}
