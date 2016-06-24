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
	 * @ORM\Column(nullable=false, length=50000)
	 */
	protected $description;		
	
	/**
	 * @ORM\Column(nullable=true, length=2000)
	 */
	protected $solution;
	
	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="assignee", referencedColumnName="id")
	 */
	protected $assignee;

	public function getContent() {
		return $this->description;
	}
	
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

	public function getStatusText() {
		if ($this->status == 0) {
			return "offen";
		} else if ($this->status == 2) {
			return "erwartet Bestägigung";
		}
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
		$items = array_merge($q->getResult(), $items);
		
		$return = array();
		foreach ($items as $k) {
				
			$foo["score"] = 0;
			$foo["id"] = $k->getId();
				
			if ($k->getTechnology() == $tech && $k->getCompany() == $comp && $k->hasTags($tags)) {
				$foo["score"] += count($k->hasTags($tags)) + 5;
					
			} else if (	$k->getTechnology() == $tech && $k->getCompany() == $comp
					|| $k->getTechnology() == $tech && $k->hasTags($tags)
					|| $k->getCompany() == $comp && $k->hasTags($tags)) {
				$foo["score"] += count($k->hasTags($tags)) + 3;
					
			} else if ($k->getTechnology() == $tech || $k->getCompany() == $comp || $k->hasTags($tags)) {
				$foo["score"] += count($k->hasTags($tags)) + 1;
			}

			if ($foo["score"] > 0) {
				if (method_exists($k, "getSolution")) {
					$foo["description"] = $k->getSolution();
				} else {
					$foo["description"] = $k->getContent();
				}
				
				$duplicate = false;
				foreach ($return as $key => $e) {
					similar_text($e["description"], $foo["description"], $v);
					if ($v > 90) {
						$return[$key]["score"] = (intval($foo["score"]) + intval($e["score"]));
						$duplicate = true;
					}
				}
				if (!$duplicate) {
					$return[] = $foo;
				}
			}
		}
		
		return $return;
	}
}
?>