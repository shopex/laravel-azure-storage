<?php

namespace Shopex\LaravelAzureStorage;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use Shopex\LaravelAzureStorage\Plugins\PrivateDownloadUrl;

/**
 * Service provider for Azure Blob Storage
 */
class AzureStorageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        app('filesystem')->extend('azure', function ($app, $config) {
            $endpoint = sprintf(
                'DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s',
                $config['name'],
                $config['key']
            );
            $endpoint .= isset($config['suffix']) ? sprintf(';EndpointSuffix=%s', $config['suffix']) : '';
            $client = BlobRestProxy::createBlobService($endpoint);
            $adapter = new AzureBlobStorageAdapter($client, $config['container'], $config['prefix'], $config['key'], $config['url']);
            $flysystem = new Filesystem($adapter);
            $flysystem->addPlugin(new PrivateDownloadUrl());
            return $flysystem;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
