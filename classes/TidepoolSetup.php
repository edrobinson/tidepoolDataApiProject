<?php
/*
    Tidepool project setup class
*/
    require 'Base.php';
    require 'classes/getTidepoolAuth.php'; //The authorization class   
    require 'classes/getMeterData.php';    //The meter data download class 
    require 'classes/tableReport.php';  
      
      
class TidepoolSetup extends Base{

    //Standard constructor
    public function __construct()
    {
        parent::__construct();
        
        //If this is an ajax call from the browser, process it.
        if ($this->jaxon->canProcessRequest())
        {
            $this->jaxon->processRequest();
        }
        //Otherwise complete the Smarty settings and display the page.
        else
        {
            $this->completePageSetup();
        }
    }
       
    //Finish the template vars and display the page
    public function completePageSetup()
    {
        //Fill in the current values in the template if defined
        if(file_exists('./assets/includes/tidepoolConfig.json'))
        {
            $json = file_get_contents('./assets/includes/tidepoolConfig.json');
            $config = json_decode($json, true);
            $this->smarty->assign('useremail', $config['useremail']);
            $this->smarty->assign('password',  $config['password']);
            $this->smarty->assign('username',  $config['username']);
            $this->smarty->assign('birthdate', $config['birthdate']);
        }
            
        $this->smarty->assign('title','Tidepool Data Reporting Setup');
        $this->smarty->assign('brand','Tidepool Data Reporting Setup');
        $this->smarty->display('TidepoolSetup.tpl');
        
    }


    //Write the config
    function processForm($data)
    {
        //$config = json_decode($data,true);
        file_put_contents('./assets/includes/tidepoolConfig.json', $data);
        $this->resp->alert('The settings have been updated.');
        return $this->resp;
    }
}