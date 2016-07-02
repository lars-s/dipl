<?php 

namespace Application\Entity;

use Zend\Form\Annotation;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

/**
 * KnowledgeSuperclass
 * 
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE") 
 * @ORM\Table(name="knowledgesuperclass")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"knowledgeSuperclass" = "KnowledgeSuperclass", "knowledge" = "Knowledge", "task" = "Task"})
 * @ORM\Entity(repositoryClass="KnowledgeRepository")
 */
class KnowledgeSuperclass {
	
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
	 * @ORM\ManyToOne(targetEntity="Company") 
	 * @ORM\JoinColumn(name="company", referencedColumnName="id")
	 */
	protected $company;

	/**
	 * @ORM\ManyToOne(targetEntity="Technology")
	 * @ORM\JoinColumn(name="technology", referencedColumnName="id")
	 */
	protected $technology;

	/**
	 * 0 = offene Aufgabe, sonstwie ungültiges Wissen
	 * 1 = erledigte Aufgabe, gültiges Wissen
	 * 2 = Task zur review durch Projektmanager
	 * @ORM\Column(nullable=false)
	 */
	protected $status = 1;
	
	/**
	 * @ORM\ManyToMany(targetEntity="Tag", inversedBy="appliesTo")
	 * @ORM\JoinTable(name="tagged_elements") 
	 */
	protected $tags;

	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="contributions")
	 * @ORM\JoinColumn(name="author", referencedColumnName="id")
	 */
	protected $author;
	
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
	
	public function toString() {
		return "{$this->id}";
	}
	
	/**
	 * @return the $company
	 */
	public function getCompany() {
		if ($this->company) {
			return $this->company;
		} else {
			return new Company();
		}
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
		if ($this->technology) {
			return $this->technology;
		} else {
			return new Technology();
		}
	}

	/**
	 * @param field_type $technology
	 */
	public function setTechnology($technology) {
		$this->technology = $technology;
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
	/**
	 * @return the $created
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @return the $updated
	 */
	public function getUpdated() {
		return $this->updated;
	}
	public function getAuthor() {
		return $this->author;
	}	
	
	public function getStatus() {
		return $this->status;
	}

	public function setStatus($status) {
		$this->status = $status;
	}

	public function setAuthor($author) {
		$this->author = $author;
	}

	public function hasTags($tags) {
		return array_intersect($this->tags->toArray(), $tags);
	}
}

?>