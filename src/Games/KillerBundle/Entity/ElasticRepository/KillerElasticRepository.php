<?php

namespace Games\KillerBundle\Entity\ElasticRepository;

use FOS\ElasticaBundle\Repository;

class KillerElasticRepository extends Repository
{
    /**
     * @param $searchText
     * @return array<Article>
     */
    public function findAdress($searchText)
    {
        $query_part = new \Elastica\Query\Bool();
        $query_part->addShould(
            new \Elastica\Query\Term(array(
                    'name' => array('value' => $searchText, 'boost' => 3),
            ))
        );

        $filters = new \Elastica\Filter\Bool();
        
        $query = new \Elastica\Query\Filtered($query_part, $filters);

        // return $this->findHybrid($query); if you also want the ES ResultSet
        return $this->find($query,5);
    }
}