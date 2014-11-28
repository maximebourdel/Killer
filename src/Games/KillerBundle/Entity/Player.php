<?php
// src/Games/KillerBundle/Entity/Player.php

namespace Games\KillerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Games\KillerBundle\Entity\PlayerRepository")
 */
class Player
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    

    /**
     * @ORM\ManyToOne(targetEntity="Games\UserBundle\Entity\User", inversedBy="players")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Games\KillerBundle\Entity\Killer", inversedBy="players")
     * @ORM\JoinColumn(nullable=false)
     */
    private $killer;
    
    /**
     * @ORM\Column(name="numkills", type="integer")
     */
    private $numkills;
    
    /**
     * @ORM\Column(name="password", type="string", length=6, nullable=true)
     */
    private $password;
    
    /**
     * @var \DateTime
	 *
	 * @ORM\Column(name="deathDate", type="datetime", nullable=true)
	 */
    private $deathDate;
    
    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="isDead", type="boolean")
     */
    private $isDead;
    
    /**
     * @ORM\Column(name="isAllowed", type="boolean")
     */
    private $isAllowed;

    /**
     * @ORM\ManyToOne(targetEntity="Games\KillerBundle\Entity\Player", inversedBy="player")
     */
    private $playerToKill;
    
    /**
     * @ORM\OneToMany(targetEntity="Games\KillerBundle\Entity\Player", mappedBy="playerToKill")
     */
    private $player;
    
    /**
     * @ORM\ManyToOne(targetEntity="Games\KillerBundle\Entity\Object", inversedBy="players")
     */
    private $object;
    
    
    public function __construct()
    {
        $this->numkills = 0;
        $this->isDead = false;
        $this->isAllowed = false;
    }
    
    
    public function getId ()
    {
        return $this->id;
    }

    public function setId ($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getUser ()
    {
        return $this->user;
    }

    public function setUser ($user)
    {
        $this->user = $user;
        return $this;
    }

    public function getKiller ()
    {
        return $this->killer;
    }

    public function setKiller ($killer)
    {
        $this->killer = $killer;
        return $this;
    }

    public function getNumkills ()
    {
        return $this->numkills;
    }

    public function setNumkills ($numkills)
    {
        $this->numkills = $numkills;
        return $this;
    }

    public function getPassword ()
    {
        return $this->password;
    }

    public function setPassword ($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getDeathDate ()
    {
        return $this->deathDate;
    }

    public function setDeathDate ($deathDate)
    {
        $this->deathDate = $deathDate;
        return $this;
    }

    public function getIsDead ()
    {
        return $this->isDead;
    }

    public function setIsDead ($isDead)
    {
        $this->isDead = $isDead;
        return $this;
    }

    public function getIsAllowed ()
    {
        return $this->isAllowed;
    }

    public function setIsAllowed ($isAllowed)
    {
        $this->isAllowed = $isAllowed;
        return $this;
    }
    
    public function getPlayerToKill ()
    {
        return $this->playerToKill;
    }
    
    public function setPlayerToKill ($playerToKill)
    {
        $this->playerToKill = $playerToKill;
        return $this;
    }

    public function getObject ()
    {
        return $this->object;
    }

    public function setObject ($object)
    {
        $this->object = $object;
        return $this;
    }

    public function getPlayer ()
    {
        return $this->player;
    }

    public function setPlayer ($player)
    {
        $this->player = $player;
        return $this;
    }
 
 
    
    
    
}