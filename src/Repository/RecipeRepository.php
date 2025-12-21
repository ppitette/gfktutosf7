<?php

namespace App\Repository;

use App\Entity\Recipe;
// use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findTotalDuration(): int
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.duration) as total')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @return Recipe[] Returns an array of Recipe objects
     */
    public function findWithDurationLowerThan(int $duration): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.duration <= :duration')
            ->setParameter('duration', $duration)
            ->orderBy('r.duration', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    // Avec le Paginator de Doctrine
    // public function paginateRecipes(int $page, int $limit): Paginator
    // {
    //     return new Paginator(
    //         $this->createQueryBuilder('r')
    //             ->setFirstResult(($page - 1) * $limit)
    //             ->setMaxResults($limit)
    //             ->getQuery()
    //             ->setHint(Paginator::HINT_ENABLE_DISTINCT, false)
    //     );
    // }

    public function paginateRecipes(int $page, ?int $userId): PaginationInterface
    {
        $builder = $this->createQueryBuilder('r')->leftJoin('r.category', 'c')->select('r', 'c');

        if ($userId) {
            $builder = $builder->andWhere('r.user = :user')
                ->setParameter('user', $userId);
        }

        return $this->paginator->paginate(
            $builder,
            $page,
            10,
            [
                'distinct' => false,
                'sortFieldAllowList' => ['r.id', 'r.title'],
            ]
        );
    }

    public function findAllWithCategories(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r', 'c')
            ->leftJoin('r.category', 'c')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
}
