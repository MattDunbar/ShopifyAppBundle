<?php

namespace MattDunbar\ShopifyAppBundle\Service;

use JsonException;
use MattDunbar\ShopifyAppBundle\Entity\Shop;
use MattDunbar\ShopifyAppBundle\Repository\ShopRepository;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Shopify\Auth\FileSessionStorage;
use Shopify\Auth\OAuth;
use Shopify\Clients\Http;
use Shopify\Context;
use Shopify\Exception\MissingArgumentException;
use Shopify\Exception\ShopifyException;

/**
 * @TODO Refactor to namespace with sub classes, SRP??
 */
class ShopifyApi
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
     * @var LoggerInterface $logger
     */
    protected LoggerInterface $logger;
    /**
     * @var ShopRepository $shopRepository
     */
    protected ShopRepository $shopRepository;


    /**
     * Init Shopify API Service
     *
     * @param string          $appUrlKey
     * @param string          $apiKey
     * @param string          $apiSecret
     * @param string          $scopes
     * @param string          $hostName
     * @param LoggerInterface $logger
     * @param ShopRepository  $shopRepository
     */
    public function __construct(
        string $appUrlKey,
        string $apiKey,
        string $apiSecret,
        string $scopes,
        string $hostName,
        LoggerInterface $logger,
        ShopRepository $shopRepository
    ) {
        $this->appUrlKey = $appUrlKey;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->scopes = $scopes;
        $this->hostName = $hostName;
        $this->logger = $logger;
        $this->shopRepository = $shopRepository;

        $this->contextInitialize();
    }

    /**
     * Initialize Shopify Context
     *
     * @return void
     */
    protected function contextInitialize(): void
    {
        try {
            Context::initialize(
                $this->apiKey,
                $this->apiSecret,
                $this->scopes,
                $this->hostName,
                new FileSessionStorage()
            );
        } catch (MissingArgumentException $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * Get Shopify App Install Details
     *
     * @param  string $shopifyDomain
     * @param  string $redirectUri
     * @return array<string,string> Install URL and State
     */
    public function getInstallDetails(string $shopifyDomain, string $redirectUri): array
    {
        $installSecret = Uuid::uuid4()->toString();
        $query = [
            'client_id' => $this->apiKey,
            'scope' => $this->scopes,
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
     * @param  string $shopDomain
     * @param  string $code
     * @return Shop|null
     */
    public function saveAccessToken(string $shopDomain, string $code): ?Shop
    {
        try {
            $response = OAuth::requestAccessToken(
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
