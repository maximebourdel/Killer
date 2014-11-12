<?php
// src/Sdz/BlogBundle/DataFixtures/ORM/Categories.php

namespace Sdz\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Games\KillerBundle\Entity\Object;

class Objects implements FixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    // Liste des noms de catégorie à ajouter
  	$id = array('1', '2', '3', '4');
    $name = array('Aspirateur', 'Voiture', 'Capote', 'Fer à repasser');
    
    
    
    for($i = 0; $i < 4; $i++)
    {
    	// On crée la catégorie
	    $object = new Object();
	    $object->setTitre($name[$i]);
	    
	    // On la persiste
	    $manager->persist($object);
    }

    // On déclenche l'enregistrement
    $manager->flush();
  }
}