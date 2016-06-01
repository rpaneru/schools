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
        
        $container = new Container('userDetails');
        if( isset($container->refreshToken) )
        {
            $clientTokenPost = array(      
                "grant_type" => "refresh_token",
                "refresh_token" => $container->refreshToken,
                "client_id" => 'testclient',
                "client_secret" => 'testpass',
            );
            
            $curlReq = new \CurlRequest($this->apiPath());
            $authObj = $curlReq->getOauth2Token($clientTokenPost);
            
            $accessToken = $authObj->access_token;
            
            $baseUrlHelper = $sm->get('ViewHelperManager')->get('BaseUrl');
            $this->redirect()->toUrl( $baseUrlHelper().'/users/index/user-details?accessToken='.$accessToken );
        }

        $form =  $this-> serviceLocator->get('loginForm');
        
        $this->layout('layout/layout');
        
        return new ViewModel(array(
            'form' => $form
        ));
    }
    
    
    public function loginProcessAction()
    {     
        $sm = $this->getServiceLocator();
        
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
                
                $container = new Container('userDetails');
                $container->loginData = $paramArray;
                
                $baseUrlHelper = $sm->get('ViewHelperManager')->get('BaseUrl');
                //$rediretUrl = $this->apiPath().'oauth/authorize?response_type=code&client_id=testclient&redirect_uri='.$baseUrlHelper().'/users/index/request-oauth2-token&state=a';
                $rediretUrl = $baseUrlHelper().'/users/index/request-oauth2-token';
                $this->redirect()->toUrl( $rediretUrl );               
            }
        }
    }
    
    
    public function requestOauth2TokenAction()
    {
        $sm = $this->getServiceLocator();
        //$authorizationCode = $_REQUEST['code'];
        
        $baseUrlHelper = $sm->get('ViewHelperManager')->get('BaseUrl');
        
        $clientTokenPost = array(      
                        /*"grant_type" => "authorization_code",
                        "code" => $authorizationCode,
                        "redirect_uri" => $baseUrlHelper().'/users/index/request-oauth2-token',*/
                        "client_id" => 'testclient',
                        "client_secret" => 'testpass',
                        "grant_type"=> "password",
                        "username"=> "testuser",
                        "password"=> "testpass"
                    );

        $curlReq = new \CurlRequest($this->apiPath());
        $authObj = $curlReq->getOauth2Token($clientTokenPost);
        
        $accessToken = $authObj->access_token;
        $refreshToken = $authObj->refresh_token;
        
        $container = new Container('userDetails');
        $container->refreshToken = $refreshToken;
        
        $this->redirect()->toUrl( $baseUrlHelper().'/users/index/user-details?accessToken='.$accessToken );
    }
    
    
    public function userDetailsAction()
    {
        $sm = $this->getServiceLocator();
        $accessToken = $_REQUEST['accessToken'];
        
        $container = new Container('userDetails');       
        $queryString = 'login/'.urlencode( json_encode($container->loginData) );
                
        $curlReq = new \CurlRequest($this->apiPath());
        $userDetails = $curlReq->httpGet($queryString, $accessToken);
        $userDetails = json_decode($userDetails);
        
        if($userDetails == null)
        {
            echo 'Invalid Credentials';
        }
        return new ViewModel(array('userDetails'=>$userDetails));    
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