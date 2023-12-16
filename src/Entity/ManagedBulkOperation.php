<?php

namespace MattDunbar\ShopifyAppBundle\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use MattDunbar\ShopifyAppBundle\Entity\ManagedBulkOperation\Status;
use MattDunbar\ShopifyAppBundle\Repository\ManagedBulkOperationRepository;

#[ORM\Entity(repositoryClass: ManagedBulkOperationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ManagedBulkOperation
{
    /**
     * @var ?int $id
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    /**
     * @var ?DateTimeImmutable $createdAt
     */
    #[ORM\Column]
    private ?DateTimeImmutable $createdAt;
    /**
     * @var ?DateTimeImmutable $updatedAt
     */
    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt;
    /**
     * @var ?DateTimeImmutable $lastPolled
     */
    #[ORM\Column]
    private ?DateTimeImmutable $lastPolled;
    /**
     * @var ?string $operationId
     */
    #[ORM\Column(length: 255)]
    private ?string $operationId = null;
    /**
     * @var ?int $totalRootEntities
     */
    #[ORM\Column(nullable: true)]
    private ?int $totalRootEntities = null;
    /**
     * @var ?int $rootEntitiesProcessed
     */
    #[ORM\Column(nullable: true)]
    private ?int $rootEntitiesProcessed = null;
    /**
     * @var ?Shop $shop
     */
    #[ORM\ManyToOne(targetEntity: Shop::class, inversedBy: 'managedBulkOperations')]
    private ?Shop $shop = null;
    /**
     * @var ?Status $status
     */
    #[ORM\Column(type: "string", enumType: Status::class)]
    private ?Status $status;
    /**
     * @var ?string $responseUrl
     */
    #[ORM\Column(type: "text", nullable: true)]
    private ?string $responseUrl;

    /**
     * Get the value of id
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get OperationId
     *
     * @return ?string
     */
    public function getOperationId(): ?string
    {
        return $this->operationId;
    }

    /**
     * Set OperationId
     *
     * @param string $operationId
     * @return $this
     */
    public function setOperationId(string $operationId): ManagedBulkOperation
    {
        $this->operationId = $operationId;

        return $this;
    }

    /**
     * Get total root entities
     *
     * @return ?int
     */
    public function getTotalRootEntities(): ?int
    {
        return $this->totalRootEntities;
    }

    /**
     * Set Total Root Entities
     *
     * @param int $totalRootEntities
     * @return $this
     */
    public function setTotalRootEntities(int $totalRootEntities): ManagedBulkOperation
    {
        $this->totalRootEntities = $totalRootEntities;

        return $this;
    }

    /**
     * Set Shop
     *
     * @param Shop $shop
     * @return $this
     */
    public function setShop(Shop $shop): ManagedBulkOperation
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * Get Shop
     *
     * @return ?Shop
     */
    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    /**
     * Get Status
     *
     * @return ?Status
     */
    public function getStatus(): ?Status
    {
        return $this->status;
    }

    /**
     * Set Status
     *
     * @param string|Status $status
     * @return $this
     */
    public function setStatus(string|Status $status): ManagedBulkOperation
    {
        if (is_string($status)) {
            $status = Status::from($status);
        }

        $this->status = $status;

        return $this;
    }

    /**
     * Get Is Complete
     *
     * @return bool
     */
    public function getIsComplete(): bool
    {
        return $this->status == Status::COMPLETED;
    }

    /**
     * Get Is Running
     *
     * @return bool
     */
    public function getIsRunning(): bool
    {
        return in_array($this->status, [Status::RUNNING, Status::CREATED]);
    }

    /**
     * Get Root Entities Processed
     *
     * @return ?int
     */
    public function getRootEntitiesProcessed(): ?int
    {
        return $this->rootEntitiesProcessed;
    }

    /**
     * Set Root Entities Processed
     *
     * @param ?int $rootEntitiesProcessed
     * @return $this
     */
    public function setRootEntitiesProcessed(?int $rootEntitiesProcessed): self
    {
        $this->rootEntitiesProcessed = $rootEntitiesProcessed;

        return $this;
    }

    /**
     * Set Created At Value
     *
     * @return void
     */
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new DateTimeImmutable();
        $this->setUpdatedAtValue();
    }

    /**
     * Get Created At
     *
     * @return ?DateTimeImmutable
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Set Updated At Value
     *
     * @return void
     */
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * Get Updated At
     *
     * @return ?DateTimeImmutable
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Get Last Polled
     *
     * @return ?DateTimeImmutable
     */
    public function getLastPolled(): ?DateTimeImmutable
    {
        return $this->lastPolled;
    }

    /**
     * Set Last Polled
     *
     * @param ?DateTimeImmutable $lastPolled
     * @return $this
     */
    public function setLastPolled(?DateTimeImmutable $lastPolled): self
    {
        $this->lastPolled = $lastPolled;

        return $this;
    }

    /**
     * Get Response Url
     *
     * @return ?string
     */
    public function getResponseUrl(): ?string
    {
        return $this->responseUrl;
    }

    /**
     * Set Response Url
     *
     * @param ?string $responseUrl
     * @return $this
     */
    public function setResponseUrl(?string $responseUrl): self
    {
        $this->responseUrl = $responseUrl;

        return $this;
    }
}
