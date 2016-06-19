<?php 

namespace Application\Entity;

use Zend\Form\Annotation;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

/**
 * Task
 * Beschreibt eine spezifische Aufgabe. Diese kann einem Angestellten zugewiesen werden
 *
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="TaskRepository")
 * @ORM\Table(name="task")
 */
class Task extends KnowledgeSuperclass {
	/**
	 * @ORM\Column(nullable=false)
	 */
	protected $description;		
	
	/**
	 * @ORM\Column(nullable=true)
	 */
	protected $solution;
	
	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="assignee", referencedColumnName="id")
	 */
	protected $assignee;

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getAssignee() {
		return $this->assignee;
	}

	public function setAssignee($assignee) {
		$this->assignee = $assignee;
	}
	public function getSolution() {
		return $this->solution;
	}

	public function setSolution($solution) {
		$this->solution = $solution;
	}


}

Class TaskRepository extends EntityRepository {
	public function getNumberOfOpenTasks() {
		$q = $this->_em->createQuery('SELECT COUNT(t) FROM \Application\Entity\Task t WHERE t.status LIKE :status
			ORDER BY t.id DESC')->setParameter("status", "1");
		$count = $q->getSingleScalarResult();

		return $count;
	}
	
	public function getRecommendations($tech, $comp, $tags) {
		$q = $this->_em->createQuery('SELECT k FROM \Application\Entity\Knowledge k WHERE k.status = 1');
		$items = $q->getResult();
		
		$q = $this->_em->createQuery('SELECT t FROM \Application\Entity\Task t WHERE t.status = 1');
		$items += $q->getResult();
		
		$return = array();
		foreach ($items as $k) {
				
			$foo["score"] = 0;
			$foo["id"] = $k->getId();
				
			if ($k->getTechnology() == $tech && $k->getCompany() == $comp && $k->hasTags($tags)) {
				$foo["score"] += 5;
					
			} else if (	$k->getTechnology() == $tech && $k->getCompany() == $comp
					|| $k->getTechnology() == $tech && $k->hasTags($tags)
					|| $k->getCompany() == $comp && $k->hasTags($tags)) {
				$foo["score"] += 3;
					
			} else if ($k->getTechnology() == $tech || $k->getCompany() == $comp || $k->hasTags($tags)) {
				$foo["score"] += 1;
			}

			if ($foo["score"] > 0) {
				$foo["description"] = $k->getContent();
				$return[] = $foo;
			}
		}
		
		return $return;
	}
}
?>