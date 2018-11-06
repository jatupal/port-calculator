<?php
namespace Member\Form;

 use Zend\Form\Form;

 class UserGroupForm extends Form
 {
     public function __construct($name = null)
     {
         // we want to ignore the name passed
         parent::__construct('userGroup');
         $this->add(array(
             'name' => 'group_id',
             'type' => 'Hidden',
         ));
		  $this->add(array(
             'name' => 'group_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Group Name',
             ),
         ));
         $this->add(array(
             'name' => 'member_id',
             'type' => 'Text',
             'options' => array(
                 'label' => 'MemberID',
             ),
         ));

	 $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Go',
                 'id' => 'submitbutton',
             ),
         ));
     }
 }
 ?>