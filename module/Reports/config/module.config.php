<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Reports\Controller\Reports' => 'Reports\Controller\ReportsController',
        ),
    ),
    'router' => array(
        'routes' => array(
			'reports' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/reports',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Reports\Controller',
                        'controller'    => 'Reports',
                        'action'        => 'index',
                    ),
                    
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // This route is a sane default when developing a module;
                    // as you solidify the routes for your module, however,
                    // you may want to remove it and replace it with more
                    // specific routes.
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action[/:id]][/page/:page][/per_page/:per_page][/clear_sec/:clear_sec]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),

                    'paginator' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:controller[/:action][/page/:page][/per_page/:per_page][/clear_sec/:clear_sec]]',
                            'defaults' => array(
                                'page' => 1,
                            ),
                        ),
                    ),
                ),
            ),
            
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Reports' => __DIR__ . '/../view',
        ),
    ),
);
