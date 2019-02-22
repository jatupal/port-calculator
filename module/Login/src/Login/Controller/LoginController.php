<?php
namespace Login\Controller;
    
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Login\Form\LoginForm;
use Login\Form\Filter\LoginFilter;
use Login\Model\UserPassword;

	
class LoginController extends AbstractActionController {
		
	protected $storage;
	protected $authservice;
	protected $baseUrl;
	public function getBaseUrl(){	   
		$basePath = $this->getRequest()->getBasePath();
		$uri = new \Zend\Uri\Uri($this->getRequest()->getUri());
		$uri->setPath($basePath);
		$uri->setQuery(array());
		$uri->setFragment('');
		$this->baseUrl = $uri->getPath();
	
		return $this->baseUrl;
	}
	protected $userTable;
	protected $roleTable;
	protected $companyTable;
	protected $companyUserTable;
	protected $assignRoleTable;
	protected $assignUserGroupTable;	
	
	
	public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Member\Model\UserTable');
		}
		return $this->userTable;
	}

	public function getCompanyUserTable()
	{
		if (!$this->companyUserTable) {
            $sm = $this->getServiceLocator();
            $this->companyUserTable = $sm->get('Member\Model\CompanyUserTable');
		}
		return $this->companyUserTable;
	}

	public function getRoleTable()
	{
		if (!$this->roleTable) {
            $sm = $this->getServiceLocator();
            $this->roleTable = $sm->get('Member\Model\RoleTable');
		}
		return $this->roleTable;
	}

	public function getCompanyTable()
	{
		if (!$this->companyTable) {
            $sm = $this->getServiceLocator();
            $this->companyTable = $sm->get('Member\Model\CompanyTable');
		}
		return $this->companyTable;
	}

	public function getAssignRoleTable()
	{
		if (!$this->assignRoleTable) {
            $sm = $this->getServiceLocator();
            $this->assignRoleTable = $sm->get('Member\Model\AssignRoleTable');
		}
		return $this->assignRoleTable;
	}

	public function getAssignUserGroupTable()
	{
		if (!$this->assignUserGroupTable) {
            $sm = $this->getServiceLocator();
            $this->assignUserGroupTable = $sm->get('Member\Model\AssignUserGroupTable');
		}
		return $this->assignUserGroupTable;
	}
		
	
	public function indexAction(){
	
		$request = $this->getRequest();
		$session = new Container('User');
		
		$view = new ViewModel();
		$loginForm = new LoginForm('loginForm');       
		$loginForm->setInputFilter(new LoginFilter() );
		
		$ReportSession = new Container('ReportSession');
		$ReportSession->getManager()->getStorage()->clear('ReportSession');  
		$error_message = '';
		if($request->isPost()){
		    $data = $request->getPost();
		    $loginForm->setData($data);
            
		    
			if(!empty($data)){
    			//$data = $loginForm->getData();    			
    			$userPassword = new UserPassword();
    			$encyptPass = $userPassword->create($data['password']);
    			
    			$UserTable = $this->getServiceLocator()->get('UserTable');
    			$resultUser = $UserTable->checkUser($data['username'], $encyptPass);
    			//var_dump($resultUser);
    			
    			if (!empty($resultUser)) {
					$session->offsetSet('user_id', $resultUser[0]['user_id']);
					$session->offsetSet('name', $resultUser[0]['name']);
					
					return $this->redirect()->toRoute('reports',
						array('controller'=>'Reports',
							'action' => 'index',
						));
    				
    			}else{
    			   //echo 'Login Error<BR>';
    				$this->flashMessenger()->addMessage(array('error' => 'invalid User or Password.'));
    				$error_message = 'invalid User or Password.';
    				
    			}						
			// Logic for login authentication                
			}else{
			    //echo 'Form Not Valid';
                $errors = $loginForm->getMessages();
			//prx($errors);  
			}
		}else{
		   //$session->getManager()->destroy();
		   //echo 'No Post Value';
		}
		
		$view->setVariable('loginForm', $loginForm);
		$view->setVariable('baseUrl', $this->getBaseUrl());
		$view->setVariable('error_message', $error_message);
		
		
		return $view;
	}
	
	public function AssignRoleToLayout(MvcEvent $e)
	{
	    $AssignRoleTable = $this->getAssignRoleTable();		
		$controller = $e->getTarget();
		$controller->layout()->AssignRoleTable = $AssignRoleTable;
	}
		
	private function getAuthService()
	{
		if (! $this->authservice) {
			$this->authservice = $this->getServiceLocator()->get('AuthService');
		}
		return $this->authservice;
	}

    public function logoutAction(){
        $session = new Container('User');        
        $session->getManager()->getStorage()->clear('User');
        $session->getManager()->destroy();
        $this->getAuthService()->clearIdentity();
        
		$ReportSession = new Container('ReportSession');
		$ReportSession->getManager()->getStorage()->clear('ReportSession');  
		
        //echo 'User ID -> '.$session->offsetGet ( 'user_id' ).'<BR>';
        //return $this->redirect()->toUrl('/login/login');
        return $this->redirect()->toRoute('login',
            array('controller'=>'Login',
                'action' => 'index',
            ));
    }
    
}
?>	