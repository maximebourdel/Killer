<?php

namespace Games\KillerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Games\UserBundle\Entity\User;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Killer
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Games\KillerBundle\Entity\KillerRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields="name", message="Un autre killer de ce nom existe déja.")
 */
class Killer {
	
	/**
	 *
	 * @var integer @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Games\UserBundle\Entity\User", inversedBy="myKillers")
	 * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	 */
	private $userAdmin;
	
	/**
	 * @ORM\OneToMany(targetEntity="Games\KillerBundle\Entity\Player", mappedBy="killer")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $players;
	
	/**
	 * @var string @ORM\Column(name="name", type="string", length=255, unique=true)
	 */
	private $name;
	
	/**
	 * @ORM\Column(name="nbParticipants", type="integer")
	 */
	private $nbParticipants;
	
	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="dateCreation", type="datetime", nullable=false)
	 */
	private $dateCreation;
	
	/**
     * @var \DateTime
	 *
	 * @ORM\Column(name="dateBegin", type="datetime", nullable=true)
	 */
	private $dateBegin;
	
	/**
     * @var \DateTime
	 *
	 * @ORM\Column(name="dateEnd", type="datetime", nullable=true)
	 */
	private $dateEnd;
	
	/**
	 *
	 * @var string @ORM\Column(name="adresse", type="string", length=255)
	 * @Assert\NotBlank()
	 * @Assert\NotNull()
	 */
	private $adresse;
	
	/**
	 * @ORM\Column(name="latitude", type="float")
	 */
	private $latitude;
	
	/**
	 * @ORM\Column(name="longitude", type="float")
	 */
	private $longitude;
	
	
	
	
	
	public function __construct()
	{
	    $this->nbParticipants = 0;
	    $this->dateCreation = new \DateTime();
	}
	
	
	
	
	
	
	
	
	
	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Set name
	 *
	 * @param string $name        	
	 * @return Killer
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
	 * Set userAdmin
	 *
	 * @param Games\UserBundle\Entity\User $userAdmin
	 */
	public function setUser(\Games\UserBundle\Entity\User $userAdmin)
	{
	    $this->userAdmin = $userAdmin;
	}
	
	/**
	 * Get userAdmin
	 *
	 * @return Games\UserBundle\Entity\User
	 */
	public function getUserAdmin()
	{
	    return $this->userAdmin;
	}
	
	/**
	 * Set nbParticipants
	 *
	 * @param string $nbParticipants
	 * @return Killer
	 */
	public function setNbParticipants($nbParticipants) {
	    $this->nbParticipants = $nbParticipants;
	
	    return $this;
	}
	
	/**
	 * Get nbParticipants
	 *
	 * @return Games\UserBundle\Entity\User
	 */
	public function getNbParticipants()
	{
	    return $this->nbParticipants;
	}
	

	/**
	 * Set dateCreation
	 *
	 * @param string $dateCreation
	 * @return Killer
	 */
	public function setDateCreation($dateCreation) {
	    $this->dateCreation = $dateCreation;
	
	    return $this;
	}
	
	/**
	 * Get dateCreation
	 *
	 * @return string
	 */
	public function getDateCreation() {
	    return $this->dateCreation;
	}
	
	/**
	 * Set dateBegin
	 *
	 * @param string $dateBegin
	 * @return Killer
	 */
	public function setDateBegin($dateBegin) {
	    $this->dateBegin = $dateBegin;
	
	    return $this;
	}
	
	/**
	 * Get dateBegin
	 *
	 * @return string
	 */
	public function getDateBegin() {
	    return $this->dateBegin;
	}
	

	/**
	 * Set dateEnd
	 *
	 * @param string $dateEnd
	 * @return Killer
	 */
	public function setDateEnd($dateEnd) {
	    $this->dateEnd = $dateEnd;
	
	    return $this;
	}
	
	/**
	 * Get dateEnd
	 *
	 * @return string
	 */
	public function getDateEnd() {
	    return $this->dateEnd;
	}
	
	/**
	 * Set adresse
	 *
	 * @param string $adresse
	 * @return Killer
	 */
	public function setAdresse($adresse) {
	    $this->adresse = $adresse;
	
	    return $this;
	}
	
	/**
	 * Get adresse
	 *
	 * @return string
	 */
	public function getAdresse() {
	    return $this->adresse;
	}
	
	/**
	 * Set latitude
	 *
	 * @param float $latitude
	 * @return Killer
	 */
	public function setLatitude($latitude) {
	    $this->latitude = $latitude;
	
	    return $this;
	}
	
	/**
	 * Get latitude
	 *
	 * @return float
	 */
	public function getLatitude() {
	    return $this->latitude;
	}
	
	/**
	 * Set longitude
	 *
	 * @param float $longitude
	 * @return Killer
	 */
	public function setLongitude($longitude) {
	    $this->longitude = $longitude;
	
	    return $this;
	}
	
	/**
	 * Get longitude
	 *
	 * @return float
	 */
	public function getLongitude() {
	    return $this->longitude;
	}
	
	/**
	 * Get list of players
	 * 
	 * @return List Killer
	 */
	public function getPlayers(){
        return $this->players;
	}
	
	/**
	 * Ajouter un nouveau Player
	 * 
	 * @param \Games\KillerBundle\Entity\Player $player
	 */
	public function addPlayer(\Games\KillerBundle\Entity\Player $player)	{
	    $this->players[] = $player;
	}
	
	/**
	 * Enlever un Player existant 
	 * 
	 * @param \Games\KillerBundle\Entity\Player $player
	 */
	public function removePlayer(\Games\KillerBundle\Entity\Player $player)	{
	    $this->players->removeElement($player);
	}
	
}
