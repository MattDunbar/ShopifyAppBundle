<?php

namespace MattDunbar\ShopifyAppBundle\Tests\Service;

use MattDunbar\ShopifyAppBundle\Repository\ShopRepository;
use MattDunbar\ShopifyAppBundle\Service\ShopifyApi;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ShopifyApiTest extends TestCase
{
    public function testInitialize(): void
    {
        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects($this->never())->method($this->anything());
        $mockShopRepository = $this->createMock(ShopRepository::class);
        new ShopifyApi(
            'example-app',
            'example-api-key',
            'example-secret',
            'write_products',
            'https://example.com',
            $mockLogger,
            $mockShopRepository
        );
    }

    public function testGetInstallUrl(): void
    {
        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockShopRepository = $this->createMock(ShopRepository::class);
        $shopifyApi = new ShopifyApi(
            'example-app',
            'example-api-key',
            'example-secret',
            'write_products',
            'https://example.com',
            $mockLogger,
            $mockShopRepository
        );
        $installDetails = $shopifyApi->getInstallDetails(
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
