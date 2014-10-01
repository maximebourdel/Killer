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
     * @var \DateTime
	 *
	 * @ORM\Column(name="dateBegin", type="datetime")
	 * @Assert\DateTime()
	 */
	private $dateBegin;
	
	/**
     * @var \DateTime
	 *
	 * @ORM\Column(name="dateEnd", type="datetime")
	 * @Assert\DateTime()
	 */
	private $dateEnd;
	
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
	 * Set user
	 *
	 * @param Sdz\BlogBundle\Entity\User $user
	 */
	public function setUser(\Games\UserBundle\Entity\User $user)
	{
	    $this->user = $user;
	}
	
	/**
	 * Get user
	 *
	 * @return Sdz\BlogBundle\Entity\User
	 */
	public function getUser()
	{
	    return $this->user;
	}
	
}
