<?php

namespace Games\KillerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Object
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Games\KillerBundle\Entity\ObjectRepository")
 */
class Object
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * @ORM\OneToOne(targetEntity="Games\KillerBundle\Entity\Image", cascade={"persist"})
     * @ORM\joinColumn(onDelete="CASCADE")
     */
    private $image;
    
    /**
     * @ORM\OneToMany(targetEntity="Games\KillerBundle\Entity\Player", mappedBy="object")
     */
    private $players;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Object
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    
    public function getImage ()
    {
        return $this->image;
    }

    public function setImage ($image)
    {
        $this->image = $image;
        return $this;
    }

    public function getPlayers ()
    {
        return $this->players;
    }

    public function setPlayers ($players)
    {
        $this->players = $players;
        return $this;
    }
 
 
    
    
}
