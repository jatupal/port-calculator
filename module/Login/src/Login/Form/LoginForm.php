<?php
namespace Login\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Element\Csrf;

class LoginForm extends Form {

    public function __construct($name) {

        parent::__construct($name);
        $this->setAttribute('method', 'post');
        
        
        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'type'  => 'text',
                'value' => '',
                'id' => 'username',
                'placeholder' => 'Username',
                'class' => 'form-control'
            ),
        
        ));
        
        $this->add(array(
            'name' => 'email',
            'type' => 'text',
            'options' => array(
                'label' => 'Email',
                'id' => 'email',
                'placeholder' => 'example@example.com',
            )
        ));

        
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type'  => 'password',
                'value' => '',
                'id' => 'password',
                'placeholder' => '**********',
                'class' => 'form-control'
            ),
        
        ));
        
         
        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'loginCsrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 3600
                )
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => "Sign in",
                'class' => "btn btn-primary btn-block"
            ),
        ));
        
        $this->add(array(
            'type' => 'Button',
            'name' => 'submit',
            'options' => array(
                'label' => 'Sign in <i class="icon-circle-right2 position-right"></i>',
                'label_options' => array(
                    'disable_html_escape' => true,
                )
            ),
            'attributes' => array(
                'type'  => 'submit',
                'class' => 'btn btn-success'
            )
        ));
    }
}