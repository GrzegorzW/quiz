<?php

namespace AppBundle\Service;

use League\Flysystem\MountManager;

class FilesystemManager
{
    public function __construct(MountManager $mountManager)
    {
        $this->mountManager = $mountManager;
        $this->adapters = [
            'drive0'
        ];
    }
    
    public function getFilesystem()
    {
        $adapterName = $this->adapters[0];
        
        return [$this->mountManager->getFilesystem($adapterName), $adapterName];
    }
    
    public function getFilesystemByName($name)
    {
        return $this->mountManager->getFilesystem($name);
    }
}
