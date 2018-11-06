<?php
namespace Member\Form;

use Zend\Form\Form;
use Zend\Form\Element\Select;
use Zend\Form\Element\Check;

 class AssignForm extends Form
 {
//	protected $_roles = array();
//    protected $_users = array();
    
	public function __construct($name = null)
     {
		parent::__construct('user');
		$this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Assign',
                 'id' => 'submitbutton',
             ),
         ));
     }
	 /*
	 public function getUsers() {
        return $this->_users;
     }
	 public function setUsers($users) {
        $this->_users = $users;
     }
	 public function getRoles() {
        return $this->_roles;
     }
	 public function setRoles($roles) {
        $this->_roles = $roles;
     }
	 */
 }
 ?>