<?php

namespace Games\KillerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\Validator\Constraints as Assert;


/**
 * Killer
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sdz\BlogBundle\Entity\KillerRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Killer {
	/**
	 *
	 * @var integer @ORM\Column(name="id", type="integer")
	 *      @ORM\Id
	 *      @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Games\UserBundle\Entity\User", inversedBy="myKillers")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $userAdmin;
	
	/**
	 *
	 * @var string @ORM\Column(name="name", type="string", length=255)
	 */
	private $name;
	
	/**
	 * @ORM\Column(name="nbParticipants", type="integer")
	 */
	private $nbParticipants;
	
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
	
	
	
	
	
	// Et modifions le constructeur pour mettre cet attribut publication à true par défaut
	public function __construct()
	{
	    $this->nbParticipants = 0;
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
	 * @param Sdz\BlogBundle\Entity\User $userAdmin
	 */
	public function setUser(\Games\UserBundle\Entity\User $userAdmin)
	{
	    $this->userAdmin = $userAdmin;
	}
	
	/**
	 * Get userAdmin
	 *
	 * @return Sdz\BlogBundle\Entity\User
	 */
	public function getUserAdmin()
	{
	    return $this->userAdmin;
	}
	
	/**
	 * Get nbParticipants
	 *
	 * @return Sdz\BlogBundle\Entity\User
	 */
	public function getNbParticipants()
	{
	    return $this->nbParticipants;
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
	public function getdDateBegin() {
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
	public function getdDateEnd() {
	    return $this->dateEnd;
	}
	
}
