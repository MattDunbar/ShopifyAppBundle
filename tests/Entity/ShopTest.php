<?php

namespace MattDunbar\ShopifyAppBundle\Tests\Entity;

use MattDunbar\ShopifyAppBundle\Entity\Shop;
use PHPUnit\Framework\TestCase;

class ShopTest extends TestCase
{
    public function testSetGetShopDomain(): void
    {
        $install = new Shop();
        $install->setShopDomain('example.myshopify.com');
        $this->assertEquals('example.myshopify.com', $install->getShopDomain());
    }
}
