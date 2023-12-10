<?php

namespace MattDunbar\ShopifyAppBundle\Service\ShopifyApi\Graphql;

use MattDunbar\ShopifyAppBundle\Entity\Shop;
use MattDunbar\ShopifyAppBundle\Service\ShopifyApi\Graphql;
use MattDunbar\ShopifyAppBundle\Service\ShopifyApi\Response;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BulkOperation
{
    /**
     * @var Graphql $graphql
     */
    protected Graphql $graphql;
    /**
     * @var HttpClientInterface $httpClient
     */
    protected HttpClientInterface $httpClient;

    /**
     * Constructor
     *
     * @param Graphql $graphql
     * @param HttpClientInterface $httpClient
     */
    public function __construct(
        Graphql $graphql,
        HttpClientInterface $httpClient
    ) {
        $this->graphql = $graphql;
        $this->httpClient = $httpClient;
    }

    /**
     * Call bulkOperationRunQuery
     *
     * @param string $query
     * @param Shop $shop
     * @return Response
     */
    public function runQuery(string $query, Shop $shop): Response
    {
        return $this->graphql->execute(
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
    }

    /**
     * Call currentBulkOperation
     *
     * @param Shop $shop
     * @return Response
     */
    public function currentOperation(Shop $shop): Response
    {
        return $this->graphql->execute(
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
    }

    /**
     * Process bulk operation result
     *
     * @param Response $currentResponse
     * @return BulkOperationResult
     */
    public function processResult(Response $currentResponse): BulkOperationResult
    {
        try {
            $fullResponse = $this->httpClient->request(
                'GET',
                (string) $currentResponse->getStringDataByPath('currentBulkOperation/url')
            );
            $rawResponse = $fullResponse->getContent();
            $responseLines = explode("\n", $rawResponse);
            if ($responseLines[count($responseLines) - 1] === '') {
                array_pop($responseLines);
            }
            return new BulkOperationResult(
                array_map(
                    function ($line) {
                        return json_decode($line, true);
                    },
                    $responseLines
                ),
                'COMPLETED'
            );
        } catch (TransportExceptionInterface | HttpExceptionInterface $e) {
            return new BulkOperationResult([], 'FAILED');
        }
    }

    /**
     * @param string $query
     * @param Shop $shop
     * @param int $wait
     * @return BulkOperationResult
     */
    public function runQuerySync(string $query, Shop $shop, int $wait = 30): BulkOperationResult
    {
        $this->runQuery($query, $shop);

        $status = 'STARTING';
        $pollingResponse = null;
        while ($status !== 'FAILED' && $status !== 'COMPLETED') {
            sleep(30);
            $pollingResponse = $this->currentOperation($shop);
            $status = $pollingResponse->getStringDataByPath('currentBulkOperation/status');
        }

        if ($status === 'COMPLETED') {
            return $this->processResult($pollingResponse);
        }

        return new BulkOperationResult([], 'FAILED');
    }
}
