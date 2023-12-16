<?php

namespace MattDunbar\ShopifyAppBundle\Service\ShopifyApi;

use MattDunbar\ShopifyAppBundle\Entity\Shop;
use Psr\Http\Client\ClientExceptionInterface;
use Shopify\Context;
use Shopify\Exception\ShopifyException;
use Shopify\Webhooks\Registry;

class Webhook
{
    /**
     * @var Config $config
     */
    protected Config $config;

    /**
     * Constructor
     *
     * @param Config $config
     */
    public function __construct(
        Config $config,
    ) {
        $this->config = $config;
    }

    /**
     * Register a webhook
     *
     * @param string $topic
     * @param string $callbackUrl
     * @param Shop $shop
     * @return bool Success
     */
    public function registerWebhook(string $topic, string $callbackUrl, Shop $shop): bool
    {
        $this->config->initialize();
        try {
            Registry::register(
                $callbackUrl,
                $topic,
                (string) $shop->getShopDomain(),
                (string) $shop->getAccessToken(),
            );
            return true;
        } catch (ShopifyException | ClientExceptionInterface $e) {
            return false;
        }
    }

    public function verify(): bool
    {
        $this->config->initialize();
        $calculatedHmac = base64_encode(
            hash_hmac(
                'sha256',
                (string) file_get_contents('php://input'),
                Context::$API_SECRET_KEY,
                true
            )
        );
        return hash_equals($calculatedHmac, $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256']);
    }
}
