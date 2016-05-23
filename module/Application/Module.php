<?php
namespace Application;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface; 

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ViewHelperProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
        $controller = $e->getTarget();
        $controllerClass = get_class($controller);
        $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        $config = $e->getApplication()->getServiceManager()->get('config');
        if (isset($config['module_layouts'][$moduleNamespace])) 
        {
            $controller->layout($config['module_layouts'][$moduleNamespace]);
        }
        }, 100);
        
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),            
        );
    }
    
    public function getViewHelperConfig()
    {
        return array(
          'factories' => array(
                'currentRequest' => function ($sm) {                   
                    $viewHelper = new View\Helper\CurrentRequest();
                    return $viewHelper;
                },
                'baseUrl' => function ($sm) {
                    $viewHelper = new View\Helper\BaseUrl();
                    return $viewHelper;
                },
                'currentUrl' => function ($sm) {
                    $viewHelper = new View\Helper\CurrentUrl();
                    return $viewHelper;
                },
                'dateFormatIndia' => function ($sm) {
                    $viewHelper = new View\Helper\DateFormatIndia();
                    return $viewHelper;
                }
            )
        );
   }

}
