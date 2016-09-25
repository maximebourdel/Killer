<?php

namespace Games\KillerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Games\KillerBundle\Entity\Weapon;

class Weapons extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface {
    
    public function load(ObjectManager $manager) {
        
        $listWeapons = array(
            'un aspirateur', 'une voiture', 'une télé'
            , 'une capote', 'un fer à repasser', 'bob l\'eponge'
            , 'un fer à lisser', 'une poêle', 'une brouette'
        );
    
        
        echo "Création des Weapons \n";
        
        foreach ($listWeapons as $key => $name) {
        	//On crée l'objet
    	    $weapon = new Weapon();
    	    $weapon->setName($name);
    	    //on persiste l'objet
    	    $manager->persist($weapon);
    	    //on crée la référence pour les autres dataLoaders
    	    $this->addReference('weapon'.$key, $weapon);
    	    echo "Référence weapon".$key." créé \n";
        }
        //On déclenche l'enregistrement
        $manager->flush();
        echo sizeof($listWeapons). " Weapons ont été créés avec succès \n";
    }
  
    public function getOrder() {
        return 2;
    }
}