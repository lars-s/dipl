<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\Knowledge;
use Application\Entity\Tag;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Application\Entity\Task;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	if ($this->getRequest()->isPost()) 
    	{
    		$id = $this->getRequest()->getPost()["id"];
    		
    		$user = $em->find('\Application\Entity\User', $id);
    		
    		$_SESSION["userId"] = $user->getId();
    		$_SESSION["user"] = $user->getFirstname();
    		
    		return $this->redirect()->toRoute('home');
    	} else {
    		$user = $em->find('\Application\Entity\User', $_SESSION["userId"]);
    	}
    	
    	$count = $em->getRepository('Application\Entity\Knowledge')->getNumberOfKnowledge();

    	$count = $em->getRepository('\Application\Entity\Knowledge')->getTotalNumberOfElements();
    	
    	$recentPosts = $em->getRepository('\Application\Entity\Knowledge')->getMostRecentElements(3);
    	
    	$openTasks = $em->getRepository('\Application\Entity\Task')->getNumberOfOpenTasks();
    	
    	$myOpenTasks = $em->getRepository('\Application\Entity\User')->getOpenTasksCount($user->getId());
    	
        return new ViewModel(["currentPosts" => $count, "preview" => $recentPosts,
			"openTasks" => $openTasks, "myOpenTasks" => $myOpenTasks ]);
    }
    
    public function itemAction() 
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	
    	if ($this->getRequest()->isPost())
    	{
    		$ferrors = array();
    		$solution = $this->getRequest()->getPost()->solution;
    		$task = $em->getRepository('Application\Entity\Task')->findOneBy(array('id' => $this->params()->fromRoute('id')));
    		
    		if ($solution !== "") 
    		{
    			$task->setSolution($solution);
    			$task->setStatus(0);
    			$em->flush();
    			return $this->redirect()->toRoute('my-open-tasks');
    		} else {
    			$ferrors[] = "Leerer Lösungstext!";
    		}
    	}
    	
    	$item = $em->getRepository('Application\Entity\Knowledge')->findOneBy(array('id' => $this->params()->fromRoute('id'))) ?
    		$em->getRepository('Application\Entity\Knowledge')->findOneBy(array('id' => $this->params()->fromRoute('id'))):
    		$em->getRepository('Application\Entity\Task')->findOneBy(array('id' => $this->params()->fromRoute('id'))) ;
    	
    	return new ViewModel(["item" => $item, "formErrors" => $ferrors]);
    }
    
    public function addItemAction() 
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		$user = $em->find('\Application\Entity\User', $_SESSION["userId"]);
    	
    	if ($this->getRequest()->isPost()) 
    	{
    		$hydrator = new DoctrineHydrator($em);
			$item = new Knowledge();
			$info = $this->getRequest()->getPost();
			$data = array(
				"content" => $info->content,
				"technology" => $em->find('\Application\Entity\Technology', $info->technology),
				"company" => $em->find('\Application\Entity\Company', $info->company)
			);
			
			$item = $hydrator->hydrate($data, $item);
			$item->setAuthor($user);
			
			$em->persist($item);

			$tags = explode(",", strtolower($info->tags));
			foreach ($tags as $tagText)
			{
				$tagText = trim($tagText);
				$existingTag = $em->getRepository('\Application\Entity\Tag')->findOneBy(['name' => $tagText]);
				
				if (!$existingTag) {
					$tag = new Tag();
						
					$tag->setName($tagText);
				} else {
					$tag = $existingTag;
				}

				$tag->getAppliesTo()->add($item);
				$item->getTags()->add($tag);
					
				$em->persist($tag);
			}				
			
			$em->flush();
    	}

    	$allEmployees = $em->getRepository('\Application\Entity\User')->findBy([], ['lastname' => 'ASC']);
    	$allCompanies = $em->getRepository('\Application\Entity\Company')->findBy([], ['name' => 'ASC']);
    	$allTechnologies = $em->getRepository('\Application\Entity\Technology')->findBy([], ['name' => 'ASC']);
    	$allTags = $em->getRepository('\Application\Entity\Tag')->getNamesAndIds();
    	
    	return new ViewModel(array("assignees" => $allEmployees, "companies" => $allCompanies,
    			"technologies" => $allTechnologies, "tags" => $allTags ));
    }    
    
    public function addTaskAction() 
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	
    	if ($this->getRequest()->isPost()) 
    	{
			$info = $this->getRequest()->getPost();
			
    		$hydrator = new DoctrineHydrator($em);
			$item = new Task();
			$assignee = $em->find('\Application\Entity\User', $info->assignee);
			
			$data = array(
				"description" => $info->content,
				"technology" => $em->find('\Application\Entity\Technology', $info->technology),
				"company" => $em->find('\Application\Entity\Company', $info->company),
				"author" => $em->find('\Application\Entity\User', $_SESSION["userId"]),
				"status" => 1
			);
			
			$item = $hydrator->hydrate($data, $item);
			$item->setAssignee($assignee);

			$tags = explode(",", strtolower($info->tags));
						
    		foreach ($tags as $tagText)
			{
				$tagText = trim($tagText);
				$existingTag = $em->getRepository('\Application\Entity\Tag')->findOneBy(['name' => $tagText]);
				
				if (!$existingTag) {
					$tag = new Tag();
						
					$tag->setName($tagText);
				} else {
					$tag = $existingTag;
				}

				$tag->getAppliesTo()->add($item);
				$item->getTags()->add($tag);
					
				$em->persist($tag);
			}	
			
			$assignee->getAssignedTasks()->add($item);
			
			$em->persist($item);
			$em->flush();
    	}
    	
    	$allEmployees = $em->getRepository('\Application\Entity\User')->findBy([], ['lastname' => 'ASC']);
    	$allCompanies = $em->getRepository('\Application\Entity\Company')->findBy([], ['name' => 'ASC']);
    	$allTechnologies = $em->getRepository('\Application\Entity\Technology')->findBy([], ['name' => 'ASC']);
    	$allTags = $em->getRepository('\Application\Entity\Tag')->getNamesAndIds();
    	    	
    	return new ViewModel(array("assignees" => $allEmployees, "companies" => $allCompanies, 
    			"technologies" => $allTechnologies, "tags" => $allTags ));
    }
    
    public function resultsAction() 
    {
    	if ($this->getRequest()->isPost())
    	{
    		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    		$query = $this->getRequest()->getPost()->query;
    		$return = array("query" => $query);

    		
    		// IS TECHNOLOGY?
    		$result = $em->getRepository('\Application\Entity\Technology')->findOneBy(["name" => $query]);
    		if ($result) {
    			$return["technology"] = $result;
    			$return["technologyItems"] = $result->getAppliesTo();
    		} 
    		
    		// IS TAG?
    		$results = $em->getRepository('\Application\Entity\Tag')->findBy(["name" => $query]);
    	    if ($results) {
    			$return["tag"] = $results;
    			$return["tagItems"] = $results->getAppliesTo();
    		} 
    		
    		// IS COMPANY?
    		
    		
    		return new ViewModel($return);
    	} else {
    		return $this->redirect()->toRoute('home');
    	}
    }

    public function myOpenTasksAction()
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	$user = $em->find('\Application\Entity\User', $_SESSION["userId"]);
    	 
    	$tasks = $em->getRepository('\Application\Entity\User')->getOpenTasks($user->getId());
    	
    	return new ViewModel(["myTasks" => $tasks]);
    }
    
    public function loginAction() 
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	$q = $em->createQuery('SELECT u from \Application\Entity\User u');
    	$users_objects = $q->getResult();
    	
    	foreach ($users_objects as $user) 
    	{
    		$users[$user->getId()] = $user->getFirstname() . " " . $user->getLastname();
    	}
    	
    	return new ViewModel(array("users" => $users));
    }
    
    public function logoutAction()
    {
    	session_destroy();
    	return $this->redirect()->toRoute('login');
    }
}
