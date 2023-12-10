<?php

namespace MattDunbar\ShopifyAppBundle\Service\ShopifyApi;

use MattDunbar\ShopifyAppBundle\Entity\Shop;
use Shopify\Auth\Session;
use Shopify\Context;
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
        $session = $this->getSession($shop);

        $productCountResponse = Product::count($session);
        if (is_array($productCountResponse) && isset($productCountResponse['count'])) {
            return $productCountResponse['count'];
        }

        return null;
    }

    /**
     * Prepare session for API call
     *
     * @param Shop $shop
     * @return Session
     */
    private function getSession(Shop $shop): Session
    {
        $this->config->initialize();
        $session = new Session('session', (string) $shop->getShopDomain(), false, 'state');
        $session->setAccessToken((string) $shop->getAccessToken());
        $session->setScope((string) $shop->getScope());

        return $session;
    }
}
