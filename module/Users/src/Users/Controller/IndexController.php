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
        $form =  $this-> serviceLocator->get('loginForm');
        
        $this->layout('layout/layout');
        
        return new ViewModel(array(
            'form' => $form
        ));
    }
    
    
    public function loginProcessAction()
    {     
        $request = $this->getRequest();
        if ($request->isPost()) 
        {
            
            $loginForm = new \Users\Form\LoginForm();
            
            $loginForm->setInputFilter($loginForm->getInputFilter());

            $loginForm->setData($request->getPost());

            if ($loginForm->isValid()) 
            {    
                $formData = $request-> getPost();

                $paramArray = array(
                    'userId'=>$formData->userId,
                    'password'=>md5($formData->password),
                    'profileTypeId'=>"2"
                );
                
                $paramObject = (object)$paramArray;
                $secObj = new \Security();
                $newHash = $secObj->generateAndMatchHash($paramObject); 
               
                $paramArray['hash'] = $newHash;
                
                echo json_encode($paramArray);
                echo '<br />';
                echo urlencode( json_encode($paramArray) );
                echo '<br />';
                echo $url = $this->apiPath()."login/".urlencode( json_encode($paramArray) );
                die;
                $curlReq = new \CurlRequest();
                $userDetails = $curlReq->httpGet($url);
                $userDetails = json_decode($userDetails);
                return $userDetails;
                
            }
        }

        return new ViewModel();
    }
    
}