<?php
/*
   	Tidepool  App. index.php
	
	The index's task is to determine the requested class and
	instance it. 
	
	The url is of the form localhost/tidepoolDataApiProject/classname[/.../...]
	
    Additional params are placed in session var "params." 
    
    Class file and class names are capitalized and equal to each other.
    i.e.
    SomeClass.php holds the class SomeClass
    
    When processing the request uri this rule is enforced
    except that the input class name can have a lowercased first char and
    the processor will ucFirst it.
	
	1. Get the requested page/class from the request URI.
	2. Capitalize the first letter to match the class name and file name
	3. Require the classfile. Die if not present
	4. Instantiate the class. Die if not present.
	
	The default page is the defaultpage in the config file.
*/
    session_start();
    
    define ('BASE','./'); 					//App base folder
    define('CLASS_DIR',BASE.'classes/'); 	//Class File Location
    define('CLASS_EXT','.php'); 			//Class File Extension

    error_reporting(E_ALL ^ E_STRICT ^ E_NOTICE);

    //Find the requested class name
    $path = $_SERVER['REQUEST_URI'];

    $pathParts = explode('/',$path);

    //Additional params passed?
    if(count($pathParts) > 3)
    {
        $pp = $pathParts;
        $params = array_slice($pp,3);
        $params = implode('/',$params);
        //var_dump($params);
        $_SESSION['params'] = $params;
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
   
    //Load the class file
    if(!file_exists($classFile))
    {
        echo "File $classFile not found...";
        die;
    }
    else
    {
       require $classFile;
    }

    //If the class exists - in the class fiile - instance it
    if(class_exists($className))
    {
        $class = new $className();  //Launch the requested
    }
    else
    {
        echo "Unable to find class $className...";
        die;
    }