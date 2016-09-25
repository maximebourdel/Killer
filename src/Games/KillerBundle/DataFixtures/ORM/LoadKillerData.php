<?php

namespace Games\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Games\KillerBundle\Entity\Killer;

class LoadKillerData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface {

    public function load(ObjectManager $manager) {

        
        $listKillers = array(
              array('0 players non commencé', NULL, NULL, 'user0', 0, '1789 La Broche à Rôtir, 14600 Ablon, France', 48.741700879765396, 2.5041505694389343 )
            , array('5 players non commencé', NULL, NULL, 'user0', 0, '10 Allée Paul Gauguin, 94450 Limeil-Brévannes, France', 49.38808887684188, 0.2780699282993737 ) 
            , array('4 players commencé (0 morts)', date_create("2016-03-26 14:01:15"), NULL, 'user0', 4, 'Le Grand Bœuf, 18360 Épineuil-le-Fleuriel, France', 46.55130547880643, 2.5041505694389343 )
            , array('4 players commencé (1 mort)', date_create("2016-03-27 15:25:18"), NULL, 'user0', 4, 'Unnamed Road, 32550 Lasseube-Propre, France', 43.57243174740972, 0.5705568194389343 )
            , array('4 players commencé (2 morts)', date_create("2016-03-28 17:04:21"), NULL, 'user0', 4, 'Camino Bajo, 24, 19443 Cobeta, Guadalajara, Spain', 40.871987756697415, -2.1540525555610657 )
            , array('4 players terminé (3 morts)', date_create("2016-03-29 01:50:42"), NULL, 'user0', 4, 'Camino Alto de Alfaraz de Sayago, 37116 Moraleja de Sayago, Zamora, Spain', 41.203456192051284, -6.021240055561066 )
        );
        
        echo "Création des Killers \n";
        
        foreach ($listKillers as $key => $values) {
            //création d'un nouveau Killer
            $killer = new Killer();
            
            //on attribue les valeurs à l'utilisateur que l'on vient de créer
            $killer->setName($values[0]);
            $killer->setDateBegin($values[1]);
            $killer->setDateEnd($values[2]);
            $killer->setUser($this->getReference($values[3]));
            $killer->setNbParticipants($values[4]);
            $killer->setAdresse($values[5]);
            $killer->setLatitude($values[6]);
            $killer->setLongitude($values[7]);
            //on persiste le Killer
            $manager->persist($killer);
            //on crée la référence pour les autres dataLoaders
            $this->addReference('killer'.$key, $killer);
            echo "Référence killer".$key." créé \n";
        }
      
        $manager->flush();
        echo sizeof($listKillers). " Killers ont été créés avec succès \n";
    }
    
    public function getOrder() {
        return 4;
    }
}

