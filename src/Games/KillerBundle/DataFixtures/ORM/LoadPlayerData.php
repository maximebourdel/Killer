<?php

namespace Games\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Games\KillerBundle\Entity\Player;

class LoadPlayerData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface {

    public function load(ObjectManager $manager) {

        
        $listPlayers = array(
              array('user1', 'killer1', 0, NULL, NULL, 0, 0, NULL)//0
            , array('user2', 'killer1', 0, NULL, NULL, 0, 0, NULL)//1
            , array('user3', 'killer1', 0, NULL, NULL, 0, 0, NULL)//2
            , array('user4', 'killer1', 0, NULL, NULL, 0, 0, NULL)//3
            , array('user7', 'killer1', 0, NULL, NULL, 0, 0, NULL)//4

            , array('user2', 'killer2', 0, 'KQNSDJ', NULL, 0, 1, 'weapon2')//5
            , array('user3', 'killer2', 0, 'LIQNET', NULL, 0, 1, 'weapon3')//6
            , array('user4', 'killer2', 0, 'JMPOZR', NULL, 0, 1, 'weapon5')//7
            , array('user5', 'killer2', 0, 'KPLGHF', NULL, 0, 1, 'weapon0')//8
                
            , array('user2', 'killer3', 1, 'KQNSDJ', NULL, 0, 1, 'weapon5')//9
            , array('user3', 'killer3', 0, 'LIQNET', NULL, 0, 1, 'weapon3')//10
            , array('user4', 'killer3', 0, 'JMPOZR', date_create("2016-03-26 23:48:42"), 1, 1, 'weapon5')//11
            , array('user5', 'killer3', 0, 'KPLGHF', NULL, 0, 1, 'weapon0')//12
                
            , array('user2', 'killer4', 1, 'KQNSDJ', NULL, 0, 1, 'weapon5')//13
            , array('user3', 'killer4', 1, 'LIQNET', NULL, 0, 1, 'weapon0')//14
            , array('user4', 'killer4', 0, 'JMPOZR', date_create("2016-03-27 23:48:42"), 1, 1, 'weapon5')//15
            , array('user5', 'killer4', 0, 'KPLGHF', date_create("2016-03-27 23:50:58"), 1, 1, 'weapon0')//16
            
            , array('user2', 'killer5', 2, 'KQNSDJ', NULL, 0, 1, 'weapon5')//17
            , array('user3', 'killer5', 1, 'LIQNET', date_create("2016-03-29 01:50:42"), 1, 1, 'weapon0')//18
            , array('user4', 'killer5', 0, 'JMPOZR', date_create("2016-03-28 23:48:42"), 1, 1, 'weapon5')//19
            , array('user5', 'killer5', 0, 'KPLGHF', date_create("2016-03-28 23:48:42"), 1, 1, 'weapon0')//20
        );
        
        echo "Création des Players \n";
        
        foreach ($listPlayers as $key => $values) {
            //création d'un nouveau Killer
            $player = new Player();
            
            //on attribue les valeurs à l'utilisateur que l'on vient de créer
            $player->setUser($this->getReference($values[0]));
            $player->setKiller($this->getReference($values[1]));
            $player->setNumKills($values[2]);
            $player->setPassword($values[3]);
            $player->setDeathDate($values[4]);
            $player->setIsDead($values[5]);
            $player->setIsAllowed($values[6]);
            //si la valeur n'est pas nulle, on va chercher la référence.
            $values[7] == NULL ? $weapon=$values[7] : $weapon=$this->getReference($values[7]);
            $player->setWeapon($weapon);
            
            //on persiste le Killer
            $manager->persist($player);
            //on crée la référence pour les autres dataLoaders
            $this->addReference('player'.$key, $player);
            echo "Référence player".$key." créé \n";
            
        }
        
        $manager->persist($this->getReference('player5')->setPlayerToKill($this->getReference('player7')));
        $manager->persist($this->getReference('player6')->setPlayerToKill($this->getReference('player8')));
        $manager->persist($this->getReference('player7')->setPlayerToKill($this->getReference('player6')));
        $manager->persist($this->getReference('player8')->setPlayerToKill($this->getReference('player5')));
        
        $manager->persist($this->getReference('player9')->setPlayerToKill($this->getReference('player10')));
        $manager->persist($this->getReference('player10')->setPlayerToKill($this->getReference('player12')));
        $manager->persist($this->getReference('player11')->setPlayerToKill($this->getReference('player10')));
        $manager->persist($this->getReference('player12')->setPlayerToKill($this->getReference('player9')));
        
        $manager->persist($this->getReference('player13')->setPlayerToKill($this->getReference('player14')));
        $manager->persist($this->getReference('player14')->setPlayerToKill($this->getReference('player13')));
        $manager->persist($this->getReference('player15')->setPlayerToKill($this->getReference('player14')));
        $manager->persist($this->getReference('player16')->setPlayerToKill($this->getReference('player13')));
        
        $manager->persist($this->getReference('player17')->setPlayerToKill($this->getReference('player18')));
        $manager->persist($this->getReference('player18')->setPlayerToKill($this->getReference('player17')));
        $manager->persist($this->getReference('player19')->setPlayerToKill($this->getReference('player18')));
        $manager->persist($this->getReference('player20')->setPlayerToKill($this->getReference('player17')));
        
        $manager->flush();
        
        echo sizeof($listPlayers). " Players ont été créés avec succès \n";
    }
    
    public function getOrder() {
        return 5;
    }
}

