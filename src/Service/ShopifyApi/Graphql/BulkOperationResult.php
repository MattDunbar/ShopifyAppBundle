<?php

namespace MattDunbar\ShopifyAppBundle\Service\ShopifyApi\Graphql;

class BulkOperationResult
{
    /**
     * @var array<mixed> $data
     */
    protected array $data;
    /**
     * @var string $status
     */
    protected string $status;

    /**
     * Constructor
     *
     * @param array<mixed> $data
     * @param string $status
     */
    public function __construct(
        array $data,
        string $status
    ) {
        $this->data = $data;
        $this->status = strtoupper($status);
    }

    /**
     * Get Data
     *
     * @return array<mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get Status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Get successful (status == COMPLETED)
     *
     * @return bool
     */
    public function getSuccessful(): bool
    {
        return $this->status === 'COMPLETED';
    }
}
