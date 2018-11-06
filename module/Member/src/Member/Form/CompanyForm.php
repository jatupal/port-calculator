<?php
namespace Member\Form;

 use Zend\Form\Form;

 class CompanyForm extends Form
 {
     public function __construct($name = null)
     {
         // we want to ignore the name passed
         parent::__construct('album');

         $this->add(array(
             'name' => 'company_id',
             'type' => 'Hidden',
         ));

         $this->add(array(
             'name' => 'member_id',
             'type' => 'Hidden',
         ));

		 $this->add(array(
             'name' => 'company_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Company Name',
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