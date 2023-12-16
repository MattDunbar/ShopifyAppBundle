<?php

namespace MattDunbar\ShopifyAppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use MattDunbar\ShopifyAppBundle\Repository\ManagedBulkOperationRepository;
use MattDunbar\ShopifyAppBundle\Repository\ShopRepository;
use MattDunbar\ShopifyAppBundle\Service\ShopifyApi;

#[ORM\Entity(repositoryClass: ShopRepository::class)]
class Shop
{
    /**
     * @var ?int $id
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    /**
     * @var ?string $shopDomain
     */
    #[ORM\Column(length: 255)]
    private ?string $shopDomain = null;
    /**
     * @var ?string $accessToken
     */
    #[ORM\Column(length: 255)]
    private ?string $accessToken = null;
    /**
     * @var ?string $scope
     */
    #[ORM\Column(length: 255)]
    private ?string $scope = null;
    /**
     * @var Collection<int, ManagedBulkOperation> $managedBulkOperations
     */
    #[ORM\OneToMany(mappedBy: 'shop', targetEntity: ManagedBulkOperation::class)]
    private Collection $managedBulkOperations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->managedBulkOperations = new ArrayCollection();
    }

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
     * Get the value of shopDomain
     *
     * @return ?string
     */
    public function getShopDomain(): ?string
    {
        return $this->shopDomain;
    }

    /**
     * Set the value of shopDomain
     *
     * @param  string $shopDomain
     * @return Shop
     */
    public function setShopDomain(string $shopDomain): Shop
    {
        $this->shopDomain = $shopDomain;

        return $this;
    }

    /**
     * Get the value of accessToken
     *
     * @return ?string
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * Set the value of accessToken
     *
     * @param  string $accessToken
     * @return Shop
     */
    public function setAccessToken(string $accessToken): Shop
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get the value of scope
     *
     * @return ?string
     */
    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * Set the value of scope
     *
     * @param  string $scope
     * @return Shop
     */
    public function setScope(string $scope): Shop
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get managedBulkOperations
     *
     * @return Collection<int, ManagedBulkOperation>
     */
    public function getManagedBulkOperations(): Collection
    {
        return $this->managedBulkOperations;
    }

    /**
     * Get Shop's Admin App URL
     *
     * @param  string $appUrlKey
     * @return string
     */
    public function getAppAdminUrl(string $appUrlKey): string
    {
        return "https://{$this->shopDomain}/admin/apps/{$appUrlKey}";
    }

    /**
     * Add Managed Bulk Operation
     *
     * @param ManagedBulkOperation $managedBulkOperation
     * @return Shop
     */
    public function addManagedBulkOperation(ManagedBulkOperation $managedBulkOperation): Shop
    {
        if (!$this->managedBulkOperations->contains($managedBulkOperation)) {
            $this->managedBulkOperations->add($managedBulkOperation);
        }

        return $this;
    }

    /**
     * Remove Managed Bulk Operation
     *
     * @param ManagedBulkOperation $managedBulkOperation
     * @return Shop
     */
    public function removeManagedBulkOperation(ManagedBulkOperation $managedBulkOperation): Shop
    {
        $this->managedBulkOperations->removeElement($managedBulkOperation);

        return $this;
    }
}
