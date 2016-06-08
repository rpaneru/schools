<?php
namespace Users;

return array(
     'controllers' => array(
         'invokables' => array(
             'Users\Controller\Index' => 'Users\Controller\IndexController',
         ),
     ),
    
    'router' => array(
        'routes' => array(
            
            'users' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/users',
                    'defaults' => array(                        
                        '__NAMESPACE__' => 'Users\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        )
    ),
    
     'view_manager' => array(
         'template_path_stack' => array(
             'users' => __DIR__ . '/../view',
         ),
     ),
 );