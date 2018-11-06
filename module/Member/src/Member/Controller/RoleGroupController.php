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
use Member\Model\RoleGroup;          // <-- Add this import
use Member\Form\RoleGroupForm;
use Member\Form\GroupRoleForm;
use Member\Model\AssignRole;
use Member\Form\AssignForm;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
class RoleGroupController extends AbstractActionController
{
	protected $roleGroupTable;
	protected $roleTable;
	protected $assignRoleTable;

	public function getRoleTable(){
		if (!$this->roleTable) {
             $sm = $this->getServiceLocator();
             $this->roleTable = $sm->get('Member\Model\RoleTable');
         }
         return $this->roleTable;
	}
	 public function getRoleGroupTable()
     {
         if (!$this->roleGroupTable) {
             $sm = $this->getServiceLocator();
             $this->roleGroupTable = $sm->get('Member\Model\RoleGroupTable');
         }
         return $this->roleGroupTable;
     }
	 public function getAssignRoleTable()
     {
         if (!$this->assignRoleTable) {
             $sm = $this->getServiceLocator();
             $this->assignRoleTable = $sm->get('Member\Model\AssignRoleTable');
         }
         return $this->assignRoleTable;
     }
	public function indexAction()
    {
       return new ViewModel(array(
             'usergroups' => $this->getRoleGroupTable()->fetchAll(),
         ));
    }

    public function addAction()
    {
		 $form = new GroupRoleForm();
         $form->get('submit')->setValue('Add');
		 $userSession = new Container('User');
		 $member_id = $userSession->member_id;
		 $member_id = 1;									//dummy
		 $form->get('member_id')->setValue($member_id);
		 $RoleTable = $this->getServiceLocator()->get('RoleTable');
		 $roles = $RoleTable->fetchAll();
		 $role_array = array();
		 foreach($roles as $role){
			 $role_array[$role->id] = $role->name;
		 }
//		 $form->roles = $role_array;
		 $form->add(array(
			'type' => 'Zend\Form\Element\MultiCheckbox',
			'name' => 'roles',
			'options' => array(
				'label' => 'Select Roles',
				'value_options' => $role_array,
				 'disable_inarray_validator' => true,
			),
		));
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
				'label' => 'Assign Roles?',
				'value_options' => $role_array,
			),
		));

         $request = $this->getRequest();
         if ($request->isPost()) {
             $role = new RoleGroup();
             $form->setInputFilter($role->getInputFilter());
             $form->setData($request->getPost());
             if ($form->isValid()) {
			 }else{
			 }
                 $role->exchangeArray($form->getData());
                 
				 $group_id = $this->getRoleGroupTable()->saveUserGroup($role);
				 foreach ($request->getPost()->roles as $role){
					$role_x = $this->getRoleTable()->getRole($role);
					$result = $this->getAssignRoleTable()->assignRole($group_id,$role_x->name);
				 }
				 //var_dump($form->roles);
				 //echo "<br><br>";
				 //var_dump($result);
                 //return $this->redirect()->toRoute('role-group');
         }
         return array('form' => $form);
    }
	    public function editAction()
    {
         $id = (int) $this->params()->fromRoute('id', 0);
         if (!$id) {
             return $this->redirect()->toRoute('role-group', array(
                 'action' => 'add'
             ));
         }
         try {
             $role = $this->getRoleGroupTable()->getRole($id);
         }
         catch (\Exception $ex) {
             return $this->redirect()->toRoute('role-group', array(
                 'action' => 'index'
             ));
         }
         $form  = new RoleGroupForm();
         $form->bind($role);
		 $form->get('isActive')->setValue($role->isActive);
         $form->get('submit')->setAttribute('value', 'Edit');
         $request = $this->getRequest();
         if ($request->isPost()) {
             $form->setInputFilter($role->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
                 $this->getRoleGroupTable()->saveRole($role);

                 // Redirect to list of albums
                 return $this->redirect()->toRoute('role-group');
             }
         }
         return array(
             'id' => $id,
             'form' => $form,
         );
    }
	    public function listRoleAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /member/member/foo
        return array();
    }
/*
	public function chkRoleAction($role_id,$user_id,$company_id){
		$result = $this->getAssignRoleGroupTable()->chkRole($role_id,$user_id,$company_id);
		return $result;
	}
*/
	public function chkRoleAction($role_id){
		 $userSession = new Container('User');
		 $user_id = $userSession->user_id;
		 $company_id = $userSession->company_id;
		 $role_array = $userSession->UserRoles;
		 $result = $this->getRoleGroupTable()->chkRole($role_id,$user_id,$company_id,$role_array);
		return $result;
	}
}
