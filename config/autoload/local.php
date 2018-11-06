<?php
/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */

return array(
	'db' => array(
        'username' => 'root',
        'password' => 'bookdee@1234',
	    
	    
	    //'username' => 'autodesk',
	    //'password' => 'gxHo9jv',
	    
	    
	    /*// to allow other adapter to be called by
	    // $sm->get('db1') or $sm->get('db2') based on the adapters config.
	    'adapters' => array(
	        'db1' => array(
	            'username' => 'root',
	            'password' => '',
	        ),
	        'db2' => array(
	            'username' => 'other_user',
	            'password' => 'other_user_passwd',
	        ),
	    ),*/
     ),
);
