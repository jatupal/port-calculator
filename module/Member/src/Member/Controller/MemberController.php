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
use Member\Model\Member;
use Member\Model\User;
use Member\Form\MemberForm;
use Member\Form\UserForm;
use Zend\View\Model\ViewModel;
class MemberController extends AbstractActionController
{
	protected $memberTable;
	protected $userTable;
	 public function getMemberTable()
     {
         if (!$this->memberTable) {
             $sm = $this->getServiceLocator();
             $this->memberTable = $sm->get('Member\Model\MemberTable');
         }
         return $this->memberTable;
     }
    public function indexAction()
    {
       return new ViewModel(array(
             'members' => $this->getMemberTable()->fetchAll(),
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
		 $form = new MemberForm();
         $form->get('submit')->setValue('Add');

         $request = $this->getRequest();
         if ($request->isPost()) {
             $member = new Member();
             $form->setInputFilter($member->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
                 $member->exchangeArray($form->getData());
                 $member_id = $this->getMemberTable()->saveMember($member);
				 return $this->redirect()->toRoute('member', array(
                 'action' => 'addMemberUser', 'id' => $member_id
				 ));
                 // Redirect to list of albums
				 //return $this->redirect()->toRoute('member');
             }
         }
         return array('form' => $form);
    }
	    public function editAction()
    {
         $id = (int) $this->params()->fromRoute('id', 0);
         if (!$id) {
             return $this->redirect()->toRoute('member', array(
                 'action' => 'add'
             ));
         }
         try {
             $member = $this->getMemberTable()->getMember($id);
         }
         catch (\Exception $ex) {
             return $this->redirect()->toRoute('member', array(
                 'action' => 'index'
             ));
         }
         $form  = new MemberForm();
		 $form->get('member_name')->setValue($member->member_name);
		 $form->get('id')->setValue($member->member_id);
		 $form->get('is_active')->setValue($member->is_active);
         //$form->bind($member);
         $form->get('submit')->setAttribute('value', 'Edit');
         $request = $this->getRequest();
         if ($request->isPost()) {
             $form->setInputFilter($member->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
                 $this->getMemberTable()->saveMember($member);

                 // Redirect to list of albums
                 return $this->redirect()->toRoute('member');
             }
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
	    public function addMemberUserAction()
    {
         $member_id = (int) $this->params()->fromRoute('id', 0);
		 $form = new UserForm();
         $form->get('submit')->setValue('Add');
		 $form->get('member_id')->setValue($member_id);
		 $form->get('name')->setLabel('Admin Name');
		 $form->get('username')->setLabel('Admin User Name');
		 $form->get('adm')->setValue(1);
         $request = $this->getRequest();
         if ($request->isPost()) {
             $user = new User();
             $form->setInputFilter($user->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
             }else{
//				echo "ERROR";
			 }

				 $user->exchangeArray($form->getData());
				 $sm2 = $this->getServiceLocator();
                 $this->userTable = $sm2->get('Member\Model\UserTable');
                 $this->userTable->saveUser($user);

                 // Redirect to list of albums
                 return $this->redirect()->toRoute('member');
         }
         return array('form' => $form);
	}					 
}
