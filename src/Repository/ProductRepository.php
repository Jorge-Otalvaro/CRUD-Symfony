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

    public function findAll()
    {
        return $this->findBy(array(), array('id' => 'ASC'));
    }

    /**
     * Buscar todos las Categorias
     */
    public function buscarTodosLosProductos()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT prod.id, prod.code, prod.name, prod.description, prod.brand, prod.price, prod.createdAt, prod.updatedAt
            FROM App:Product prod
            ORDER BY prod.id ASC'
        );

        return $query;
    }

    /**
     * @return Product[]
     */
    public function findAllGreaterThanPrice($filter): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT prod
            FROM App:Product prod
            WHERE 
            prod.id like :id OR prod.name like :name OR
            prod.code like :code OR prod.brand like :brand
            ORDER BY prod.name ASC'
        )->setParameter([
            'id' => '%' . $filter . '%',
            'code' => '%' . $filter . '%',
            'name' => '%' . $filter . '%',
            'brand' => '%' . $filter . '%'
        ]);
        
        return $query->getResult();
    }
}
