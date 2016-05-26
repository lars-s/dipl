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
	 * @ORM\ManyToMany(targetEntity="Company", inversedBy="appliesTo")
	 * @ORM\JoinTable(name="company_elements") 
	 */
	protected $company;

	/**
	 * @ORM\ManyToMany(targetEntity="Technology", inversedBy="appliesTo")
	 * @ORM\JoinTable(name="tech_elements") 
	 */
	protected $technology;
	
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

		
}

?>