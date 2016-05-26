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
    	}
    	
    	$count = $em->getRepository('Application\Entity\Knowledge')->getNumberOfKnowledge();

    	$count = $em->getRepository('\Application\Entity\Knowledge')->getTotalNumberOfElements();
    	
    	$recentPosts = $em->getRepository('\Application\Entity\Knowledge')->getMostRecentElements(3);
    	
    	$openTasks = $em->getRepository('\Application\Entity\Task')->getNumberOfOpenTasks();
    	
        return new ViewModel(["currentPosts" => $count, "preview" => $recentPosts,
			"openTasks" => $openTasks]);
    }
    
    public function itemAction() 
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	
    	$item = $em->getRepository('Application\Entity\Knowledge')->findOneBy(array('id' => $this->params()->fromRoute('id')));
    	
    	return new ViewModel(["item" => $item]);
    }
    
    public function addItemAction() 
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	
    	if ($this->getRequest()->isPost()) 
    	{
    		$hydrator = new DoctrineHydrator($em);
			$item = new Knowledge();
			$info = $this->getRequest()->getPost();
			$data = array(
				"content" => $info->content,
				"technology" => $info->technology,
				"company" => $info->company
			);
			
			$tags = explode(" ", $info->tags);
			
			$item = $hydrator->hydrate($data, $item);
			$em->persist($item);
			
			foreach ($tags as $tagText)
			{
				$tag = new Tag();
				
				$tag->setName($tagText);

				$tag->getAppliesTo()->add($item);
				$item->getTags()->add($tag);
				
				$em->persist($tag);
			}

			$em->flush();
    	}
    	    	
    	return new ViewModel();
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
				"content" => $info->content,
				"technology" => $info->technology,
				"company" => $info->company,
				"creator" => "Jeff"
			);
			
			$item = $hydrator->hydrate($data, $item);
			$item->setAssignedTo($assignee);
			
			$assignee->getAssignedTasks()->add($item);
			
			$em->persist($item);
			$em->flush();
    	}
    	
    	$allEmployees = $em->getRepository('\Application\Entity\User')->findAll();
    	    	
    	return new ViewModel(array("assignees" => $allEmployees));
    }
    
    public function resultsAction() 
    {
    	return new ViewModel();
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
