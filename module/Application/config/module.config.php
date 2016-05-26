<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),    	
	        'item' => array(
		        'type' => 'Zend\Mvc\Router\Http\Segment',
		        'options' => array(
		        	'route'    => '/item[/:id]',
			        'defaults' => array(
				        'controller' => 'Application\Controller\Index',
				        'action'     => 'item',
			        ),
		        ),
	        ),    	
	        'add-item' => array(
		        'type' => 'Zend\Mvc\Router\Http\Literal',
		        'options' => array(
		        	'route'    => '/add-item',
			        'defaults' => array(
				        'controller' => 'Application\Controller\Index',
				        'action'     => 'add-item',
			        ),
		        ),
	        ),    	
	        'add-task' => array(
		        'type' => 'Zend\Mvc\Router\Http\Literal',
		        'options' => array(
		        	'route'    => '/add-task',
			        'defaults' => array(
				        'controller' => 'Application\Controller\Index',
				        'action'     => 'add-task',
			        ),
		        ),
	        ),      	
	        'results' => array(
		        'type' => 'Zend\Mvc\Router\Http\Segment',
		        'options' => array(
		        	'route'    => '/results',
			        'defaults' => array(
				        'controller' => 'Application\Controller\Index',
				        'action'     => 'results',
			        ),
		        ),
	        ),  
	        'login' => array(
		        'type' => 'Zend\Mvc\Router\Http\Segment',
		        'options' => array(
	        		'route'    => '/login',
	        		'defaults' => array(
	        				'controller' => 'Application\Controller\Index',
	        				'action'     => 'login',
	        		),
		        ),
	        ),  
	        'logout' => array(
		        'type' => 'Zend\Mvc\Router\Http\Segment',
		        'options' => array(
	        		'route'    => '/logout',
	        		'defaults' => array(
	        				'controller' => 'Application\Controller\Index',
	        				'action'     => 'logout',
	        		),
		        ),
	        ),
        ),
 		
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController'
        ),
    ),    
    'doctrine' => array(
    		'driver' => array(
    				'application_driver' => array(
    						'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
    						'cache' => 'array',
    						'paths' => array(
    							__DIR__ . '/../src/Application/Entity'
    						)
    				),    				
    				'orm_default' => array(
    						'drivers' => array(
    								'Application\Entity' =>  'application_driver',
    						),
    				),
    		),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
