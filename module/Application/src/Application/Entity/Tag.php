<?php 

namespace Application\Entity;

use Zend\Form\Annotation;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

/**
 * Tag
 * 
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="TagRepository")
 * @ORM\Table(name="tag")
 */
class Tag {
	
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
	 * Name des Tags 
	 * 
	 * @ORM\Column(nullable=false)
	 */
	protected $name;

// 	/**
// 	 * Beschreibung des Tags, optional
// 	 *
// 	 * @ORM\Column(nullable=true)
// 	 */
// 	protected $description;
	
	/**
	 *  @ORM\ManyToMany(targetEntity="KnowledgeSuperclass", mappedBy="tags")
	 */
	protected $appliesTo;
	
	public function __construct() {
		$this->appliesTo = new ArrayCollection();
	}
	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param field_type $name
	 */
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

	/**
	 * @return the $appliesTo
	 */
	public function getAppliesTo() {
		return $this->appliesTo;
	}

	/**
	 * @param field_type $appliesTo
	 */
	public function setAppliesTo($appliesTo) {
		$this->appliesTo = $appliesTo;
	}

	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}
}

Class TagRepository extends EntityRepository 
{
	public function getNamesAndIds() {
		$query ='SELECT partial c.{id, name} FROM Application\Entity\Tag c ORDER BY c.name ASC';
	
		return $this->_em->createQuery($query)->getResult();
	}
}