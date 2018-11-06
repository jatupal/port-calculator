<?php
namespace Member\Form;

 use Zend\Form\Form;
 use Zend\Db\Adapter\AdapterInterface;
 use Zend\Db\Adapter\Adapter;

 class UserForm extends Form
 {
     protected $adapter;
     
     public function __construct(AdapterInterface $dbAdapter)
     {
         // we want to ignore the name passed
         $this->adapter =$dbAdapter;
         parent::__construct();
        
         
         $this->add(array(
             'name' => 'user_id',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'adm',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'member_id',
             'type' => 'Hidden',
         ));
		  $this->add(array(
             'name' => 'name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Name',
             ),
		     'attributes' => array(
		         'class' => 'form-control',
		     ),
         ));
         $this->add(array(
             'name' => 'email',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Email',
             ),
             'attributes' => array(
                 'class' => 'form-control',
             ),
         ));
		  $this->add(array(
             'name' => 'phone',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Telephone',
             ),
		      'attributes' => array(
		          'class' => 'form-control',
		      ),
         ));
         $this->add(array(
             'name' => 'username',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Username',
             ),
             'attributes' => array(
                 'class' => 'form-control',
             ),
         ));
         
         $this->add(array(
             'name' => 'company',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Ship From',
             ),
             'attributes' => array(
                 'class' => 'form-control',
             ),
         ));
         
         $this->add(array(
             'name' => 'address1',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Address1',
             ),
             'attributes' => array(
                 'class' => 'form-control',
             ),
         ));         
         
         
         $this->add(array(
             'name' => 'address2',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Address2',
             ),
             'attributes' => array(
                 'class' => 'form-control',
             ),
         ));
         
         $this->add(array(
             'name' => 'address3',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Address3',
             ),
             'attributes' => array(
		         'class' => 'form-control',
		     ),
         ));
         
         $this->add(array(
             'name' => 'city',
             'type' => 'Text',
             'options' => array(
                 'label' => 'City',
             ),
             'attributes' => array(
		         'class' => 'form-control',
		     ),
         ));
         
         $this->add(array(
             'name' => 'state',
             'type' => 'Text',
             'options' => array(
                 'label' => 'State',
             ),
             'attributes' => array(
                 'class' => 'form-control',
             ),
         ));
         
         $this->add(array(
             'name' => 'zipcode',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Zipcode',
             ),
             'attributes' => array(
                 'class' => 'form-control',
             ),
         ));
         
         $this->add(array(
             'name' => 'countrycode',
             'type' => 'Zend\Form\Element\Select',
             'options' => array(
                 'label' => 'Country',
                 'value_options' => $this->getCountrySelect(),
             ),
             'attributes' => array(
                 'class' => 'select-clear',
             ),
         ));
                  

         $this->add(array(
             'name' => 'password',
             'type' => 'Password',
             'options' => array(
                 'label' => 'Password',
             ),
             'attributes' => array(
                 'class' => 'form-control',
             ),
         ));
/*
		  $this->add(array(
             'name' => 'showdata',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Show',
             ),
         ));
*/
	 $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Go',
                 'id' => 'submitbutton',
             ),
         ));
/*
	 	  $this->add(array(
             'name' => 'company',
             'type' => 'Zend\Form\Element\MultiCheckbox',
             'options' => array(
                 'label' => 'Company',
             ),
         ));
*/
	 }
 
 
 
	 public function getCountrySelect()
	 {
	     $dbAdapter = $this->adapter;
	     $sql       = 'SELECT *  FROM countrycode ORDER BY country_label ASC';
	     $statement = $dbAdapter->query($sql);
	     $result    = $statement->execute();
	 
	     $selectData = array();
	     $selectData['']= 'Select Country';
	     foreach ($result as $res) {
	         $selectData[$res['countrycode']] = $res['country_label'];
	     }
	     return $selectData;
	 }
 
 }
 ?>