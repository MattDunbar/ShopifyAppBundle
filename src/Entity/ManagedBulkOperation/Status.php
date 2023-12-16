<?php

namespace MattDunbar\ShopifyAppBundle\Entity\ManagedBulkOperation;

enum Status: string
{
    case CANCELED = 'CANCELED';
    case CANCELING = 'CANCELING';
    case COMPLETED = 'COMPLETED';
    case CREATED = 'CREATED';
    case EXPIRED = 'EXPIRED';
    case FAILED = 'FAILED';
    case RUNNING = 'RUNNING';
}
