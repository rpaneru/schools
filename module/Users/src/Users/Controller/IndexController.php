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
        
        $form =  $this-> serviceLocator->get('loginForm');
        
        $this->layout('layout/layout');
        
        return new ViewModel(array(
            'form' => $form
        ));
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
                
                if( $authObj->status == 401)
                {
                    $this->redirect()->toUrl( $baseUrlHelper().'/users/index/login' );
                }
                
                $accessToken = $authObj->access_token;
                $refreshToken = $authObj->refresh_token;
                                
                $queryString = "login/".urlencode( json_encode($paramArray) );
                $curlReq = new \CurlRequest($this->apiPath());
                $userDetails = $curlReq->httpGet($queryString, $accessToken);
                $userDetails = json_decode($userDetails);                 
                
                $container = new Container('userDetails');
                $container->accessToken = $accessToken;
                $container->refreshToken = $refreshToken;
        
                return new ViewModel(array('userDetails'=>$userDetails));        
            }
        }       
    }

    
    public function logoutAction()
    {
        $sm = $this->getServiceLocator();
        
        $container = new Container('userDetails');
        $container->getManager()->getStorage()->clear();
        
        $baseUrlHelper = $sm->get('ViewHelperManager')->get('BaseUrl');
        $this->redirect()->toUrl( $baseUrlHelper().'/users/index/login' );
    }
}