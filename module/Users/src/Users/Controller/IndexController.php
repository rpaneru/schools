<?php
namespace Users\Controller;
 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{    
    public function indexAction()
    {
        return new ViewModel(array());
    }
    
    public function loginAction()
    {       
        //echo $this->apiPath();
        
        $form =  $this-> serviceLocator->get('loginForm');
        
        return new ViewModel(array(
            'form' => $form
        ));
    }
    
}