<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Member for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Member\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Member\Model\User;          // <-- Add this import
use Member\Form\UserForm;
use Member\Form\AssignForm;
use Zend\View\Model\ViewModel;
use Member\Model\Company;
class UserController extends AbstractActionController
{
	protected $userTable;
	protected $roleTable;
	protected $companyTable;
	protected $companyUserTable;
	protected $companyAddressTable; 
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
     
     public function getCompanyAddressTable()
     {
         if (!$this->companyTable) {
             $sm = $this->getServiceLocator();
             $this->companyTable = $sm->get('Member\Model\CompanyAddressTable');
         }
         return $this->companyAddressTable;
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

    public function indexAction()
    {
		$userSession = new Container('User');
	    $member_id = $userSession->member_id;
		if($member_id > 0){
			return new ViewModel(array(
				'users' => $this->getUserTable()->fetchUsers($member_id),
			));
		}else{
			return new ViewModel(array(
				'users' => $this->getUserTable()->fetchAll(),
			));
		}
    }

    public function getRolesAction()
    {
		$userSession = new Container('User');
		$user_id = $userSession->user_id;
		$group_id = $this->getAssignUserGroupTable()->getUserGroup($user_id)->group_id;
		$roles = $this->getServiceLocator()->get('Member\Model\AssignRoleTable')->getRoles($group_id);
		$role_array = array();
		foreach($roles as $role){
			$role_array[] = $role->role_name;
		}
		return $role_array;
    }

    public function addAction()
    {
		 $member_id = 1;
		 $role_name = "member::user::add";
		 $userSession = new Container('User');
		 $user_id = $userSession->user_id;
		 //if(!$this->getServiceLocator()->get('Member\Model\AssignRoleTable')->chkRole($role_name,$user_id)){
			//return $this->redirect()->toRoute('user');
		 //}else{
		 //}
		 $userSession = new Container('User');
		 $member_id = $userSession->member_id;
         
		 $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		 $form = new UserForm($dbAdapter);
		 
		 $form->get('submit')->setValue('Add');
		 $form->get('member_id')->setValue($member_id);
         $request = $this->getRequest();
         if ($request->isPost()) {
             $user = new User();
             $form->setInputFilter($user->getInputFilter());
             $form->setData($request->getPost());
             if ($form->isValid()) {
				 $result_array = $request->getPost();
                 $user->exchangeArray($form->getData());
                 $user_id = $this->getUserTable()->saveUser($user);
				 
                 $company = new Company();
				 $company->company_name = $request->getPost()->company;
				 $company->member_id = $member_id;
				 $company_id = $this->getCompanyTable()->saveCompany($company);
				 
				 $company_array = array();
				 $company_array['fullname'] = $request->getPost()->company;
				 $company_array['address1'] = $request->getPost()->address1;
				 $company_array['address2'] = $request->getPost()->address2;
				 $company_array['address3'] = $request->getPost()->address3;
				 $company_array['city'] = $request->getPost()->city;
				 $company_array['state'] = $request->getPost()->state;
				 $company_array['zipcode'] = $request->getPost()->zipcode;
				 $company_array['countrycode'] = $request->getPost()->countrycode;
				 
				 $CountryTable = $this->getServiceLocator()->get('CountryTable');
				 $CountryDetail = $CountryTable->GetCountry($request->getPost()->countrycode);
				 if(!empty($CountryDetail)){$country = $CountryDetail[0]['country_label'];}else{$country = '';}
				 $company_array['country'] = $country;
				 $company_array['email'] = $request->getPost()->email;
				 $company_array['phone'] = $request->getPost()->phone;				 
				 
				 $CompanyAddressTable = $this->getServiceLocator()->get('CompanyAddressTable');
				 
				 $CompanyAddressTable->AddCompanyAddress($company_id, $company_array);
				 
				 $this->getCompanyUserTable()->saveCompanyUser($user_id,$company_id);
				 
				 $result = $this->getAssignUserGroupTable()->assignUserGroup($user_id,$company_id,1);
				 
				 /*
				 foreach($request->getPost()->company as $comp){
					$this->getCompanyUserTable()->saveCompanyUser($user_id,$comp);
				 }
				 */
                 // Redirect to list of albums
                 return $this->redirect()->toRoute('user',array('action' => 'index'));
             }
         }
         return array('form' => $form);
    }
    
    

    public function registerAction()
    {
        $this->layout('login/login');
        $member_id = 1;
        $role_name = "member::user::add";
        $userSession = new Container('User');
        $user_id = $userSession->user_id;
         
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new UserForm($dbAdapter);
        	
        $form->get('submit')->setValue('Add');
        $form->get('member_id')->setValue($member_id);
        
        $user = new User();
        $form->setInputFilter($user->getInputFilter());
        
        //var_dump($form);
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $result_array = $request->getPost();
                $user->exchangeArray($form->getData());
                $user_id = $this->getUserTable()->saveUser($user);
                	
                $company = new Company();
                $company->company_name = $request->getPost()->company;
                $company->member_id = $member_id;
                $company_id = $this->getCompanyTable()->saveCompany($company);
                	
                $company_array = array();
                $company_array['fullname'] = $request->getPost()->company;
                $company_array['address1'] = $request->getPost()->address1;
                $company_array['address2'] = $request->getPost()->address2;
                $company_array['address3'] = $request->getPost()->address3;
                $company_array['city'] = $request->getPost()->city;
                $company_array['state'] = $request->getPost()->state;
                $company_array['zipcode'] = $request->getPost()->zipcode;
                $company_array['countrycode'] = $request->getPost()->countrycode;
                	
                $CountryTable = $this->getServiceLocator()->get('CountryTable');
                $CountryDetail = $CountryTable->GetCountry($request->getPost()->countrycode);
                if(!empty($CountryDetail)){$country = $CountryDetail[0]['country_label'];}else{$country = '';}
                $company_array['country'] = $country;
                $company_array['email'] = $request->getPost()->email;
                $company_array['phone'] = $request->getPost()->phone;
                	
                $CompanyAddressTable = $this->getServiceLocator()->get('CompanyAddressTable');
                	
                $CompanyAddressTable->AddCompanyAddress($company_id, $company_array);
                	
                $this->getCompanyUserTable()->saveCompanyUser($user_id,$company_id);
                	
                $result = $this->getAssignUserGroupTable()->assignUserGroup($user_id,$company_id,1);
                	
                /*
                 foreach($request->getPost()->company as $comp){
                 $this->getCompanyUserTable()->saveCompanyUser($user_id,$comp);
                 }
                */
                // Redirect to list of albums
                return $this->redirect()->toRoute('user',array('action' => 'index'));
            }
        }
        return array('form' => $form);
    }
    
    
    
	public function edit2Action(){
		$request = $this->getRequest();
        if ($request->isPost()) {
			$user_id = $request->getPost()->user_id;
			$user = new User();
			//$user = $this->getUserTable()->getUser($user_id);
			$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		    $form = new UserForm($dbAdapter);
			//$form->bind($user);
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
			}
                 $user->exchangeArray($form->getData());
				 $user_id = $this->getUserTable()->saveUser($user);

 				 //$this->getCompanyUserTable()->deleteAllCompanyUser($user_id);
				 //foreach($request->getPost()->company as $comp){
				 //	$this->getCompanyUserTable()->saveCompanyUser($user_id,$comp);
				 //}
				 //RENAME COMPANY NAME
				 $usercompany_array = $this->getCompanyUserTable()->getCompanyUser($user_id);
				foreach($usercompany_array as $user_company){
					 $company_id = $user_company->company_id;
				 //echo $user_company->company_id.'<br>';
				}
				$member_id = $request->getPost()->member_id;
				 $company = new Company();
				 $company->company_id = $company_id;
				 $company->member_id = $member_id;
				 $company->company_name = $request->getPost()->company;
				 $this->getCompanyTable()->saveCompany($company);
                 // Redirect to list of albums
                 return $this->redirect()->toRoute('user',array('action' => 'index'));
            //}else{
				 //echo $request->getPost()->username;
				 //var_dump($form->getMessages());
			//	 var_dump($request->getPost()->company);

				 //return $this->redirect()->toRoute('user',array('action' => 'index'));
			
         }else{
		 }

	}
	    public function editAction()
    {
		 $role_name = "member::user::edit";
		 $userSession = new Container('User');
		 $member_id = $userSession->member_id;
//set value for test
		 $member_id = 1;
//		 $user_id = $userSession->user_id;
		 //if(!$this->getServiceLocator()->get('Member\Model\AssignRoleTable')->chkRole($role_name,$user_id)){
			//return $this->redirect()->toRoute('user');
		 //}else{
		 //}
         $id = (int) $this->params()->fromRoute('id', 0);
/*
		 if (!$id) {
             return $this->redirect()->toRoute('user', array(
                 'action' => 'add'
             ));
         }
*/
         $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		 $form = new UserForm($dbAdapter);
		    
		 if($id){
		 try {
             $user = $this->getUserTable()->getUser($id);
         }
         catch (\Exception $ex) {
             return $this->redirect()->toRoute('user', array('action' => 'index'));
//			echo "Error:".$ex;
         }

         $form->bind($user);
// 		 $form->get('showdata')->setAttribute('value', $userSession->id.":2");
		 $form->get('member_id')->setAttribute('value', $member_id);
         $form->get('submit')->setAttribute('value', 'Edit');
//		 $companies = $this->getCompanyTable()->getMemberCompany($member_id);
/*
		 $company_array = array();
		 foreach($companies as $company){
			 $company_array[$company->company_id] = $company->company_name;
		 }
*/
//		 $set_company = array();
//		 $usercompany_array = $this->getCompanyUserTable()->getCompanyUser($id);
//		 $form->get('company')->setValueOptions($company_array);
		 //$form->get('company')->setRegisterInArrayValidator(false);
/*
		 foreach($usercompany_array as $user_company){
			 $set_company[] = $user_company->company_id;
			 //echo $user_company->company_id.'<br>';
		 }
 		 $form->get('company')->setAttribute('value', $set_company);
*/
		$usercompany_array = $this->getCompanyUserTable()->getCompanyUser($id);
		foreach($usercompany_array as $user_company){
			 $company_id = $user_company->company_id;
			 //echo $user_company->company_id.'<br>';
		}
		$company = $this->getCompanyTable()->getCompany($company_id);
		$form->get('company')->setAttribute('value', $company->company_name);
//		 var_dump($set_company);
		}

         return array(
             'id' => $id,
             'form' => $form,
         );
    }
	    public function listUserAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /member/member/foo
        return array();
    }
	   public function assignAction()
    {
		 $id = (int) $this->params()->fromRoute('id', 0);
//         if (!$id) {
//             return $this->redirect()->toRoute('user', array('action' => 'index'));
//         }
		 $form = new AssignForm();
         $form->get('submit')->setValue('Assign');
		 $UserTable = $this->getServiceLocator()->get('UserTable');
    	 $users = $UserTable->fetchAll();
		 $user_array = array();
		 foreach($users as $user){
			 $user_array[$user->user_id] = $user->name;
		 }
		 $form->add(array(
             'name' => 'user',
             'type' => 'Zend\Form\Element\Select',
             'options' => array(
                 'label' => 'Select User',
				 'value'        => $id,
				 'value_options' => $user_array,
             ),
         ));
 		 $form->get('user')->setValue($id);
//Change Assign Roles to Role Groups
		 $user_group_id = $this->getAssignUserGroupTable()->getUserGroup($id)->group_id;
		 if($user_group_id>0){
			//echo $user_group_id;
		 }else{
			  $user_group_id = 0;
		 }
		 $RoleGroupTable = $this->getServiceLocator()->get('Member\Model\RoleGroupTable');
		 $role_groups = $RoleGroupTable->fetchAll();
		 $role_array = array();
		 foreach($role_groups as $role){
			 $role_array[$role->group_id] = $role->group_name;
		 }
		 $form->add(array(
			'type' => 'Zend\Form\Element\Radio',
			'name' => 'groups',
			'options' => array(
				'label' => 'Assign Role Group?',
				'value_options' => $role_array,
			),
			'attributes' => array(
                'value' =>  $user_group_id,
			),
		));
/*
		 $RoleTable = $this->getServiceLocator()->get('RoleTable');
		 $roles = $RoleTable->fetchAll();
		 $role_array = array();
		 foreach($roles as $role){
			 $role_array[$role->id] = $role->name;
		 }
		 
		 $form->add(array(
			'type' => 'Zend\Form\Element\MultiCheckbox',
			'name' => 'roles',
			'options' => array(
				'label' => 'Assign Role?',
				'value_options' => $role_array,
			),
		));
		
*/
         $request = $this->getRequest();
         if ($request->isPost()) {
             $form = $request->getPost();
			 $user_id = $form->user;
			 $company_id = 1;
			 //echo $form->groups;
			 $result = $this->getAssignUserGroupTable()->assignUserGroup($user_id,$company_id,$form->groups);
			 /*
			 var_dump($form->roles);
			 foreach ($form->roles as $role){
					//var_dump($role);
					$role_x = $this->getRoleTable()->getRole($role);
					//var_dump($role_x);
					$result = $this->getAssignRoleTable()->assignRole($form->user,$role_x->name);
			 }
			*/
//			 $result = $this->getAssignRoleTable()->assignRole($form->user,$form->roles);
			 //echo 'user:'.$form->user;
			 //var_dump($form->roles);
             return $this->redirect()->toRoute('user');
         }
         return array('form' => $form);
    }
}
