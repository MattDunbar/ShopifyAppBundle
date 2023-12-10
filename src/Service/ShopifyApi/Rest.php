<?php

namespace MattDunbar\ShopifyAppBundle\Service\ShopifyApi;

use MattDunbar\ShopifyAppBundle\Entity\Shop;
use Shopify\Auth\Session;
use Shopify\Exception\UninitializedContextException;
use Shopify\Rest\Admin2023_10\Product;
use Shopify\Utils;

class Rest
{
    /**
     * @var Config $config
     */
    protected Config $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Get Product Count
     *
     * @param Shop $shop
     * @return ?int
     */
    public function getProductCount(Shop $shop): ?int
    {
        /** @var string $shopDomain */
        $shopDomain = $shop->getShopDomain();
        if ($shopDomain == null) {
            return null;
        }
        /** @var Session $session */
        $session = $this->getSession($shopDomain);
        if ($session == null) {
            return null;
        }

        $productCountResponse = Product::count($session);
        if (is_array($productCountResponse) && isset($productCountResponse['count'])) {
            return $productCountResponse['count'];
        }

        return null;
    }

    /**
     * Prepare session for API call
     *
     * @param string $shop
     * @return Session|null
     */
    private function getSession(string $shop): ?Session
    {
        $this->config->initialize();
        try {
            return Utils::loadOfflineSession($shop);
        } catch (UninitializedContextException $e) {
            return null;
        }
    }
}
