<?php

namespace AppBundle\Entity;

use Pagerfanta\Adapter\DoctrineORMAdapter;

/**
 * FortuneRepository
 *
 */
class FortuneRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Get last items
     *
     * @return Fortune[]
     */
    public function findLastPublished()
    {
        $queryBuilder = $this->createQueryBuilder('F')->where('F.published = true')->orderBy('F.createdAt', 'DESC'); // 'F' is an alias
        return new DoctrineORMAdapter($queryBuilder);
    }

    /**
     * Get top rated items
     *
     * @return Fortune[]
     */
    public function findBestRated($nb)
    {
        return $this->createQueryBuilder('F')->setMaxResults($nb)->orderBy('F.upVote-F.downVote', 'DESC')->getQuery()->getResult(); // 'F' is an alias
    }

    /**
     * Get items by author
     *
     * @return Fortune[]
     */
    public function findByAuthor($author)
    {
        return $this->createQueryBuilder('F')->where('F.author = :author')->setParameter("author", $author)->getQuery()->getResult(); // 'F' is an alias
    }

    /**
     * Get unpublished items
     *
     * @return Fortune[]
     */
    public function findUnpublished()
    {
        return $this->createQueryBuilder('F')->where('F.published = false')->orderBy('F.createdAt', 'DESC')->getQuery()->getResult(); // 'F' is an alias
    }

    /**
     * Count unpublished items
     *
     * @return Fortune[]
     */
    public function countUnpublished()
    {
        return $this->createQueryBuilder('F')->select('COUNT(F)')->where('F.published = false')->orderBy('F.createdAt', 'DESC')->getQuery()->getSingleScalarResult(); // 'F' is an alias
    }

}

