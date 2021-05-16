<?php
/*
   	Tidepool Data app. index.php
	
	The index's task is to determine the requested class and
	instance it. 
	
    The url is of the form localhost/<project folder>/<classname>[/.../...]
	
    Additional params are placed in session var "params." 
    
    Class file and class names are capitalized and equal to each other.
    i.e.
    SomeClass.php holds the class SomeClass
    
    When processing the request uri this rule is enforced
    except that the input class name can have a lowercased first char and
    the processor will ucaseFirst it.
	
	1. Get the requested page/class from the request URI.
	2. Capitalize the first letter to match the class name and file name
	3. Require the classfile. Die if not present
	4. Instantiate the class. Die if not present.
	
	The default page is the home page.
*/
    session_start();
    
    define ('BASE','./'); 					            //App base folder
    define('CLASS_DIR',BASE.'classes/'); 	//Class File Location
    define('CLASS_EXT','.php'); 			      //Class File Extension

    
    error_reporting(E_ALL ^ E_STRICT ^ E_NOTICE);

    //Find the requested class name
    $path = $_SERVER['REQUEST_URI'];
	
    $pathParts = explode('/',$path);

    //Additional params passed?
    if(count($pathParts) > 3)
    {
        $params = array_slice($pathParts,3);
        $_SESSION['params'] = implode('|',$params);
    }
    else
    {
        $_SESSION['params'] = '';
    }

    $className = ucfirst($pathParts[2]);
	
    //If just the base given display the default page,,,
    if($className == '')
    {
       require 'assets/includes/config.php'; 
       $config = new Config();
	   $className = $config->defaultPage;
	   unset($config);
    }
    
    $classFile = CLASS_DIR.$className.CLASS_EXT; //Class file is named just like the class
    if(!file_exists($classFile))
    {
        echo "File $classFile not found...";
        die;
    }
    else
    {
       require $classFile;
    }

    if(class_exists($className))
    {
        $class = new $className();
    }
    else
    {
        echo "Unable to find class $className...";
        die;
    }