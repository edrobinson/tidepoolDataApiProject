<?php
/*
    5/16/2017
   Base class for my projects
   Provides instances of jaxon php, ezsql and smarty templating
   and a dispatch function to direct the Jaxon calls
   from the browser.
   
   Note: All of the page templates use Bootstrap styling.
*/  
      use Jaxon\Jaxon;
      use Jaxon\Response\Response;
      require "assets/vendor/autoload.php";

abstract class Base{
	public $db;				        //ezsql db class instance
	public $smarty;			        //Smarty instance
	public $jaxon;			        //jaxon instance
	public $resp;   		        //jaxon response object
    public $config;                 //App.configuration
    public $className;              //Classname from uri
    public $utilities;              //Utility class

	public function __construct()
	{
        if(!class_exists('Config'))
		require 'assets/includes/config.php';
        $this->config = new Config();
        $this->jaxonSetup();    //Setup the ajax handling framework
	    //$this->dbSetup();       //Setup the mysql framework - Not used in Tidepool project
        //Setup smarty only at page load (canProcessRequest will return false then)
		if( ! $this->jaxon->canProcessRequest())
		{
            $this->smartySetup();
		}
	}
    
    //Instance the Jaxon class and Jaxon Response class.
    //Register our dispatcher function which is the
    //single point of call from te browser.
    private function jaxonSetup()
    {
        $this->jaxon = new Jaxon();
        $this->resp = new Response(); 
        $this->jaxon->register(Jaxon::USER_FUNCTION, array($this,"dispatch"));
     }
    
    //This function instances the EzSql database objects
    private function dbSetup()
    {
       include('assets/includes/dbsettings.php');
       $this->db = new ezSQL_mysqli($dbuser, $dbpassword, $dbdatabase, $dbhost);
    }
    
    //Smarty templating engine setup
    //Also does app. global assigns
    public function smartySetup()
    {
        //Instance the SMarty template management class
        $this->smarty = new Smarty();
        //Configure the directories
        $this->smarty->setTemplateDir('assets/smarty-templates');               //Raw template files
        $this->smarty->setCompileDir('assets/libs/smarty/templates_c');         //Compiled template files
        $this->smarty->setCacheDir('assets/libs/smarty/cache');                 //Cached files
        $this->smarty->setConfigDir('assets/libs/smarty/configs');              //Configurations    

        //Assign global/common tag values
        $this->smarty->assign('year',date('Y'));                                 //Date year for footers
        $this->smarty->assign('footertext',$this->config->footerText);           //Copyright text for footers 
        
        //Generate the Jaxon client code
        $this->smarty->assign('jaxonjs',$this->jaxon->getJs());                    
        $this->smarty->assign('jaxoncss',$this->jaxon->getCss());                  
        $this->smarty->assign('jaxonscript',$this->jaxon->getScript( ));
       
       //Determine this pages help file name.
        //The "standard" is the help/pagenameHelp.html
        $parts = pathinfo($_SERVER['REQUEST_URI']);                                 //Parent script
        $helpfile = $parts['basename'];                                             //parent.php
        $helpfile .= 'Help.html';                                                   //parentHelp.html
        $helpfile = 'help/'.$helpfile;                                              //help/parentHelp/.html - the help file path
        $this->smarty->assign('help',$helpfile);
       
        //Set the nav brand and title tag values - same for both
        $brand = $this->config->brands[ucfirst($parts['basename'])];
        if($brand == '')
        {
            $brand = $this->config->brands['default'];
        }
        $brand = ucfirst($brand);
        $this->smarty->assign('brand',$brand);
        $this->smarty->assign('title',$brand);
    }
    
	/*
		The dispatch method is the only jaxon registered method.
		It is called from the client passing the method to invoke and
		any parameters to be pased to the method.

		We get the arguments and strip off the first as the method to call.
		Use the reflectionmethod class to see if the method is available. 
		If so, call it passing remaining arguments. If not, complain.
	*/
	public function dispatch($func, $data='')
	{
		$aArgs 		= func_get_args();
		$sMethod 	= array_shift($aArgs); //The first argument is the method name.
		try
		{
			$ref = new ReflectionMethod($this, $sMethod); 		//Method defined?
		}
		catch(Exception $e)
		{
			return $this->resp->alert("Error: Method $sMethod is not available."); //Oops...
		}
		
        //Call  the requested method returning it's response to the browser
		return call_user_func_array(array(&$this,$sMethod), $aArgs); //Dispatch the called method passing the arguments
		
	}
}
?>