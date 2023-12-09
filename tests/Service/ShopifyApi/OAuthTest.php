<?php

namespace MattDunbar\ShopifyAppBundle\Tests\Service\ShopifyApi;

use MattDunbar\ShopifyAppBundle\Repository\ShopRepository;
use MattDunbar\ShopifyAppBundle\Service\ShopifyApi;
use PHPUnit\Framework\TestCase;
use Shopify\Auth\FileSessionStorage;
use Shopify\Context;

class OAuthTest extends TestCase
{
    public function testGetInstallUrl(): void
    {
        // @TODO: Build a wrapper around this to avoid static methods that can't be tested properly
        Context::initialize(
            'testApiKey',
            'testApiSecret',
            'any-scope',
            'https://example.com',
            new FileSessionStorage()
        );

        $mockShopRepository = $this->createMock(ShopRepository::class);
        $mockShopifyConfig = $this->createMock(ShopifyApi\Config::class);
        $mockShopifyConfig->expects($this->once())
            ->method('initialize');

        $shopifyApi = new ShopifyApi\OAuth(
            $mockShopRepository,
            $mockShopifyConfig
        );
        $installDetails = $shopifyApi->startInstall(
            'example.myshopify.com',
            'https://example.com/redirect'
        );
        $this->assertIsArray($installDetails);
        $installUrl = $installDetails['url'] ?? null;
        $this->assertNotNull($installUrl);
        $this->assertStringStartsWith('https://example.myshopify.com/admin/oauth/authorize', $installUrl);
        $this->assertStringContainsString('/oauth/authorize', $installUrl);
    }
}
