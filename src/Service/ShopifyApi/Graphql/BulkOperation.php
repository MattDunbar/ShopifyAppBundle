<?php

namespace MattDunbar\ShopifyAppBundle\Service\ShopifyApi\Graphql;

use DateTimeImmutable;
use MattDunbar\ShopifyAppBundle\Entity\ManagedBulkOperation;
use MattDunbar\ShopifyAppBundle\Entity\ManagedBulkOperation\Status;
use MattDunbar\ShopifyAppBundle\Entity\Shop;
use MattDunbar\ShopifyAppBundle\Repository\ManagedBulkOperationRepository;
use MattDunbar\ShopifyAppBundle\Service\ShopifyApi\Graphql;
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
     * @var ManagedBulkOperationRepository $managedBulkOperationRepository
     */
    protected ManagedBulkOperationRepository $managedBulkOperationRepository;

    /**
     * Constructor
     *
     * @param Graphql $graphql
     * @param HttpClientInterface $httpClient
     * @param ManagedBulkOperationRepository $managedBulkOperationRepository
     */
    public function __construct(
        Graphql $graphql,
        HttpClientInterface $httpClient,
        ManagedBulkOperationRepository $managedBulkOperationRepository
    ) {
        $this->graphql = $graphql;
        $this->httpClient = $httpClient;
        $this->managedBulkOperationRepository = $managedBulkOperationRepository;
    }

    /**
     * Call bulkOperationRunQuery
     *
     * @param string $query
     * @param Shop $shop
     * @return ManagedBulkOperation
     */
    public function runQuery(string $query, Shop $shop): ManagedBulkOperation
    {
        $newOperation = $this->graphql->execute(
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


        $managedBulkOperation = $this->managedBulkOperationRepository->create();
        $managedBulkOperation->setShop($shop);
        $managedBulkOperation->setOperationId(
            (string) $newOperation->getStringDataByPath('bulkOperationRunQuery/bulkOperation/id')
        );
        $managedBulkOperation->setStatus(Status::CREATED);
        $this->managedBulkOperationRepository->save($managedBulkOperation);

        return $managedBulkOperation;
    }

    /**
     * Call currentBulkOperation
     *
     * @param Shop $shop
     * @return ManagedBulkOperation
     */
    public function currentOperation(Shop $shop): ManagedBulkOperation
    {
        $currentOperation = $this->graphql->execute(
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
        $managedBulkOperation = $this->managedBulkOperationRepository->findOneBy(
            [
                'shop' => $shop,
                'operationId' => $currentOperation->getStringDataByPath('currentBulkOperation/id')
            ]
        );
        // @TODO: SRP; Move response processing into a separate class
        if ($managedBulkOperation === null) {
            $managedBulkOperation = $this->managedBulkOperationRepository->create();
            $managedBulkOperation->setShop($shop);
            $managedBulkOperation->setOperationId(
                (string) $currentOperation->getStringDataByPath('currentBulkOperation/id')
            );
        }
        $managedBulkOperation->setStatus(
            (string) $currentOperation->getStringDataByPath('currentBulkOperation/status')
        );
        $managedBulkOperation->setResponseUrl(
            $currentOperation->getStringDataByPath('currentBulkOperation/url')
        );
        $managedBulkOperation->setLastPolled(new DateTimeImmutable());
        $this->managedBulkOperationRepository->save($managedBulkOperation);

        return $managedBulkOperation;
    }

    /**
     * Process bulk operation result
     *
     * @param ManagedBulkOperation $managedBulkOperation
     * @return BulkOperationResult
     */
    public function processResult(ManagedBulkOperation $managedBulkOperation): BulkOperationResult
    {
        try {
            $fullResponse = $this->httpClient->request(
                'GET',
                (string) $managedBulkOperation->getResponseUrl()
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
        $managedCurrentOperation = null;
        while ($status != 'FAILED' && $status != 'COMPLETED') {
            sleep(30);
            $managedCurrentOperation = $this->currentOperation($shop);
            $status = $managedCurrentOperation->getStatus();
        }

        if ($status == 'COMPLETED') {
            return $this->processResult($managedCurrentOperation);
        }

        return new BulkOperationResult([], 'FAILED');
    }
}
