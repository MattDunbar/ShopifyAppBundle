<?php

namespace MattDunbar\ShopifyAppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class InstallType extends AbstractType
{
    /**
     * Build Install Form
     *
     * @param  FormBuilderInterface $builder
     * @param  array<string,mixed>  $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shopifyDomain', TextType::class)
            ->add('install', SubmitType::class);
    }
}
