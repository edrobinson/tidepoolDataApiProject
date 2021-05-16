<?php
/*
    Tidepool Date API project home page.
    
    The page displays a form to gather the user's credentials,
    the optional desired date range, the data type - only smbg finger sticks are 
    handled and an optional user download file path.
    
    The normal mode of operation is to accept the data from the browser, run the authorization class
    to get a token and userid from Tidepool then request a set of test results. The results are then 
    passed to the PDF generation class to produce the report.
    
    The user has the option to upload a data file downloaded from Tidepool.
    If a file has been downloaded, a flag is set in the form data and only the
    report is processed. Otherwise we call for the authorization and data before
    generating the report.
    
    This is version 2. It uses the Requests classes based tidepool api class.
*/
    require 'Base.php';                         //The common base class
    require 'classes/pdftableReport.php';       //Report generating class ising Fpdf
    require 'classes/tidepoolAPI.php';          //Simplified API class
    
class TidepoolMainV2 extends Base{
    
    var $token;     //Authorization token
    var $userid;    //Authorization user
    var $json;      //Returned json string
    var $ajson;     //Returned decoded json data array
    var $data;      //Form data json array from the browser
    var $fname = './assets/jsonfiles/tidepooldata.json'; //path to the saved json file 

    public function __construct()
    {
        parent::__construct();
        
        //If this is an ajax call from the browser, process it.
        if ($this->jaxon->canProcessRequest())
        {
            $this->jaxon->processRequest();
        }
        //Otherwise complete the Smarty settings and display the page.
        //This only happens once
        else
        {
            $this->completePageSetup();
        }
    }
       
    //Finish the template vars and display the page
    public function completePageSetup()
    {
        //Load config file and decode it.
        $json = file_get_contents('./assets/includes/tidepoolConfig.json');
        $config = json_decode($json, true);
        
        //Do the Smarty assigns and display the page
        $this->smarty->assign('email', $config['useremail']);
        $this->smarty->assign('password', $config['password']);
        $this->smarty->assign('title','Tidepool Data Reporting');
        $this->smarty->assign('brand','Tidepool Data Reporting');
        $this->smarty->display('TidepoolMain.tpl');
        
    }
//------------------------- End of setup --- Start of processing -----------------------------

    //This function is called from the browser via a Jaxon PHP call.
    //The data is the form data from the browser as a json string.
    function runReports($data)
    {
        //Decode the browser json string into the data var.
        $this->data = json_decode($data,true);
              
        //The upload flag is true if we are processing a user upload.
        //If not, we need to call Tidepool for authorization and data download.
        if($this->data['uploadflag'] == 0)
        {
            if(!$this->callTidepool())          //Authorize and get report data
            {
                return $this->resp;             //Reply to the browser if anything went wrong             
            }
        }
        
        $this->displayDocument();               //Create and store the pdf of the returned values
        $this->resp->call('displayReport');     //Make a JS call in the browser to display the report 
        return $this->resp;                     //Return the Jaxon response obj.
    }

    //Get Authorization and Download from Tidepool
    function callTidepool()
    {
        extract($this->data);                   //Extract the browser data to local scope
        $tpAPI = new tidepoolAPI();             //Instance our api class
        
        //1. Call for Tidepool credentials
        try{
            $tpAPI->getTidepoolAuthorization($useremail, $password);
        }catch (Exception $e){
            $this->resp->alert('Tidepool Authorization Failed: '.$e->getMessage().' at line '.$e->getLine());
            return false;
        }
        //2. Call for Tidepool data
        try{
            $tpAPI->getTidepoolData($startdate, $enddate, $datatype);
        }catch(Exception $e){
            $this->resp->alert('Tidepool data download failed: '.$e->getMessage().' at line '.$e->getLine());
            return false;
        }
        return true;
    }
    
    //Create a PDF of the data and save it to a file.
    //The response to the browser will include a js call
    //to display the pdf.
    function displayDocument()
    {
        $reporter = new pdftableReport($this->data);
        $reporter->report($this->fname);
    }
    
}