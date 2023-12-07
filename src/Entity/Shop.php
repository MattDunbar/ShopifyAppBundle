<?php

namespace MattDunbar\ShopifyAppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MattDunbar\ShopifyAppBundle\Repository\ShopRepository;
use MattDunbar\ShopifyAppBundle\Service\ShopifyApi;

#[ORM\Entity(repositoryClass: ShopRepository::class)]
class Shop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $shopDomain = null;

    #[ORM\Column(length: 255)]
    private ?string $accessToken = null;

    #[ORM\Column(length: 255)]
    private ?string $scope = null;

    /**
     * Get the value of id
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of shopDomain
     *
     * @return string|null
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
     * @return string|null
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
     * @return string|null
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
     * Get Shop's Admin App URL
     *
     * @param  string $appUrlKey
     * @return string
     */
    public function getAppAdminUrl(string $appUrlKey): string
    {
        return "https://{$this->shopDomain}/admin/apps/{$appUrlKey}";
    }
}
