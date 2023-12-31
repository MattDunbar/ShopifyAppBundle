<?php

namespace MattDunbar\ShopifyAppBundle\Service\ShopifyApi;

use MattDunbar\ShopifyAppBundle\Entity\Shop;
use Shopify\Clients\Graphql as ShopifyGraphql;
use Shopify\Exception\ShopifyException;
use JsonException;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Graphql
{
    /**
     * @var Config $config
     */
    protected Config $config;
    /**
     * @var HttpClientInterface $httpClient
     */
    protected HttpClientInterface $httpClient;

    public function __construct(
        Config $config,
        HttpClientInterface $httpClient
    ) {
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $query
     * @param Shop $shop
     *
     * @return Response
     */
    public function execute(string $query, Shop $shop): Response
    {
        $this->config->initialize();
        try {
            $client = new ShopifyGraphql((string) $shop->getShopDomain(), $shop->getAccessToken());
            $response = $client->query($query)->getDecodedBody();
            if (is_array($response)) {
                return new Response($response);
            }
            return new Response();
        } catch (ShopifyException | JsonException $e) {
            return new Response();
        }
    }
}
