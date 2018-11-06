<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Login for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Login;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Router\RouteMatch;


use Zend\Authentication\Adapter\DbTable as DbAuthAdapter;
use Zend\Session\Container;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		        // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }
        

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager   = $e->getApplication()->getEventManager();
        $eventManager->attach('dispatch', array($this, 'loadConfiguration' ));
                
        
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $serviceManager = $e->getApplication()->getServiceManager();
        
		$eventManager->attach(MvcEvent::EVENT_DISPATCH, array(
            $this,
            'beforeDispatch'
        ), 100);
        
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array(
            $this,
            'afterDispatch'
        ), -100);
        
        
        //$app = $e->getApplication();
        //$eventManager = $app->getEventManager();
     
        //$this->initAcl($e); //Initialise the ACL 
        //Attach some events
        //$eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'authPreDispatch')); //Authentication check
        //$eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkAcl')); //Acl check
    }
       
    public function loadConfiguration(MvcEvent $e)
    {
        $session = new Container('User');
        
        $controller = $e->getTarget();
        if ($session->offsetExists ( 'user_id' )){
            $controller->layout()->user_id = $session->offsetGet ( 'user_id' );
            $controller->layout()->name = $session->offsetGet ( 'name' );
        }else{
            $controller->layout()->user_id = '';
            $controller->layout()->name = '';
        }        
        
        
        
    }
    
    public function beforeDispatch(MvcEvent $event){
        
        $request = $event->getRequest();
        $response = $event->getResponse();
        $target = $event->getTarget ();
    
        /* Offline pages not needed authentication */
        $whiteList = array (
            'Login\Controller\Login-index',            
        );
    
        $requestUri = $request->getRequestUri();
        $controller = $event->getRouteMatch ()->getParam ( 'controller' );
        $action = $event->getRouteMatch ()->getParam ( 'action' );
    
        $requestedResourse = $controller . "-" . $action;
        
        $session = new Container('User');
        
        $router   = $event->getRouter();
		$routeMatch = $event -> getRouteMatch();
		$routeParams = $routeMatch->getParams();
		
		if (trim(strtolower($requestedResourse)) == 'login\controller\login-index'){
			
		}else{
			if((!isset($session->user_id) || $session->user_id == '')){
				$routeParams = $routeMatch->getParams();
				if(isset($routeParams['__CONTROLLER__'])){
					$Controller = strtolower($routeParams['__CONTROLLER__']);		
				}elseif(isset($routeParams['controller'])){
					$Controller = strtolower($routeParams['controller']);		
				}else{
					$Controller = '';
				}
							
				$url = $router->assemble(array(), array(
					'name' => 'login'
				));
				
				//$url = 'login/login';
				$response->setHeaders ( $response->getHeaders ()->addHeaderLine ( 'Location', $url ) );
				$response->setStatusCode ( 302 );
				$response->sendHeaders ();			
			}else{				
				$routeMatch = $event -> getRouteMatch();
				$routeParams = $routeMatch->getParams();
				$serviceManager = $event->getApplication()->getServiceManager();
								
				if(isset($routeParams['__CONTROLLER__'])){
					$Controller = strtolower($routeParams['__CONTROLLER__']);		
				}elseif(isset($routeParams['controller'])){
					$Controller = strtolower($routeParams['controller']);		
				}else{
					$Controller = '';
				}
						
				$url = $this->CheckUser($event);
				//var_dump($url);
				if(!empty($url)){			
					$response->setHeaders ( $response->getHeaders ()->addHeaderLine ( 'Location', $url ) );
					$response->setStatusCode ( 302 );
					$response->sendHeaders ();
				}
			}
		}
    
        //print "Called before any controller action called. Do any operation.";
    }
	
	function CheckUser($event){
        $request = $event->getRequest();
        $response = $event->getResponse();
        $target = $event->getTarget ();    
       
        $session = new Container('User');
        $router   = $event->getRouter();		
        //echo 'Check User ';
		$url = '';
		if(!isset($session->user_id) || $session->user_id == ''){
			$url = $router->assemble(array(), array(
				'name' => 'login'
			));
		}else{
		}
		
		return $url;
    }
    
    function afterDispatch(MvcEvent $event){
        //print "Called after any controller action called. Do any operation.";
    }
        
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'AuthService' => function ($serviceManager) {
                    $adapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $dbAuthAdapter = new DbAuthAdapter ( $adapter, 'users', 'username', 'password' );
                    	
                    $auth = new AuthenticationService();
                    $auth->setAdapter ( $dbAuthAdapter );
                    return $auth;
                }
            ),
        );
    }
    
    public function authPreDispatch(MvcEvent $e)
    {
        $matches = $e->getRouteMatch();
        if(!$matches instanceof RouteMatch){
            return false;
        }
        $controller = $matches->getParams('controller');
        $app = $e->getApplication();
        $serviceManager = $app->getServiceManager();
        $auth = $serviceManager->get('AuthService');
        if(!$auth->hasIdentity() && $controller != 'Login\Controller\Login'){
            $router = $e->getRouteMatch();
            $url = $router->assemble(array(),array('name'=>'login'));
            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);
            
            return $response;
            
        }
        return false;
    }
    
    public function initAcl(MvcEvent $e){
        $acl = new \Zend\Permissions\Acl\Acl();
        $roles = include __DIR__ .'/config/module.acl.roles.php';
        $allResource = array();
        foreach($roles as $role => $resources){
            $role = new \Zend\Permissions\Acl\Role\GenericRole($role);
            $acl->addRole($role);
            
            $allResource = array_merge($resources, $allResource);
            
            foreach($resources as $resource){
                if(!$acl->hasResource($resource)){
                    $acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
                }
            }
            
            foreach($resources as $resource){
                $acl->allow($role, $resource);
            }
        }
        $e->getViewModel()->acl = $acl;
       
    }
    
    public function checkAcl(MvcEvent $e){
        $matches = $e->getRouteMatch();
        $action = $matches->getParams('action');
        $controller = explode("\\", $matches->getParams('controller'));
        
        $route = $controller[2].'/'.$action;
        $role = $e->getApplication()->getServiceManager()->get('AuthService')->getStorage()->read()->role;
        if(isset($role)){
            $userRole = $role;
        }else{
            $userRole = 'guest';
        }
        
        if($e->getViewModel()->acl->hasResource($route) || $e->getViewModel()->acl->isAllowed($userRole, $route)){
            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $e->getRequest()->getBaseUrl().'/404');
            $response->setStatusCode(401);
        }
        
    }

    
    
    
}
