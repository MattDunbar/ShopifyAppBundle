<?php

namespace MattDunbar\ShopifyAppBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use MattDunbar\ShopifyAppBundle\Entity\Shop;

/**
 * @extends ServiceEntityRepository<Shop>
 */
class ShopRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shop::class);
    }

    /**
     * Find One By Shop Domain
     *
     * @param  string $shopDomain
     * @return ?Shop
     */
    public function findOneByShopDomain(string $shopDomain): ?Shop
    {
        try {
            /** @var Shop $shop */
            $shop = $this->createQueryBuilder('s')
                ->andWhere('s.shopDomain = :val')
                ->setParameter('val', $shopDomain)
                ->getQuery()
                ->getOneOrNullResult();
            return $shop;
        } catch (NonUniqueResultException $exception) {
            return null;
        }
    }

    /**
     * Find or create by shop domain
     *
     * @param  string $shopDomain
     * @return Shop
     */
    public function findOrCreateByShopDomain(string $shopDomain): Shop
    {
        $shop = $this->findOneByShopDomain($shopDomain);
        if ($shop === null) {
            $shop = new Shop();
            $shop->setShopDomain($shopDomain);
        }
        return $shop;
    }

    /**
     * Create or update by shop domain
     *
     * @param string $shopDomain
     * @param string $accessToken
     * @param string $scope
     * @return Shop
     */
    public function createOrUpdateByShopDomain(string $shopDomain, string $accessToken, string $scope): Shop
    {
        $shop = $this->findOrCreateByShopDomain($shopDomain);
        $shop->setAccessToken($accessToken);
        $shop->setScope($scope);
        $this->save($shop);

        return $shop;
    }

    /**
     * Save Shop
     *
     * @param  Shop $shop
     * @return void
     */
    public function save(Shop $shop): void
    {
        $this->_em->persist($shop);
        $this->_em->flush();
    }
}
