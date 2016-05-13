<?php 

namespace Application\Entity;

use Zend\Form\Annotation;
use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 * Beschreibt eine spezifische Aufgabe. Diese kann einem Angestellten zugewiesen werden
 * 
 * @ORM\Entity
 * @ORM\Table(name="task")
 */
class Task {
	
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
	 * Verwendete Technologie(n)
	 * 
	 * @ORM\Column(nullable=false)
	 */
	protected $technology;	
	
	/**
	 * Beschreibung
	 * 
	 * @ORM\Column(nullable=false)
	 */
	protected $desc;
	
	/**
	 * Welcher Benutzer hat diese Aufgabe erstellt?
	 *
	 * @ORM\Column(nullable=true)
	 */
	protected $creator;
	
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
	 * Wer hat diese Aufgabe zuletzt bearbeitet?
	 * 
	 * @ORM\Column(nullable=false)
	 * 
	 */
	protected $changedBy;
	
}