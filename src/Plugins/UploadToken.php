<?php

/*
 * This file is part of the overtrue/flysystem-qiniu.
 * (c) overtrue <i@overtrue.me>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Onex\LaravelAzureStorage\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

class UploadToken extends AbstractPlugin
{
    public function getMethod()
    {
        return 'getUploadToken';
    }

    public function handle($key = null, $expires = 3600)
    {
        return $this->filesystem->getAdapter()->getUploadToken($key, $expires);
    }
}
