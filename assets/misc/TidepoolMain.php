<?php
/*
    Tidepool project home page
    The page displays a form to gather the user's credentials,
    the optional desired date range, the data type - only smbg finger sticks are 
    handled and an optional user download file path.
    
    The normal mode of operation is to accept the data from the browser, run the authorization class
    to get a token and userid from Tidepool then request a set of test results. The results are then 
    passed to the PDF generation class to produce the report.
    
    If a file path is entered the code only calls for the report the report setting a
    variable in the passed data to suppress any other processing. The report uses the file
    path in the browser data sent down.
*/
    require 'Base.php';
    require 'classes/getTidepoolAuth.php';      //The authorization class   
    require 'classes/getMeterData.php';         //The meter data download class 
    require 'classes/tableReport.php';          //Report generating class Word/PDF from T.P. Data.
    require 'classes/pdftableReport.php';       //Report generating class ising Fpdf
    
class TidepoolMain extends Base{
    var $token;     //Authorization token
    var $userid;    //Authorization user
    var $json;      //Returned json string
    var $ajson;     //Returned data
    var $data;      //Form data from the browser
    var $fname = './assets/jsonfiles/tidepooldata.json'; //path to the saved json file 

    //Standard constructor
    public function __construct()
    {
        parent::__construct();
        
        $this->jaxon->processRequest();

        $this->completePageSetup();
    }
       
    //Finish the template vars and display the page
    public function completePageSetup()
    {
        $json = file_get_contents('./assets/includes/tidepoolConfig.json');
        $config = json_decode($json, true);
        $this->smarty->assign('email', $config['useremail']);
        $this->smarty->assign('password', $config['password']);
        $this->smarty->assign('title','Tidepool Data Reporting');
        $this->smarty->assign('brand','Tidepool Data Reporting');
        $this->smarty->display('TidepoolMain.tpl');
        
    }


    //Run the reports - called from the browser via Jaxon call
    //The data is the form data.
    function runReports($data)
    {
        $datalist = json_decode($data,true);    //to an assoc array  
        $this->data = $datalist;                //Ease of access to methods
        //If the uploadflag is false we're doing a normal Tidepool download
        //and need to call Tidepool
        if($this->data['uploadflag'] == 0)
        {
            $this->data['dateCheck'] = true;    //Require date check as we're using a file we downloaded.
            if(!$this->callTidepool())          //Authorize and get report data
            {
                return $this->resp;             //Return if anything went wrong                 
            }
        }
        else
        {
            $this->data['dateCheck'] = false ;  //No date check as we're using a file user uploaded.
            
        }    
        $this->displayDocument();               //Create and store the pdf of the returned values
        $this->resp->call('displayReport');     //Make a JS call in the browser to display the report 
        return $this->resp;
    }

    //Download from Tidepool
    function callTidepool()
    {
        if(!$this->getTidepoolAuthorization())  //Get a token and user id
        {
            return false;
        }
        if(!$this->getTidepoolData())           //Call for the user requested data
        {
            return false;
        }
        return true;
    }
    
    //Using the getTidepoolAuth class, 
    //Call for a token and userid from the Tidepool server 
    function getTidepoolAuthorization()
    {
        extract($this->data);
        try{
            $getAuth = new getTidepoolAuth($useremail, $password);
            $getAuth->fetchNewToken();          //Run the api call
            $this->token = $getAuth->readToken();     //Get the auth token
            $this->userid = $getAuth->readUserid();  //and the returned test user
        }catch(Exception $e){
            $this->resp->alert('Tidepool Authorization Failed: '.$e->getMessage().' at line '.$e->getLine());
            return false;
        }
        return true;
    }
    
    //Using, the getMeterData class, call for the Tidepool results
    function getTidepoolData()
    {
        extract($this->data);
        try{
            //Create the meterdata api class
            $getData = new getMeterData($this->token, $this->userid, $datatype, $startdate, $enddate);
            $getData->readMeterData();        //Make the api call
            $this->json = $getData->readBody();     //Get the json
            //Save the data to a file for the report writer
            file_put_contents($this->fname, $this->json);
        }catch (Exception $e){
            $this->resp->alert('Tidepool Data Capture Failed: '.$e->getMessage().' In File:'.$e->getFile().' On Line:'.$e->getLine());
            return false;
        }
        return true;
    }
    
    
    //Create a word document, save as pdf 
    function displayDocument()
    {
        
        $reporter = new pdftableReport($this->data);
        $reporter->report($this->fname);
    }
    
}