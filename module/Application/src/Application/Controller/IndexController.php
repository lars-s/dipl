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
use Zend\View\Model\JsonModel;

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
    	
    	$recentPosts = $user->getFavorites();
    	
    	$openTasks = $em->getRepository('\Application\Entity\Task')->getNumberOfOpenTasks();
    	
    	$myOpenTasks = $em->getRepository('\Application\Entity\User')->getOpenTasksCount($user->getId());
    	
    	$myPendingTasks = $em->getRepository('\Application\Entity\User')->getPendingTasksCount($user->getId());

    	$myReviewTasks = $em->getRepository('\Application\Entity\User')->getReviewTasksCount($user->getId());
    	 
    	$isProjectManager = $user->getLevel();
    	
        return new ViewModel(["currentPosts" => $count, "preview" => $recentPosts, "myPendingTasks" => $myPendingTasks,
			"openTasks" => $openTasks, "myOpenTasks" => $myOpenTasks, "myReviewTasks" => $myReviewTasks,
        		 "projectManager" => $isProjectManager ]);
    }
    
    public function itemAction() 
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	$values = array();
    	$user = $em->find('\Application\Entity\User', $_SESSION["userId"]);
    	
    	if (isset($this->getRequest()->getPost()->favorite)) {
    		$status = $this->getRequest()->getPost()->favorite;
    		if ( $em->getRepository('Application\Entity\Knowledge')->find($this->params()->fromRoute('id')) ) {
    			$item = $em->getRepository('Application\Entity\Knowledge')->find($this->params()->fromRoute('id'));
    		} else {
    			$item = $em->getRepository('Application\Entity\Task')->find($this->params()->fromRoute('id'));
    		}
    		if ($status == "true") {
    			$user->removeFavorites($item);
    			$s = "Favorit entfernt";
    		} else {
    			$user->addFavorites($item);
    			$s = "Favorit hinzugefügt";
    		}
    		
    		$em->flush();
    		
    		return new JsonModel(["success" => $s]);
    	}
    	
    	
    	if (strpos($this->getRequest()->getHeader('referer'), "/results")) {
    		$values["ref"] = "true";
    	}
    	
    	$values["user"] = $user;

    	if ($this->getRequest()->isPost())
    	{
    		$solution = $this->getRequest()->getPost()->solution;
    		$task = $em->getRepository('Application\Entity\Task')->findOneBy(array('id' => $this->params()->fromRoute('id')));
    		
    		// Kommt von Projektmanager: Lösung akzeptieren oder ablehnen
    		if ($this->getRequest()->getPost()->status) {
    			if ($this->getRequest()->getPost()->status == "decline") {
    				$task->setStatus(0);
    				$task->setProblems($this->getRequest()->getPost()->problems);
    				$task->setProblemsDate(new \DateTime("now"));
    			} else {
    				$task->setStatus(1);
    			}
    			$em->flush();
    			return $this->redirect()->toRoute('my-open-tasks');
    		}
    		
    		if ($this->getRequest()->getPost()->useExistingSolution) {
    			$solId = $this->getRequest()->getPost()->solution;
    			$solItem = $em->getRepository('Application\Entity\Task')->find($solId);
    			
    			if ($solItem) {
    				$solution = $solItem->getSolution();
    			} else {
    				$solItem = $em->getRepository('Application\Entity\Knowledge')->find($solId);
    				$solution = $solItem->getContent();
    			}
    		}
    		
    		if ($solution !== "") 
    		{
    			$task->setSolution($solution);
    			$task->setStatus(2);
    			$task->setSolutionDate(new \DateTime("now"));
    			$em->flush();
    			return $this->redirect()->toRoute('my-open-tasks');
    		}
    	}
    	
    	if ( $em->getRepository('Application\Entity\Knowledge')->find($this->params()->fromRoute('id')) ) {
    		$item = $em->getRepository('Application\Entity\Knowledge')->find($this->params()->fromRoute('id'));

    		$values["item"] = $item;
    	} else {
    		// DIES IST EIN TASK!
    		$item = $em->getRepository('Application\Entity\Task')->find($this->params()->fromRoute('id'));
    		$values["item"] = $item;

			$tags = array();
    		foreach ($item->getTags() as $tagText)
			{
				$tagText = trim($tagText);
				if ($tagText !== "") {
					$tags[] = $tagText;
				}
			}	
    		if ($item->getStatus() == 0 && $item->getAssignee() == $user ) {
	    		$_SESSION["openTasks"] = true;    	
    		}
    		
    		if ($item->getStatus() == 2 && $item->getAuthor() == $user ) {
	    		$values["reviewItem"] = "blaaa";
	    		$_SESSION["openTasks"] = true;    	
    		}
    		
    		if ($item->getProblems() !== null) {
    			$values["problems"] = $item->getProblems();
    		}
    		
			if ($item->getStatus() > 0) {
				$values["solution"] = $item->getSolution();
				
			} else {
				$values["reccs"] = $em->getRepository('Application\Entity\Task')->getRecommendations(
						$item->getTechnology(),
						$item->getCompany(),
						$tags
				);
	    		usort($values["reccs"], function($a, $b) {
	    			return $b["score"] - $a["score"];
	    		});
			}
    	}

    	return new ViewModel($values);
    }
    
    public function addItemAction() 
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		$user = $em->find('\Application\Entity\User', $_SESSION["userId"]);
    	
		$ai = null;
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
			$ai = $item;

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
    			"technologies" => $allTechnologies, "tags" => $allTags, "addedItem" => $ai ));
    }    
    
    public function addTaskAction() 
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	$ae = null;
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
				"status" => 0
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
			$ae = $tag;
    	}
    	
    	$allEmployees = $em->getRepository('\Application\Entity\User')->findBy(["level" => "0"], ['lastname' => 'ASC']);
    	$allCompanies = $em->getRepository('\Application\Entity\Company')->findBy([], ['name' => 'ASC']);
    	$allTechnologies = $em->getRepository('\Application\Entity\Technology')->findBy([], ['name' => 'ASC']);
    	$allTags = $em->getRepository('\Application\Entity\Tag')->getNamesAndIds();

    	    	
    	return new ViewModel(array("assignees" => $allEmployees, "companies" => $allCompanies, 
    			"technologies" => $allTechnologies, "tags" => $allTags, "addedElement" => $ae ));
    }
    
    public function resultsAction() 
    {
    	if ($this->getRequest()->isPost())
    	{
    		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    		$query = $this->getRequest()->getPost()->query;
    		
    		$_SESSION["searchQuery"] = $query;
    		
    		$return = array("query" => $query);

    		// IS TECHNOLOGY?
    		$result = $em->getRepository('\Application\Entity\Technology')->findOneBy(["name" => $query]);
    		if ($result) {
    			$return["technology"] = $result;
    			$return["technologyItems"] = $result->getAppliesTo();
    		} 
    		
    		// IS TAG?
    		$results = $em->getRepository('\Application\Entity\Tag')->findOneBy(["name" => $query]);
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
    	 

    	if ($user->getLevel() == 1) {
    		$tasks = $em->getRepository('\Application\Entity\User')->getReviewTasks($user->getId());
    		
    	} else {    	 
    		$tasks = $em->getRepository('\Application\Entity\User')->getOpenTasks($user->getId());
    	}
    	
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
    
    public function getRecommendationAssigneeAction() {    	
    	if ($this->getRequest()->isPost())
    	{    		
    		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    		$query = $this->getRequest()->getPost();
    		    		
    		$company = $em->getRepository('\Application\Entity\Company')->findOneBy(array("name" => $query["company"]));
    		$technology = $em->getRepository('\Application\Entity\Technology')->findOneBy(array("name" => $query["technology"]));
    		$t = explode(",", strtolower($query["tags"]));
			$tags = array();
    		foreach ($t as $tagText)
			{
				$tagText = trim($tagText);
				if ($tagText !== "") {
					$tags[] = $tagText;
				}
			}
    		
    		$return = $em->getRepository('\Application\Entity\User')->getRecommendationForAssignment(
    			$technology,
    			$company,
    			$tags
    		);
    		
    		usort($return, function($a, $b) {
				return $b["score"] - $a["score"];
    		});
    		
    		return new JsonModel($return);

    	} else {
    		return $this->redirect()->toRoute('home');
    	}
    }
}
