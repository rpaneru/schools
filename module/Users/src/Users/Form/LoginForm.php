<?php
namespace Users\Form;

use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterProviderInterface;

class LoginForm extends Form implements InputFilterProviderInterface 
{
    public function __construct() 
    {
       parent::__construct("Login Form");
       

        $this-> add(array(
            'name' => 'userId',
            'type' => 'Zend\Form\Element\Text',
             'attributes' => array(
               'required' => 'required'
            ),
            'options' => array(
                    'label' => 'UserId'

            )
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'Zend\Form\Element\Password',             
            'attributes' => array(
                    'placeholder' => '**********',
                    'required' => 'required'
            ),
            'options' => array(
             'label' => 'Password',
            )
        ));
                
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Login',
                'id' => 'submitButton',
            ),
        ));  
                
                
   }
   
    public function getInputFilterSpecification() 
    {
       return array(
           'userId' => array(
               'required' => 'true',
               'validators' => array(
                   array(
                       'name' => 'NotEmpty',                       
                       'options' => array(
                           'message' => 'Can not be left blank'
                       )
                   )
               ),
               'filters' => array(
                        array('name'=>'StripTags'),
                        array('name'=>'StringTrim')                        
                        )
           ),
           
           
           'password' => array(
               'required' => 'true',
               'validators' => array(
                   array(
                       'name' => 'NotEmpty',                       
                       'options' => array(
                           'message' => 'Can not be left blank'
                       ),                 
                   )
               ),
               'filters' => array(
                        array('name'=>'StripTags'),
                        array('name'=>'StringTrim')                        
                        )
           )
           
       );
   }
}