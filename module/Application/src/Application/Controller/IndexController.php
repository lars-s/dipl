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
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

    	$count = $em->getRepository('\Application\Entity\Knowledge')->getTotalNumberOfElements();
    	
    	$recentPosts = $em->getRepository('\Application\Entity\Knowledge')->getMostRecentElements(3);
    	
        return new ViewModel(["currentPosts" => $count, "preview" => $recentPosts]);
    }
    
    public function itemAction() 
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	
    	$item = $em->getRepository('\Application\Entity\Knowledge')->findOneBy(array('id' => $this->params()->fromRoute('id')));
    	
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
			
			$item = $hydrator->hydrate($data, $item);
			
			$em->persist($item);
			$em->flush();
    	}
    	    	
    	return new ViewModel();
    }
    
    public function resultsAction() 
    {
    	return new ViewModel();
    }
}
