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
	 * @ORM\Column(nullable=false)
	 */
	protected $status;

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}
	public function getStatus() {
		return $this->status;
	}

	public function setStatus($status) {
		$this->status = $status;
	}

}

Class TaskRepository extends EntityRepository {
	public function getNumberOfOpenTasks() {
		$q = $this->_em->createQuery('SELECT COUNT(t) FROM \Application\Entity\Task t WHERE t.status LIKE :status
			ORDER BY t.id DESC')->setParameter("status", "open");
		$count = $q->getSingleScalarResult();

		return $count;
	}
}
?>