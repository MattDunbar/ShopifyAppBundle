<?php

namespace MattDunbar\ShopifyAppBundle\Service\ShopifyApi;

use JsonException;
use Shopify\Auth\OAuth as ShopifyOAuth;
use MattDunbar\ShopifyAppBundle\Entity\Shop;
use MattDunbar\ShopifyAppBundle\Repository\ShopRepository;
use Psr\Http\Client\ClientExceptionInterface;
use Ramsey\Uuid\Uuid;
use Shopify\Clients\Http;
use Shopify\Context;
use Shopify\Exception\ShopifyException;

class OAuth
{
    /**
     * @var ShopRepository $shopRepository
     */
    protected ShopRepository $shopRepository;
    /**
     * @var Config $config
     */
    protected Config $config;

    public function __construct(
        ShopRepository $shopRepository,
        Config $config
    ) {
        $this->shopRepository = $shopRepository;
        $this->config = $config;
    }

    /**
     * Get Shopify App Install Details
     *
     * @param  string $shopifyDomain
     * @param  string $redirectUri
     * @return array<string,string> Install URL and State
     */
    public function startInstall(string $shopifyDomain, string $redirectUri): array
    {
        $this->config->initialize();

        $installSecret = Uuid::uuid4()->toString();
        $query = [
            'client_id' => Context::$API_KEY,
            'scope' => Context::$SCOPES,
            'redirect_uri' => $redirectUri,
            'state' => $installSecret,
            'grant_options[]' => '',
        ];

        return [
            'url' => "https://{$shopifyDomain}/admin/oauth/authorize?" . http_build_query($query),
            'state' => $installSecret
        ];
    }

    /**
     * Get access token.
     *
     * @param string $shopDomain
     * @param string $code
     * @return Shop|null
     */
    public function finishInstall(string $shopDomain, string $code): ?Shop
    {
        $this->config->initialize();

        try {
            $response = ShopifyOAuth::requestAccessToken(
                new Http($shopDomain),
                [
                    'client_id' => Context::$API_KEY,
                    'client_secret' => Context::$API_SECRET_KEY,
                    'code' => $code,
                ]
            );
            $responseBody = $response->getDecodedBody();

            if (!isset($responseBody['access_token']) || !isset($responseBody['scope'])) {
                return null;
            }

            return $this->shopRepository->createOrUpdateByShopDomain(
                $shopDomain,
                (string)$responseBody['access_token'],
                (string)$responseBody['scope']
            );
        } catch (ShopifyException | ClientExceptionInterface | JsonException $exception) {
            return null;
        }
    }
}
