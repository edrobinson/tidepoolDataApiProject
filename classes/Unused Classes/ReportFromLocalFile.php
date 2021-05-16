<?php
/*
    Tidepool project generate report from a dsownloaded json file
*/
    require 'Base.php';
    require 'classes/tableReport.php';  
      
      
class ReportFromLocalFile extends Base{
    var $data;
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
        $this->smarty->assign('title','Tidedepool Report From File');
        $this->smarty->assign('brand','Tidepool Report Ffrom File');
        $this->smarty->display('reportFromLocalFile.tpl');
        
    }


    //Run the reports  
    function runReports($data)
    {
        $datalist = json_decode($data,true); //to an assoc array
        $file = trim($datalist['userfile'], '"');
            
        $json = file_get_contents($file);
        
        file_put_contents("assets/jsonfiles/tidepooldata.json", $json);
        
        

        $this->data = $datalist;        //Ease of access to methods
        $this->displayDocument();
        $this->resp->call('displayReport');
        return $this->resp;
    }

    
    
    
    //Create a word document, save as pdf 
    function displayDocument()
    {
        $reporter = new tableReport($this->data);
        $reporter->report();
    }
    
      


}