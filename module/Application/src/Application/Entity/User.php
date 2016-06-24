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
	 * 0 = Entwickler
	 * 1 = Projektmanager
	 * @ORM\Column(name="level", type="smallint")
	 */
	protected $level;
	
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
	public function getLevel() {
		return $this->level;
	}

	public function setLevel($level) {
		$this->level = $level;
	}

}

Class UserRepository extends EntityRepository 
{
	public function getOpenTasks($user) {
		$q = $this->_em->createQuery('SELECT t FROM \Application\Entity\Task t WHERE t.status = 0
				AND t.assignee = :var')
		->setParameter("var", "$user");
		return $q->getResult();
	}
	
	public function getOpenTasksCount($user) {
		$q = $this->_em->createQuery('SELECT count(t) FROM \Application\Entity\Task t WHERE t.status = 0
				AND t.assignee = :var')
		->setParameter("var", "$user");
		return $q->getSingleScalarResult();
	}

	public function getRecommendationForAssignment($tech, $comp, $tags) {
		$q = $this->_em->createQuery('SELECT u FROM \Application\Entity\User u WHERE u.level = 0');
		$users = $q->getResult();
				
		$return = array();
		foreach ($users as $u) {
			
			$foo["score"] = 0;
			$foo["fullname"] = $u->getFullname();
			$foo["id"] = $u->getId();
			
			$q = $this->_em->createQuery('SELECT k FROM \Application\Entity\Knowledge k WHERE k.status = 1 AND k.author = :var')
				->setParameter("var", $u->getId());
			$allKnowledge = $q->getResult();
			foreach ($allKnowledge as $k) {
				if ($k->getTechnology() == $tech && $k->getCompany() == $comp && $k->hasTags($tags)) {
					$foo["score"] += 5;
					
				} else if (	$k->getTechnology() == $tech && $k->getCompany() == $comp 
							|| $k->getTechnology() == $tech && $k->hasTags($tags)
							|| $k->getCompany() == $comp && $k->hasTags($tags)) {
					$foo["score"] += 3;
					
				} else if ($k->getTechnology() == $tech || $k->getCompany() == $comp || $k->hasTags($tags)) {
					$foo["score"] += 1;
				}
			}
			
			$q = $this->_em->createQuery('SELECT t FROM \Application\Entity\Task t WHERE t.status = 1 AND t.assignee = :var')
			->setParameter("var", $u->getId());
			$allFinishedTasks = $q->getResult();
			foreach ($allFinishedTasks as $t) {
				if ($t->getTechnology() == $tech && $t->getCompany() == $comp && $t->hasTags($tags)) {
					$foo["score"] += 5;
					
				} else if (	$t->getTechnology() == $tech && $t->getCompany() == $comp 
							|| $t->getTechnology() == $tech && $t->hasTags($tags)
							|| $t->getCompany() == $comp && $t->hasTags($tags)) {
					$foo["score"] += 3;
					
				} else if ($t->getTechnology() == $tech || $t->getCompany() == $comp || $t->hasTags($tags)) {
					$foo["score"] += 1;
				}
			}
			if ($foo["score"] > 0) {
				$return[] = $foo;
			}
				
		}
	
		return $return;
	}
}