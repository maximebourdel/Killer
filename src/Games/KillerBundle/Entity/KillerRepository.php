<?php

namespace Games\KillerBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * KillerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class KillerRepository extends EntityRepository
{
    public function getNbPlayersAllowed(Killer $killer)
    {
        $qb = $this->createQueryBuilder('p')->getScalarResult();
    
        $qb->join('p.players', 'players')
        ->addSelect('players');
        
        
        $qb->where('players.isAllowed = true')
        ->andWhere('p.killer = :id')
        ->setParameter('id', $killer->getId())
        ;
    
        return $qb
        ->getQuery()
        ->getResult()
        ;
    }
}
