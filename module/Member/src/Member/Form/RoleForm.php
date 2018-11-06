<?php
namespace Member\Form;

use Zend\Form\Form;
use Zend\Form\Element\Select;
 class RoleForm extends Form
 {
     public function __construct($name = null)
     {
         // we want to ignore the name passed
         parent::__construct('user');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
		  $this->add(array(
             'name' => 'name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Name',
             ),
         ));
		  $this->add(array(
             'name' => 'isActive',
             'type' => 'Zend\Form\Element\Select',
             'options' => array(
                 'label' => 'isActive',
				 'value_options' => array(
                             'Y' => 'Yes',
                             'N' => 'No',
				  ),
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