<?php

namespace App\Repository;


use App\Entity\Comments\Comments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CommentsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comments::class);
    }

    public function getParentComments(int $limit)
    {
        return $this->createQueryBuilder('comments')
            ->andWhere('comments.parent_id is null')
            ->orderBy('comments.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getChildComments(int $limit, int $parent_id)
    {
        return $this->createQueryBuilder('comments')
            ->andWhere('comments.parent_id = :parent_id')
            ->setParameter('parent_id', $parent_id)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getMoreChildComments(int $limit, int $parent_id, int $last_child_id)
    {
        return $this->createQueryBuilder('comments')
            ->andWhere('comments.parent_id = :parent_id')
            ->andWhere('comments.id > :id')
            ->setParameters([ 'parent_id' => $parent_id, 'id' => $last_child_id ])
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
