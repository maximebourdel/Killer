<?php
// src/OC/UserBundle/Entity/User.php

namespace Games\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

use Games\KillerBundle\Entity\Killer;

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
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=20)
   */
  private $name;
  
  /**
   * @var string
   *
   * @ORM\Column(name="surname", type="string", length=20)
   */
  private $surname;
  
  
  /**
   * Représente la liste des killers d'un user
   * 
   * @ORM\OneToMany(targetEntity="Games\KillerBundle\Entity\Killer", mappedBy="userAdmin")
   */
  private $myKillers; //avec un s car plusieurs
  
  
  /**
  * @ORM\OneToMany(targetEntity="Games\KillerBundle\Entity\Player", mappedBy="user")
  * @ORM\JoinColumn(nullable=false)
  */
  private $players;
  
 
  public function addMyKiller(\Games\KillerBundle\Entity\Killer $killer)
  {
      $this->myKillers[] = $killer;
      $killer->setUser($this); // On ajoute ceci
      return $this;
  }
  
  public function removeMyKiller(Killer $killer)
  {
      $this->myKillers->removeElement($killer);
  }
  
  public function getMyKillers()
  {
      return $this->myKillers;
  }
  
  
  
  
}