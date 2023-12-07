<?php

namespace MattDunbar\ShopifyAppBundle\Entity;

use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class Install
{
    /**
     * @var string $shopifyDomain
     */
    protected string $shopifyDomain;

    /**
     * Get Shopify Domain
     *
     * @return string
     */
    public function getShopifyDomain(): string
    {
        return $this->shopifyDomain;
    }

    /**
     * Set Shopify Domain
     *
     * @param  string $shopifyDomain
     * @return void
     */
    public function setShopifyDomain(string $shopifyDomain): void
    {
        $this->shopifyDomain = $shopifyDomain;
    }

    /**
     * Load Validator Metadata
     *
     * @param  ClassMetadata $metadata
     * @return void
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint(
            'shopifyDomain',
            new Regex(
                [
                'pattern' => '/^[a-zA-Z0-9][a-zA-Z0-9\-]*\.myshopify\.com/',
                ]
            )
        );
    }
}
