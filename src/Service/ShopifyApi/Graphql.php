<?php

namespace MattDunbar\ShopifyAppBundle\Service\ShopifyApi;

use MattDunbar\ShopifyAppBundle\Entity\Shop;
use Shopify\Clients\Graphql as ShopifyGraphql;
use Shopify\Exception\ShopifyException;
use JsonException;

class Graphql
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
     * @param string $query
     * @param Shop $shop
     *
     * @return array<mixed>
     */
    public function execute(string $query, Shop $shop): array
    {
        try {
            $client = new ShopifyGraphql((string) $shop->getShopDomain(), $shop->getAccessToken());
            return (array) $client->query(data: $query)->getDecodedBody();
        } catch (ShopifyException | JsonException $e) {
            return [];
        }
    }
}
