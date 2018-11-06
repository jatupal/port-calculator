<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Member\Controller\Member' => 'Member\Controller\MemberController',
			'Member\Controller\User' => 'Member\Controller\UserController',
			'Member\Controller\Role' => 'Member\Controller\RoleController',
			'Member\Controller\RoleGroup' => 'Member\Controller\RoleGroupController',
			'Member\Controller\UserGroup' => 'Member\Controller\UserGroupController',
			'Member\Controller\Company' => 'Member\Controller\CompanyController',
        ),
    ),
/*
	'router' => array(
        'routes' => array(
            'member' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/member',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Member\Controller',
                        'controller'    => 'Member',
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
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
			'user' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/user',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Member\Controller',
                        'controller'    => 'User',
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
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
			'role' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/role',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Member\Controller',
                        'controller'    => 'Role',
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
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
		),
    ),
*/
	'router' => array(
         'routes' => array(
             'user' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/user[/:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
						),
                     'defaults' => array(
                         'controller' => 'Member\Controller\User',
                         'action'     => 'index',
						),
					),
				),
				 'role' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/role[/:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Member\Controller\Role',
                         'action'     => 'index',
						),
					),
				),
				 'member' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/member[/:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Member\Controller\Member',
                         'action'     => 'index',
                     ),
                 ),
			 ),
			 'company' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/company[/:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Member\Controller\Company',
                         'action'     => 'index',
                     ),
                 ),
			 ),
			 'user-group' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/user-group[/:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Member\Controller\UserGroup',
                         'action'     => 'index',
                     ),
                 ),
			 ),
			 'role-group' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/role-group[/:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Member\Controller\RoleGroup',
                         'action'     => 'index',
                     ),
                 ),
			 ),
         ),
    ),

	'view_manager' => array(
        'template_path_stack' => array(
            'Member' => __DIR__ . '/../view',
        ),
    ),
);