<?php

namespace App\Repository;

use App\Entity\Wish;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Wish|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wish|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wish[]    findAll()
 * @method Wish[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wish::class);
    }

    public function findPublishedWishesWithCategories(): ?array
    { // on crée un query builder et on donne l'alias de w à Wish
        $queryBuilder = $this->createQueryBuilder('w');
        // on ajoute la jointure avec catégorie, pour éviter les multiples requêtes
        // on oublie pas de sélectionner les données !
        $queryBuilder->join('w.categorie', 'c')
            ->addSelect('c');
        // clause where...
        $queryBuilder->andWhere('w.isPublished = 1');
        // order by...
        $queryBuilder->orderBy('w.dateCreated', 'DESC');
        // récupère l'objet query de doctrine
        $query = $queryBuilder->getQuery();
        // retourne le résultat de la requête
        return $query->getResult(); }
}
