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
   *
   * @var string @ORM\Column(type="string", length=255, nullable=false)
   */
  private $firstName;
  
  /**
   *
   * @var string @ORM\Column(type="string", length=255, nullable=false)
   */
  private $name;
  
  
  /**
   * Représente la liste des killers d'un user
   * 
   * @ORM\OneToMany(targetEntity="Games\KillerBundle\Entity\Killer", mappedBy="userAdmin", cascade={"persist"})
   * @ORM\joinColumn(onDelete="CASCADE")
   */
  private $myKillers; //avec un s car plusieurs
  
  
  /**
  * @ORM\OneToMany(targetEntity="Games\KillerBundle\Entity\Player", mappedBy="user")
  * @ORM\JoinColumn(nullable=false)
  */
  private $players;
  
  
  /**
   * Set name
   *
   * @param string $name
   * @return User
   */
  public function setName($name) {
      $this->name = $name;
  
      return $this;
  }
  
  /**
   * Get name
   *
   * @return string
   */
  public function getName() {
      return $this->name;
  }
  
  /**
   * Set firstName
   *
   * @param string $firstName
   * @return User
   */
  public function setFirstName($firstName) {
      $this->firstName = $firstName;
  
      return $this;
  }
  
  /**
   * Get firstName
   *
   * @return string
   */
  public function getFirstName() {
      return $this->firstName;
  }
  
  
  
 
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