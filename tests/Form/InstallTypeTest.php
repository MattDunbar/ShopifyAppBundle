<?php

namespace MattDunbar\ShopifyAppBundle\Tests\Form;

use MattDunbar\ShopifyAppBundle\Form\InstallType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class InstallTypeTest extends TestCase
{
    public function testBuildFormContainsShopifyDomain(): void
    {
        $installType = new InstallType();
        $mockFormBuilder = $this->createMock(FormBuilderInterface::class);
        $mockFormBuilder->expects($this->once())
            ->method('add')
            ->with('shopifyDomain', TextType::class);
        $installType->buildForm($mockFormBuilder, []);
    }
}
