<?php

namespace Games\KillerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Games\KillerBundle\Entity\Object;

class Objects extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface {
    
    public function load(ObjectManager $manager) {
        
        $listObjets = array(
            'un aspirateur', 'une voiture', 'une télé'
            , 'une capote', 'un fer à repasser', 'bob l\'eponge'
            , 'un fer à lisser', 'une poêle', 'une brouette'
        );
    
        foreach ($listObjets as $key => $name) {
        	//On crée l'objet
    	    $object = new Object();
    	    $object->setName($name);
    	    //on persiste l'objet
    	    $manager->persist($object);
    	    //on crée la référence pour les autres dataLoaders
    	    $this->addReference('object'.$key, $object);
    	    echo "Référence object".$key." créé \n";
        }
        //On déclenche l'enregistrement
        $manager->flush();
        echo sizeof($listObjets). " objets ont été créés avec succès \n";
    }
  
    public function getOrder() {
        return 2;
    }
}