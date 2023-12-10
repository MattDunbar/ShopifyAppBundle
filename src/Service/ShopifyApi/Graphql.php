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
        try {
            $client = new ShopifyGraphql((string) $shop->getShopDomain(), $shop->getAccessToken());
            $response = $client->query(data: $query)->getDecodedBody();
            if (is_array($response)) {
                return new Response($response);
            }
            return new Response();
        } catch (ShopifyException | JsonException $e) {
            return new Response();
        }
    }

    /**
     * @param string $query
     * @param Shop $shop
     *
     * @return Response
     * @TODO Refactor, SRP + Build Graphql Response class
     */
    public function bulkExecuteSync(string $query, Shop $shop): Response
    {
        $response = $this->execute(
            <<<QUERY
                    mutation {
                        bulkOperationRunQuery(
                            query: """
                                $query
                            """
                        ) {
                            bulkOperation {
                                id
                                status
                            }
                            userErrors {
                                field
                                message
                            }
                        }
                    }
                QUERY,
            $shop
        );

        $status = 'STARTING';
        $pollingResponse = null;
        while ($status !== 'FAILED' && $status !== 'COMPLETED') {
            sleep(30);
            $pollingResponse = $this->execute(
                <<<QUERY
                        query {
                            currentBulkOperation {
                                id
                                status
                                url
                            }
                        }
                    QUERY,
                $shop
            );
            $status = $pollingResponse->getStringDataByPath('currentBulkOperation/status');
        }

        if ($status === 'COMPLETED') {
            try {
                $fullResponse = $this->httpClient->request(
                    'GET',
                    (string) $pollingResponse->getStringDataByPath('currentBulkOperation/url')
                );
                $rawResponse = $fullResponse->getContent();
                $responseLines = explode("\n", $rawResponse);
                return new Response([
                    'status' => 'COMPLETED',
                    'data' => array_map(
                        function ($line) {
                            return json_decode($line, true);
                        },
                        $responseLines
                    )
                ]);
            } catch (TransportExceptionInterface | HttpExceptionInterface $e) {
                return new Response(['status' => 'FAILED', 'data' => null]);
            }
        }

        return new Response(['status' => 'FAILED', 'data' => null]);
    }
}
