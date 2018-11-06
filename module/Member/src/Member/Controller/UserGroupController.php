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
use Member\Model\UserGroup;
use Member\Form\UserGroupForm;
use Zend\View\Model\ViewModel;
use Member\Model\Role;

class UserGroupController extends AbstractActionController
{
	protected $userGroupTable;
	protected $roleTable;
	protected $assignRoleTable;
	 public function getUserGroupTable()
     {
         if (!$this->userGroupTable) {
             $sm = $this->getServiceLocator();
             $this->userGroupTable = $sm->get('Member\Model\UserGroupTable');
         }
         return $this->userGroupTable;
     }

	 	 public function getRoleTable()
     {
         if (!$this->roleTable) {
             $sm = $this->getServiceLocator();
             $this->roleTable = $sm->get('Member\Model\RoleTable');
         }
         return $this->roleTable;
     }

    public function indexAction()
    {
       return new ViewModel(array(
             'usergroups' => $this->getUserGroupTable()->fetchAll(),
         ));
    }

    public function addAction()
    {
		 $role_name = "member::usergroup::add";
		 $userSession = new Container('User');
		 $user_id = $userSession->user_id;
		 if(!$this->getServiceLocator()->get('Member\Model\AssignRoleTable')->chkRole($role_name,$user_id)){
//			return $this->redirect()->toRoute('user-group');
		 }else{
		 }
		 $member_id = $userSession->member_id;
		 $form = new UserGroupForm();
         $form->get('submit')->setValue('Add');
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

		 $request = $this->getRequest();
         if ($request->isPost()) {
             $userGroup = new UserGroup();
             $form->setInputFilter($userGroup->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
			 }
                 $userGroup->exchangeArray($form->getData());
				//var_dump($this->getUserGroupTable());
				//echo "====<br>";
				//var_dump($userGroup);
                 $this->getUserGroupTable()->saveUserGroup($userGroup);

                 // Redirect to list of albums
                 return $this->redirect()->toRoute('user-group');
             //}else{
			//	echo "INVALID";
			//	var_dump($form->getMessages());
			 //}
         }
         return array('form' => $form);
    }
	    public function editAction()
    {
		 $role_name = "member::usergroup::edit";
		 $userSession = new Container('User');
		 $user_id = $userSession->user_id;
		 if(!$this->getServiceLocator()->get('Member\Model\AssignRoleTable')->chkRole($role_name,$user_id)){
			return $this->redirect()->toRoute('user');
		 }else{
		 }

         $id = (int) $this->params()->fromRoute('id', 0);
         if (!$id) {
             return $this->redirect()->toRoute('usergroup', array(
                 'action' => 'add'
             ));
         }
         try {
             $userGroup = $this->getUserGroupTable()->getUserGroup($id);
         }
         catch (\Exception $ex) {
             return $this->redirect()->toRoute('usergroup', array('action' => 'index'));
//			echo "Error:".$ex;
         }
         $form  = new UserForm();
         $form->bind($userGroup);
// 		 $form->get('showdata')->setAttribute('value', $userSession->id.":2");
		 $form->get('member_id')->setAttribute('value', $userSession->member_id);
         $form->get('submit')->setAttribute('value', 'Edit');
         $request = $this->getRequest();
         if ($request->isPost()) {
             $form->setInputFilter($userGroup->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
                 $this->getUserTable()->saveUser($userGroup);

                 // Redirect to list of albums
                 return $this->redirect()->toRoute('usergroup');
             }
         }
         return array(
             'id' => $id,
             'form' => $form,
         );
    }
	    public function listUserGroupAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /member/member/foo
        return array();
    }
}
