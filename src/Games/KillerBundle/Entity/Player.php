<?php
// src/Games/KillerBundle/Entity/Player.php

namespace Games\KillerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\ManyToOne(targetEntity="Games\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Games\KillerBundle\Entity\Killer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $killer;
    
    /**
     * @ORM\Column(name="numkills", type="integer")
     */
    private $numkills;
    
    /**
     * @ORM\Column(name="password", type="string", length=6)
     */
    private $password;
    
    /**
     * @var \DateTime
	 *
	 * @ORM\Column(name="deathDate", type="datetime")
	 * @Assert\DateTime()
	 */
    private $deathDate;
    
    /**
     * @ORM\Column(name="isDead", type="boolean")
     */
    private $isDead;
}