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
                    'password'=>md5($formData->password),
                    'profileTypeId'=>"2"
                );
                
                $paramObject = (object)$paramArray;
                $secObj = new \Security();
                $newHash = $secObj->generateAndMatchHash($paramObject); 
               
                $paramArray['hash'] = $newHash;
                
                $session = new \Zend\Session\Container('userDetails');
                var_dump($session->refreshToken);
                
                if( !isset($session->refreshToken) )
                {
                    ///////////////////////////////////////////////////////////////////////////////////////////////

                    //http://schools-api/oauth/authorize?response_type=code&client_id=testclient&redirect_uri=http://schools-api/oauth/receivecode&state=a
                    $oauthAuthorizationCodesTable = $sm->get('Users\Model\OauthAuthorizationCodesTable');        
                    $oauthAuthorizationCodesData = $oauthAuthorizationCodesTable->getOauthAuthorizationCodes( array('client_id'=>'testclient') );

                    $clientTokenPost = array(      
                        "grant_type" => "authorization_code",
                        "code" => $oauthAuthorizationCodesData["authorization_code"],
                        "redirect_uri" => $this->apiPath()."oauth/receivecode",
                        "client_id" => 'testclient',
                        "client_secret" => 'testpass',
                    );

                    $curlReq = new \CurlRequest($this->apiPath());
                    $authObj = $curlReq->getOauth2Token($clientTokenPost);
                    
                    $accessToken = $authObj->access_token;                
                    $session->refreshToken = $authObj->refresh_token;
                }                
                else
                {
                    $clientTokenPost = array(      
                        "grant_type" => "refresh_token ",
                        "refresh_token" => $session->refreshToken,
                        //"redirect_uri" => $this->apiPath()."oauth/receivecode",
                        "client_id" => 'testclient',
                        "client_secret" => 'testpass',
                    );

                    $curlReq = new \CurlRequest($this->apiPath());
                    $authObj = $curlReq->getOauth2Token($clientTokenPost);
                    $session->refreshToken = $authObj->refresh_token;
                    
                    echo 'bbbb';
                    var_dump($session->refreshToken);
                }
                
                die;
                ///////////////////////////////////////////////////////////////////////////////////////////////
                        
                $queryString = 'login/'.urlencode( json_encode($paramArray) );
                
                $userDetails = $curlReq->httpGet($queryString, $accessToken);
                $userDetails = json_decode($userDetails);

                if($userDetails == null)
                {
                    echo 'Invalide credentials';
                }
                if($userDetails->status == 403)
                {
                    echo 'Access Forbidden';
                }               
                else
                {
                    return new ViewModel(array('userDetails'=>$userDetails));    
                }           
            }
        }
    }
    
}