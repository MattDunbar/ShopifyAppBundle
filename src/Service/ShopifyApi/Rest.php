<?php

namespace MattDunbar\ShopifyAppBundle\Service\ShopifyApi;

use MattDunbar\ShopifyAppBundle\Entity\Shop;
use Shopify\Auth\Session;
use Shopify\Rest\Admin2023_10\CustomCollection;
use Shopify\Rest\Admin2023_10\Product;
use Shopify\Rest\Admin2023_10\SmartCollection;

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
     * @return int
     */
    public function getProductCount(Shop $shop): int
    {
        $session = $this->getSession($shop);

        $productCountResponse = Product::count($session);
        if (is_array($productCountResponse) && isset($productCountResponse['count'])) {
            return (int) $productCountResponse['count'];
        }

        return 0;
    }

    /**
     * Get Collection Count
     *
     * @param Shop $shop
     * @return int
     */
    public function getCollectionCount(Shop $shop): int
    {
        $session = $this->getSession($shop);
        $count = 0;
        $collectionCountResponses = [CustomCollection::count($session), SmartCollection::count($session)];
        foreach ($collectionCountResponses as $collectionCountResponse) {
            if (is_array($collectionCountResponse) && isset($collectionCountResponse['count'])) {
                $count = $count + (int) $collectionCountResponse['count'];
            }
        }

        return $count;
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
