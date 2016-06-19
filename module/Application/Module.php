<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {    	
    	session_start();
    	$e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
    		$controller = $e->getTarget();
    		$target = $e->getApplication()->getServiceManager()->get('Application')->getMvcEvent()->getRouteMatch()->getMatchedRouteName();
    		
    		if (!isset($_SESSION["user"]) && ($target != "login") ) {
    			$controller->plugin('redirect')->toRoute('login');
    		}
    	}, 100);
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $application  = $e->getTarget();
        $sm = $application->getServiceManager();
        $em = $sm->get('doctrine.entitymanager.orm_default');
        
        if ( 0 ) {
	        $classes = array(
	        	$em->getClassMetadata('Application\Entity\Company'),
	        	$em->getClassMetadata('Application\Entity\Knowledge'),
        		$em->getClassMetadata('Application\Entity\Task'),
        		$em->getClassMetadata('Application\Entity\Tag'),
        		$em->getClassMetadata('Application\Entity\Technology'),
        		$em->getClassMetadata('Application\Entity\User'),
	        	$em->getClassMetadata('Application\Entity\KnowledgeSuperclass')
	        );
	        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
	        $tool->updateSchema($classes);
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
