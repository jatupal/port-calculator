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
use Member\Model\Role;          // <-- Add this import
use Member\Form\RoleForm;
use Member\Model\AssignRole;
use Member\Form\AssignForm;
use Zend\View\Model\ViewModel;
class RoleController extends AbstractActionController
{
	protected $roleTable;
	protected $assignRoleTable;
	 public function getRoleTable()
     {
         if (!$this->roleTable) {
             $sm = $this->getServiceLocator();
             $this->roleTable = $sm->get('Member\Model\RoleTable');
         }
         return $this->roleTable;
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
             'roles' => $this->getRoleTable()->fetchAll(),
         ));
    }

    public function fooAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /member/member/foo
        return array();
    }
    public function addAction()
    {
		 $form = new RoleForm();
         $form->get('submit')->setValue('Add');

         $request = $this->getRequest();
         if ($request->isPost()) {
             $role = new Role();
             $form->setInputFilter($role->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
                 $role->exchangeArray($form->getData());
                 $this->getRoleTable()->saveRole($role);

                 // Redirect to list of albums
                 return $this->redirect()->toRoute('role');
             }
         }
         return array('form' => $form);
    }
	    public function editAction()
    {
         $id = (int) $this->params()->fromRoute('id', 0);
         if (!$id) {
             return $this->redirect()->toRoute('role', array(
                 'action' => 'add'
             ));
         }
         try {
             $role = $this->getRoleTable()->getRole($id);
         }
         catch (\Exception $ex) {
             return $this->redirect()->toRoute('role', array(
                 'action' => 'index'
             ));
         }
         $form  = new RoleForm();
         $form->bind($role);
		 $form->get('isActive')->setValue($role->isActive);
         $form->get('submit')->setAttribute('value', 'Edit');
         $request = $this->getRequest();
         if ($request->isPost()) {
             $form->setInputFilter($role->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
                 $this->getRoleTable()->saveRole($role);

                 // Redirect to list of albums
                 return $this->redirect()->toRoute('role');
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
	    public function assignBAction()
    {
		 $form = new AssignForm();
         $form->get('submit')->setValue('Assign');
		 $UserTable = $this->getServiceLocator()->get('UserTable');
    	 $users = $UserTable->fetchAll();
		 $user_array = array();
		 foreach($users as $user){
			 $user_array[$user->id] = $user->name;
		 }
		 
		 $form->add(array(
             'name' => 'user',
             'type' => 'Zend\Form\Element\Select',
             'options' => array(
                 'label' => 'Select User',
				 'value_options' => $user_array,
             ),
         ));
		 
		 $roles = $this->getRoleTable()->fetchAll();
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
		

         $request = $this->getRequest();
         if ($request->isPost()) {
             $form = $request->getPost();
			 //echo "User_id:".$form->user;
	 		 //$AssignRoleTable = $this->getServiceLocator()->get('AssignRoleTable');
//			 if($form->roles){
				 foreach ($form->roles as $role){
					var_dump($role);
					$role_x = $this->getRoleTable()->getRole($role);
					var_dump($role_name);
					$result = $this->getAssignRoleTable()->assignRole($form->user,$role_x->role_name);
				 }
//			 }
			 //var_dump($form->roles);
             return $this->redirect()->toRoute('role');
         }
         return array('form' => $form);
    }

	public function chkRoleAction($role_id,$user_id){
		$result = $this->getAssignRoleTable()->chkRole($role_id,$user_id);
		return $result;
	}

}
