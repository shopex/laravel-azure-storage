<?php

namespace Shopex\LaravelAzureStorage\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

class PrivateDownloadUrl extends AbstractPlugin
{

    public function getMethod()
    {
        return 'privateDownloadUrl';
    }

    public function handle($path)
    {
        return $this->filesystem->getAdapter()->getUrl($path);
    }
}
