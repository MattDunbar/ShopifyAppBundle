<?php

namespace MattDunbar\ShopifyAppBundle\EntityFactory;

use MattDunbar\ShopifyAppBundle\Entity\Install;

class InstallFactory
{
    /**
     * Create Install
     *
     * @return Install
     */
    public function create(): Install
    {
        return new Install();
    }
}
