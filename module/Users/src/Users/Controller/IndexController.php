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
        
        $request = $this->getRequest();
        if ($request->isPost()) 
        {
            $loginForm = new \Users\Form\LoginForm();
            $form->setInputFilter($loginForm->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) 
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
                
                $url = $this->apiPath()."login/".urlencode( json_encode($paramArray) );
                
                $curlReq = new \CurlRequest();
                $userDetails = $curlReq->httpGet($url);
                $userDetails = json_decode($userDetails);
                var_dump($userDetails);
                
                die;
                
            }
        }
        
        return new ViewModel(array(
            'form' => $form
        ));
    }
    
}