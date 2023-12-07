<?php

namespace MattDunbar\ShopifyAppBundle\Tests\EntityFactory;

use MattDunbar\ShopifyAppBundle\Entity\Install;
use MattDunbar\ShopifyAppBundle\EntityFactory\InstallFactory;
use PHPUnit\Framework\TestCase;

class InstallFactoryTest extends TestCase
{
    public function testInstallFactoryReturnsInstallEntity(): void
    {
        $installFactory = new InstallFactory();
        $this->assertInstanceOf(Install::class, $installFactory->create());
    }
}
