<?php
// src/OC/UserBundle/Entity/User.php

namespace Games\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 */
class User extends BaseUser
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;
  
  
  /**
   * Représente les
   * 
   * @ORM\OneToMany(targetEntity="Games\KillerBundle\Entity\Killer", mappedBy="userAdmin")
   */
  private $myKillers; //avec un s car plusieurs
  
  
  public function createKiller(\Games\KillerBundle\Entity\Killer $killer)
  {
      $this->killers[] = $killer;
      $killers->setUser($this); // On ajoute ceci
      return $this;
  }
}