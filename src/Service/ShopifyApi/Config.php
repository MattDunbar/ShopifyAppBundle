<?php

namespace MattDunbar\ShopifyAppBundle\Service\ShopifyApi;

use Shopify\Auth\FileSessionStorage;
use Shopify\Context;
use Shopify\Exception\MissingArgumentException;

class Config
{
    /**
     * @var string $appUrlKey
     */
    protected string $appUrlKey;
    /**
     * @var string $apiKey
     */
    protected string $apiKey;
    /**
     * @var string $apiSecret
     */
    protected string $apiSecret;
    /**
     * @var string $scopes
     */
    protected string $scopes;
    /**
     * @var string $hostName
     */
    protected string $hostName;


    /**
     * Init Shopify API Service
     *
     * @param string          $appUrlKey
     * @param string          $apiKey
     * @param string          $apiSecret
     * @param string          $scopes
     * @param string          $hostName
     */

    public function __construct(
        string $appUrlKey,
        string $apiKey,
        string $apiSecret,
        string $scopes,
        string $hostName,
    ) {
        $this->appUrlKey = $appUrlKey;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->scopes = $scopes;
        $this->hostName = $hostName;
    }

    /**
     * Initialize Shopify Context
     *
     * @return bool
     */
    public function initialize(): bool
    {
        try {
            Context::initialize(
                $this->apiKey,
                $this->apiSecret,
                $this->scopes,
                $this->hostName,
                new FileSessionStorage()
            );
            return true;
        } catch (MissingArgumentException $exception) {
            return false;
        }
    }

    /**
     * Get App URL Key
     *
     * @return string
     */
    public function getAppUrlKey(): string
    {
        return $this->appUrlKey;
    }
}
