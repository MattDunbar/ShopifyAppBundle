<?php

namespace MattDunbar\ShopifyAppBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use MattDunbar\ShopifyAppBundle\Entity\ManagedBulkOperation;

/**
 * @extends ServiceEntityRepository<ManagedBulkOperation>
 */
class ManagedBulkOperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ManagedBulkOperation::class);
    }

    /**
     * Save ManagedBulkOperation
     *
     * @param  ManagedBulkOperation $managedBulkOperation
     * @return void
     */
    public function save(ManagedBulkOperation $managedBulkOperation): void
    {
        $this->_em->persist($managedBulkOperation);
        $this->_em->flush();
    }

    public function create(): ManagedBulkOperation
    {
        return new ManagedBulkOperation();
    }
}
