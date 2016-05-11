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

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	
    	$know = $em->getRepository('\Application\Entity\Knowledge')->findBy(array('technology' => "javascript"));
    	
        return new ViewModel(["knows" => $know]);
    }
    
    public function itemAction() {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	
    	$item = $em->getRepository('\Application\Entity\Knowledge')->findOneBy(array('id' => $this->params()->fromRoute('id')));
    	
    	return new ViewModel(["item" => $item]);
    }
    
    public function addItemAction() {
    	return new ViewModel();
    }
}
