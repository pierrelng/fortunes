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
    public function findLast()
    {
        $queryBuilder = $this->createQueryBuilder('F')->orderBy('F.createdAt', 'DESC'); // 'F' is an alias
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
        return $this->createQueryBuilder('F')->where('F.published = false')->getQuery()->getResult(); // 'F' is an alias
    }

}

