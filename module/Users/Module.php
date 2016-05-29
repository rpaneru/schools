<?php
namespace Users;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
 
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Users\Form\LoginForm;

use Users\Model\OauthAuthorizationCodes;
use Users\Model\OauthAuthorizationCodesTable;

 class Module implements AutoloaderProviderInterface, ConfigProviderInterface
 {
     public function getAutoloaderConfig()
     {
         return array( 
             'Zend\Loader\ClassMapAutoloader' => array(
                 __DIR__ . '/autoload_classmap.php',
             ),
             'Zend\Loader\StandardAutoloader' => array(
                 'namespaces' => array(
                     __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                 ),
             ),
         );
     }

     public function getConfig()
     {
         return include __DIR__ . '/config/module.config.php';
     }
     
        
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'loginForm' => function($sm)
                {
                    $form = new LoginForm();
                    return $form;
                },
                'OauthAuthorizationCodesTableGateway' => function ($sm) 
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OauthAuthorizationCodes());
                    return new TableGateway('oauth_authorization_codes', $dbAdapter, null, $resultSetPrototype);
                },
                'Users\Model\OauthAuthorizationCodesTable' => function($sm)
                {
                    $tableGateway = $sm-> get('OauthAuthorizationCodesTableGateway');
                    $dbAdapter = $sm-> get('Zend\Db\Adapter\Adapter');
                    $table = new OauthAuthorizationCodesTable($dbAdapter,$tableGateway);                        
                    return $table;
                }
            )
        );
    }
 }