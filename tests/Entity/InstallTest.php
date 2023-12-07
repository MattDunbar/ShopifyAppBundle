<?php

namespace MattDunbar\ShopifyAppBundle\Tests\Entity;

use MattDunbar\ShopifyAppBundle\Entity\Install;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class InstallTest extends TestCase
{
    public function testSetGetShopifyDomain(): void
    {
        $install = new Install();
        $install->setShopifyDomain('example.myshopify.com');
        $this->assertEquals('example.myshopify.com', $install->getShopifyDomain());
    }

    public function testLoadValidatorMetadataValidatesShopifyDomain(): void
    {
        $mockClassMetaData = $this->createMock(ClassMetadata::class);
        $mockClassMetaData->expects($this->once())
            ->method('addPropertyConstraint')
            ->with('shopifyDomain', $this->isInstanceOf(Regex::class));
        Install::loadValidatorMetadata($mockClassMetaData);
    }
}
