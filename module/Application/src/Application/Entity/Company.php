<?php 

namespace Application\Entity;

use Zend\Form\Annotation;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Company
 * 
 * @ORM\Entity
 * @ORM\Table(name="company")
 */
class Company {
	
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
	 * @ORM\Column(nullable=false)
	 */
	protected $name;

// 	/**
// 	 * @ORM\Column(nullable=true)
// 	 */
// 	protected $description;
	
	/**
	 *  @ORM\ManyToMany(targetEntity="KnowledgeSuperclass", mappedBy="companies")
	 */
	protected $appliesTo;
	
	public function __construct() {
		$this->appliesTo = new ArrayCollection();
	}

	public function __toString() {
		return $this->name;
	}
	
	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

// 	/**
// 	 * @return the $description
// 	 */
// 	public function getDescription() {
// 		return $this->description;
// 	}

// 	/**
// 	 * @param field_type $description
// 	 */
// 	public function setDescription($description) {
// 		$this->description = $description;
// 	}

	public function getAppliesTo() {
		return $this->appliesTo;
	}

	public function setAppliesTo($appliesTo) {
		$this->appliesTo = $appliesTo;
	}

	public function getId() {
		return $this->id;
	}
}