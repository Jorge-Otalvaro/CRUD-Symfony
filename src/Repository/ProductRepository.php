<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\ORM\Tools\Pagination\Paginator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Product $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Product $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Buscar todos las Categorias
     */
    public function buscarTodosLosProductos()
    {
        return $this->getEntityManager()->createQuery(
            'SELECT prod.id, prod.code, prod.name, prod.description, prod.brand, prod.price, prod.createdAt, prod.updatedAt FROM  App:Product prod'
        );
    }

    public function findFilter($filter)
    {
        return $this->createQueryBuilder("a")->andWhere('a.id like :id' OR 'a.code like :code' OR 'a.name like :name' OR 'a.brand like :brand')
        ->setParameters([
            'id' => '%' . $filter . '%',
            'code' => '%' . $filter . '%',
            'name' => '%' . $filter . '%',
            'brand' => '%' . $filter . '%'
        ])->getQuery();
    }
}
