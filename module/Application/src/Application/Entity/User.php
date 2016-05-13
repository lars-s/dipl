<?php 

namespace Application\Entity;

use Zend\Form\Annotation;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 * Beschreibt einen Angestellten
 * 
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User {
	
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
	 * Vorname
	 * 
	 * @ORM\Column(nullable=true)
	 */
	protected $firstname;	
	
	/**
	 * Nachname
	 * 
	 * @ORM\Column(nullable=true)
	 */
	protected $lastname;
	
	/**
	 * Zugewiesene, unerledigte Aufgaben
	 *
	 * @ORM\Column(nullable=true)
	 */
	protected $openTasks;
	
	/**
	 * Erledigte Aufgaben
	 *
	 * @ORM\Column(nullable=true)
	 */
	protected $closedTasks;
	
	/**
	 * 
	 */
	
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
	
}