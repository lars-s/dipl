<?php 

namespace Application\Entity;

use Zend\Form\Annotation;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

/**
 * Knowledge
 *
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="KnowledgeRepository")
 * @ORM\Table(name="knowledge")
 */
class Knowledge extends KnowledgeSuperclass {

	/**
	 * Beschreibung des Wissens
	 *
	 * @ORM\Column(nullable=false, length=50000)
	 */
	protected $content;

	public function getContent() {
		return $this->content;
	}
	public function getdDescription() {
		return $this->content;
	}
	

	public function setContent($content) {
		$this->content = $content;
	}
}
Class KnowledgeRepository extends EntityRepository {
	public function getMostRecentElements($amount = 3) {
		$returnValue = array();

		$q = $this->_em->createQuery('SELECT k FROM \Application\Entity\Knowledge k
				ORDER BY k.id DESC');
		$foo = $q->getResult();
		
		$amount = min($amount, $this->getTotalNumberOfElements());
		
		for ($i = 0; $i < $amount; $i++) {
			$returnValue[] = $foo[$i];	
		}

		return $returnValue;
	}

	public function getNumberOfKnowledge()
	{
		$q = $this->_em->createQuery('SELECT COUNT(k) FROM \Application\Entity\Knowledge k');
		$count = $q->getSingleScalarResult();

		return $count;
	}
	
	public function getTotalNumberOfElements() {
		$q = $this->_em->createQuery('SELECT COUNT(k) FROM \Application\Entity\Knowledge k');
		return $q->getSingleScalarResult();
	}
}

?>
