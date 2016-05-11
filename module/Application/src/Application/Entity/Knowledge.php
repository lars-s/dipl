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
class Knowledge {
	
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
	 * Beschreibt zu welcher Firma das Wissen gehört; zu einer many2one-relationship ausbauen
	 * 
	 * @ORM\Column(nullable=true)
	 */
	protected $company;

	/**
	 * Weist das Wissen einer Technologie zu; zu einer many2many-relationship ausbauen
	 * Bezieht sich vorerst ausschließlich auf Programmier- und Skriptsprachen
	 * 
	 * @ORM\Column()
	 */
	protected $technology;
	
	/**
	 * Beschreibung des Wissens
	 * 
	 * @ORM\Column(nullable=false)
	 */
	protected $content;
	
	/**
	 * Hier sind alle zugewiesenen Tags
	 * 
	 * @ORM\OneToMany(targetEntity="Tag", mappedBy="appliesTo")
	 * 
	 */
	protected $tags;

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
	
	public function __construct() {
		$this->created = new \DateTime("now");
		$this->updated = new \DateTime("now");
		$this->tags = new ArrayCollection();
	}
	/**
	 * @return the $company
	 */
	public function getCompany() {
		return $this->company;
	}

	/**
	 * @param field_type $company
	 */
	public function setCompany($company) {
		$this->company = $company;
	}

	/**
	 * @return the $technology
	 */
	public function getTechnology() {
		return $this->technology;
	}

	/**
	 * @param field_type $technology
	 */
	public function setTechnology($technology) {
		$this->technology = $technology;
	}

	/**
	 * @return the $content
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param field_type $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * @return the $tags
	 */
	public function getTags() {
		return $this->tags;
	}

	/**
	 * @param \Doctrine\Common\Collections\ArrayCollection $tags
	 */
	public function setTags($tags) {
		$this->tags = $tags;
	}

	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}
		
}
Class KnowledgeRepository extends EntityRepository {
	public function getMostRecentElements($amount = 3) {
		$returnValue = array();
		
		$q = $this->_em->createQuery('SELECT k FROM \Application\Entity\Knowledge k 
				ORDER BY k.id DESC');
		$foo = $q->getResult();
		
		for ($i = 0; $i < $amount; $i++) {
			$returnValue[] = $foo[$i];	
		}
		
		return $returnValue;
	}
}
?>