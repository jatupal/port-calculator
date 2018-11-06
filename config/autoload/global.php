<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
     'db' => array(
         'driver'         => 'Pdo',
         'dsn'            => 'mysql:dbname=port_calculator;host=localhost',
         //'dsn'            => 'mysql:dbname=ebay_system;host=localhost',
         'driver_options' => array(
             PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
         ),
         
         /*//other adapter when it needed...
         'adapters' => array(
         
             'db1' => array(
                 'driver'         => 'Pdo',
                 'dsn'             => 'mysql:dbname=zf2_staging;host=localhost',
                 'driver_options'  => array(
                     PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                 ),
             ),
             'db2' => array(
                 'driver'         => 'Pdo',
                 'dsn'             => 'mysql:dbname=zf2_test;host=localhost',
                 'driver_options'  => array(
                     PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                 ),
             ),
         
         ),*/
     ),
     'service_manager' => array(
         'factories' => array(
             'Zend\Db\Adapter\Adapter'
                     => 'Zend\Db\Adapter\AdapterServiceFactory',
         ),
         'abstract_factories' => array(
             'Zend\Db\Adapter\AdapterAbstractServiceFactory',
         ),
     ),
    
    'module_layouts' => array(
        'Application' => 'layout/layout.phtml',
        'Login' => 'layout/layout_login.phtml',
    ),
    
 );