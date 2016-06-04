<?php 

namespace Application\Entity;

use Zend\Form\Annotation;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

/**
 * User
 * Beschreibt einen Angestellten
 * 
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\Table(name="user")
 */
class User {
	
	/**
	 * @var integer
	 * 
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * 
     * @Annotation\Attributes({"type":"hidden"})
     */
	protected $id;	
	
	/**
	 * Vorname
	 * 
	 * @ORM\Column(nullable=true)
	 */
	protected $firstname;	
	
	/**
	 * Nachname
	 * 
	 * @ORM\Column(nullable=true)
	 */
	protected $lastname;
	
	/**
	 * Zugewiesene, unerledigte Aufgaben
	 * @ORM\OneToMany(targetEntity="Task", mappedBy="assignee")
	 */
	protected $assignedTasks;

	/**
	 * @var datetime
	 *
	 * @ORM\Column(name="created", type="datetime", nullable=false)
	 * @Annotation\Exclude()
	 */
	protected $created;
	
	/**
	 * @var datetime
	 *
	 * @ORM\Column(name="updated", type="datetime", nullable=false)
	 * @Annotation\Exclude()
	 */
	protected $updated;	

	/**
	 * @ORM\OneToMany(targetEntity="KnowledgeSuperclass", mappedBy="author")
	 */
	protected $contributions;
	
	public function __construct() {
		$this->created = new \DateTime("now");
		$this->updated = new \DateTime("now");
		$this->assignedTasks = new ArrayCollection();
		$this->contributions = new ArrayCollection();
	}
	
	public function getId() {
		return $this->id;
	}

	public function getFirstname() {
		return $this->firstname;
	}

	public function getLastname() {
		return $this->lastname;
	}

	public function getAssignedTasks() {
		return $this->assignedTasks;
	}

	public function getCreated() {
		return $this->created;
	}

	public function getUpdated() {
		return $this->updated;
	}
	
	public function getContributions() {
		return $this->contributions;
	}
	
	public function setFirstname($firstname) {
		$this->firstname = $firstname;
	}

	public function setLastname($lastname) {
		$this->lastname = $lastname;
	}

	public function setAssignedTasks($assignedTasks) {
		$this->assignedTasks = $assignedTasks;
	}

	public function setContributions($contributions) {
		$this->contributions = $contributions;
	}	
	
	public function getFullname() {
		return $this->firstname . " " . $this->lastname;
	}
}

Class UserRepository extends EntityRepository {

}