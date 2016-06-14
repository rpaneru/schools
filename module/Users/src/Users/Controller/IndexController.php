<?php
namespace Users\Controller;
 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Session\Container;

class IndexController extends AbstractActionController
{
    protected $OauthClientsTable;
    protected $OauthAuthorizationCodesTable;

    public function getOauthClientsTable()
    {
        if (!$this->OauthClientsTable) 
        {
            $sm = $this->getServiceLocator();
            $this->OauthClientsTable = $sm->get('Users\Model\OauthClientsTable');
        }
        return $this->OauthClientsTable;
    }
    
    
    public function getOauthAuthorizationCodesTable()
    {
        if (!$this->OauthAuthorizationCodesTable) 
        {
            $sm = $this->getServiceLocator();
            $this->OauthAuthorizationCodesTable = $sm->get('Users\Model\OauthAuthorizationCodesTable');
        }
        return $this->OauthAuthorizationCodesTable;
    }
    
    
    public function indexAction()
    {        
        return new ViewModel(array());
    }
    
    
    public function loginAction()
    {
        $sm = $this->getServiceLocator();
        $baseUrlHelper = $sm->get('ViewHelperManager')->get('BaseUrl');
        $container = new Container('userDetails'); 
        
        $message = $this->params()->fromQuery('message');
        if($message)
        {
            echo '<br /><br /><br /><br />'.$message;
        }
        
        if($container->offsetExists('accessToken'))
        {            
            $this->redirect()->toUrl( $baseUrlHelper().'/users/index/bypass-login');
        }
        
        $form =  $this-> serviceLocator->get('loginForm');
        
        $this->layout('layout/layout');
        
        return new ViewModel(array(
            'form' => $form
        ));
    }
    
    
    public function bypassLoginAction()
    {
        $sm = $this->getServiceLocator();
        $baseUrlHelper = $sm->get('ViewHelperManager')->get('BaseUrl');
        $container = new Container('userDetails'); 
        
        $queryString = "user-details/test";
        $curlReq = new \CurlRequest($this->apiPath());
        $userDetails = $curlReq->httpGet($queryString, $container->offsetGet('accessToken') );
        $userDetails = json_decode($userDetails);        
        
        
        $view = new ViewModel(array('userDetails'=>$userDetails)); 
        $view->setTemplate('users/index/login-process.phtml'); 
        return $view;
    }
    
    
    public function loginProcessAction()
    {     
        $sm = $this->getServiceLocator();
        $baseUrlHelper = $sm->get('ViewHelperManager')->get('BaseUrl');
        
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
                    'password'=>md5($formData->password)                    
                );
                
                $paramObject = (object)$paramArray;
                $secObj = new \Security();
                $newHash = $secObj->generateAndMatchHash($paramObject);
                $paramArray['hash'] = $newHash; 
        
                $clientTokenPost = array(                              
                    "client_id" => 'testclient',
                    "client_secret" => 'testpass',
                    "grant_type"=> "password",
                    "username"=> $formData->userId,
                    "password"=> $formData->password
                );

                $curlReq = new \CurlRequest($this->apiPath());
                $authObj = $curlReq->getOauth2Token($clientTokenPost);
  
                if( property_exists($authObj, 'status') )
                {
                    if( $authObj->status == 401)
                    {
                        $this->redirect()->toUrl( $baseUrlHelper().'/users/index/login?message='. urlencode($authObj->detail) );                    
                    }
                }
                
                $accessToken = $authObj->access_token;
                $refreshToken = $authObj->refresh_token;
                                
                $queryString = "user-details/".urlencode( json_encode($paramArray) );
                $curlReq = new \CurlRequest($this->apiPath());
                $userDetails = $curlReq->httpGet($queryString, $accessToken);
                $userDetails = json_decode($userDetails); 

                $container = new Container('userDetails');
                $container->accessToken = $accessToken;
                $container->refreshToken = $refreshToken;
                $container->roleId = $userDetails->roleId;
                
                return new ViewModel(array('userDetails'=>$userDetails));        
            }
        }       
    }
    
    
    public function logoutAction()
    {
        $sm = $this->getServiceLocator();
        
        $container = new Container('userDetails');
        $container->getManager()->getStorage()->clear('accessToken'); 
        $container->getManager()->getStorage()->clear('refreshToken'); 
        $container->getManager()->getStorage()->clear('roleId'); 
        $container->getManager()->destroy();
        
        $baseUrlHelper = $sm->get('ViewHelperManager')->get('BaseUrl');
        $this->redirect()->toUrl( $baseUrlHelper().'/users/index/login?message='. urlencode("You are successfully logged out.") );
    }
}