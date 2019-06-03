<?php

namespace Shopex\LaravelAzureStorage;

use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter as BaseAzureBlobStorageAdapter;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\BlobSharedAccessSignatureHelper;
use MicrosoftAzure\Storage\Common\Internal\Resources;

class AzureBlobStorageAdapter extends BaseAzureBlobStorageAdapter
{
    /**
     * @var BlobRestProxy $client
     */
    private $client;
    /**
     * @var string $container
     */
    private $container;
    /**
     * @var string $baseUrl
     */
    private $baseUrl;
    /**
     * @var string $accountKey
     */
    private $accountKey;
    /**
     * AzureBlobStorageExtendedAdapter constructor.
     *
     * @param BlobRestProxy $client
     * @param $container
     * @param null $prefix
     * @param null $accountKey
     * @param null $baseUrl
     */
    public function __construct(BlobRestProxy $client, $container, $prefix = null, $accountKey = null, $baseUrl = null)
    {
        $this->client = $client;
        $this->container = $container;
        $this->baseUrl = $baseUrl;
        $this->accountKey = $accountKey;
        $this->setPathPrefix($prefix);
        parent::__construct($client, $container, $prefix);
    }

    /**
     * Generate public url
     *
     * @param $path
     * @param string $sasKey
     * @return string
     */
    public function getUrl($path, $sasKey = '')
    {
        if ( ! empty($this->baseUrl)) {
            return sprintf('%s/%s/%s%s'
                , $this->baseUrl
                , $this->container
                , $path
                , $sasKey);
        }
        return sprintf('https://%s.blob.core.windows.net/%s/%s%s'
            , $this->client->getAccountName()
            , $this->container
            , $path
            , $sasKey);
    }

    public function getClient()
    {
        return $this->client;
    }
}
