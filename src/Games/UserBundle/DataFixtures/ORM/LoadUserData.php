<?php

namespace Games\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Games\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface {

    public function load(ObjectManager $manager) {

        $listUsers = array(
             array('admin','admin','max--14@hotmail.fr','Maxime','Bourdel')
            ,array('maxime','maxime','bourdel.maxime@hotmail.fr','Maxime','Bourdel')
            ,array('arnaud','arnaud','nonoaj@hotmail.fr','Arnaud','Jehenne')
            ,array('nicolas','nicolas','nicobailleul@hotmail.fr','Nicolas','Bailleul')
            ,array('corentin','corentin','corentin@hotmail.fr','Corentin','Clement')
            ,array('brendan','brendan','brendan.carnot@gmail.com','Brendan','Carnot')
            ,array('aymen','aymen','aymenhafsi@hotmail.fr','Aymen','Hafsi')
            ,array('relou','relou','relou@hotmail.fr','Relou','Fake')
        );
        
        foreach ($listUsers as $key => $values) {
            //création d'un nouvel utilisateur
            $user = new User();
            
            //on attribue les valeurs à l'utilisateur que l'on vient de créer
            $user->setUsername($values[0]);
            $user->setPlainPassword($values[1]);
            $user->setEmail($values[2]);
            $user->setFirstName($values[3]);
            $user->setName($values[4]);
            $user->setEnabled(true);
            //on persiste l'utilisateur
            $manager->persist($user);
            //on crée la référence pour les autres dataLoaders
            $this->addReference('user'.$key, $user);
            echo "Référence user".$key." créé \n";
        }
      
        $manager->flush();
        echo sizeof($listUsers). " utilisateurs ont été créés avec succès \n";
    }
    
    public function getOrder() {
        return 3;
    }
}
