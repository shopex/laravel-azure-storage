<?php

namespace Onex\LaravelAzureStorage;

use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter as BaseAzureBlobStorageAdapter;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use Onex\LaravelAzureStorage\Plugins\BlobSharedAccessSignatureHelper;
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
        $path = preg_replace_callback('#(?:(?![，。？])[\xC0-\xFF][\x80-\xBF]+)+#',function($str) {
            if (is_array($str)) $str = implode(',', $str);
            return urlencode($str);
        },$path);//$content是需要处理的字符串

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

    /**
     * Get private file download url.
     *
     * @param string $path
     * @param int    $expires
     *
     * @return string
     */
    public function privateDownloadUrl($path, $expires = 3600)
    {
        $sas = new BlobSharedAccessSignatureHelper($this->client->getAccountName(), $this->accountKey);

        $sasString = $sas->generateBlobServiceSharedAccessSignatureToken(
            Resources::RESOURCE_TYPE_BLOB
            , $this->container . '/' . $path
            , 'r'
            , $this->getTZDate($expires)
            , ""
            , ""
            , 'https'
        );
        return $this->getUrl($path, sprintf('?%s', $sasString));
    }

    /**
     * Get private file download url.
     *
     * @param string $path
     * @param int    $expires
     *
     * @return string
     */
    public function getUploadToken($path = '', $expires = 3600)
    {
        $sas = new BlobSharedAccessSignatureHelper($this->client->getAccountName(), $this->accountKey);

        $sasString = $sas->generateBlobServiceSharedAccessSignatureToken(
            Resources::RESOURCE_TYPE_CONTAINER
            , $this->container
            , 'rw'
            , $this->getTZDate($expires)
            , ""
            , ""
            , 'https'
        );
        return $sasString;
    }

    public function getClient()
    {
        return $this->client;
    }

    private function getTZDate($expires) {
        ini_set("date.timezone", "Etc/GMT");
        return str_replace('+00:00', 'Z', gmdate('c', time() + $expires));
    }
}
