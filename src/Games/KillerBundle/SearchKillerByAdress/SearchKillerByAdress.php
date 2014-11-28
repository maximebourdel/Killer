<?php

namespace Games\KillerBundle\SearchKillerByAdress;


use Games\KillerBundle\Entity\ElasticRepository\KillerElasticRepository;

use Symfony\Component\HttpFoundation\JsonResponse;

class SearchKillerByAdress
{
    public function searchAdress($data)
    {
        
         // ...avant de les retourner en json
        return new JsonResponse($data, 200, array(
                'Cache-Control' => 'no-cache',
        ));
    }
    
}